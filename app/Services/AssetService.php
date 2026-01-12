<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AssetHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;

class AssetService
{
    /**
     * Generate Serial Number Otomatis (INV-YYYYMM-0001)
     */
    public function generateSerialNumber(): string
    {
        $lastAsset = Asset::latest('id')->first();
        $nextId = $lastAsset ? ($lastAsset->id + 1) : 1;
        return 'INV-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Handle Image Upload & Storage
     * Menghapus image lama, simpan yang baru
     */
    public function handleImageUploads(Asset $asset, array $validatedData, array $requestFiles = []): array
    {
        foreach (['image', 'image2', 'image3'] as $key) {
            if (isset($requestFiles[$key])) {
                // Hapus gambar lama jika ada
                if ($asset->$key) {
                    Storage::disk('public')->delete($asset->$key);
                }
                // Simpan yang baru
                $validatedData[$key] = $requestFiles[$key]->store('asset-images', 'public');
            }
        }

        return $validatedData;
    }

    /**
     * Split Stock (Pecah Aset)
     * Logic: Stok awal dipecah, satu bagian ke user, sisa di gudang
     * 
     * Contoh:
     *   - Aset A punya stok 100
     *   - Admin: ambil 30 untuk User X (dikeploykan)
     *   - Hasil: Aset A stok 70 (available), Aset A-S123 stok 30 (deployed ke User X)
     */
    public function splitStock(
        Asset $asset,
        int $quantityToSplit,
        int $targetUserId,
        ?string $assignedDate = null,
        ?string $returnDate = null,
        int $adminId = 0
    ): Asset {
        // Guard: cek stok cukup
        if ($quantityToSplit >= $asset->quantity) {
            throw new \Exception("Quantity to split harus kurang dari stok saat ini ({$asset->quantity})");
        }

        $stokAwal = $asset->quantity;
        $sisaStok = $stokAwal - $quantityToSplit;

        // 1. Update aset induk: kurangi stok, set available
        $asset->update([
            'quantity' => $sisaStok,
            'user_id' => null,
            'status' => 'available',
            'assigned_date' => null,
            'return_date' => null,
        ]);

        // Catat history untuk induk
        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => $adminId,
            'action' => 'split_stock',
            'notes' => "Stok dipecah: {$stokAwal} → {$sisaStok}. ({$quantityToSplit} unit dipisah untuk user lain)"
        ]);

        // 2. Buat aset baru hasil pecahan
        $newAsset = $asset->replicate();
        $baseSN = $asset->serial_number;
        $newAsset->serial_number = $baseSN . '-S' . rand(100, 999);
        $newAsset->quantity = $quantityToSplit;
        $newAsset->user_id = $targetUserId;
        $newAsset->status = 'deployed';
        $newAsset->assigned_date = $assignedDate ? \Carbon\Carbon::parse($assignedDate)->format('Y-m-d H:i:s') : now();
        $newAsset->return_date = $returnDate ? \Carbon\Carbon::parse($returnDate)->format('Y-m-d H:i:s') : null;
        
        // Jangan duplikat gambar (set null)
        $newAsset->image = null;
        $newAsset->image2 = null;
        $newAsset->image3 = null;
        
        $newAsset->save();

        // Catat history untuk aset baru
        AssetHistory::create([
            'asset_id' => $newAsset->id,
            'user_id' => $adminId,
            'action' => 'manual_override',
            'notes' => "Aset hasil pecahan (Split) dari induk ID {$asset->id}. Dipinjamkan ke user ID: {$targetUserId}"
        ]);

        return $newAsset;
    }

    /**
     * Create Asset Request (Pengajuan Peminjaman)
     * Digunakan oleh Karyawan untuk request aset
     * 
     * Validasi:
     *   - Jika booking: aset harus status 'deployed' (lagi dipinjam orang)
     *   - Jika pinjam biasa: stok harus cukup & status 'available'
     */
    public function createAssetRequest(
        int $userId,
        int $assetId,
        int $quantity,
        ?string $returnDate = null,
        ?string $returnTime = null,
        string $reason = '',
        bool $isBooking = false
    ): AssetRequest {
        $asset = Asset::findOrFail($assetId);

        // Cek duplikat request aktif
        $existingReq = AssetRequest::where('user_id', $userId)
            ->where('asset_id', $assetId)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existingReq) {
            throw new \Exception('Anda sudah memiliki permintaan aktif atau antrian untuk aset ini.');
        }

        // Validasi stok & status sesuai tipe request
        if ($isBooking) {
            // Booking: aset harus lagi dipinjam
            if ($asset->status !== 'deployed') {
                throw new \Exception('Booking hanya bisa dilakukan jika aset sedang dipinjam orang lain.');
            }
        } else {
            // Pinjam biasa: harus available & stok cukup
            if ($asset->quantity < $quantity) {
                throw new \Exception("Gagal! Stok tidak mencukupi. Tersisa {$asset->quantity} unit.");
            }
            if ($asset->status !== 'available') {
                throw new \Exception("Gagal! Aset sedang tidak tersedia (Status: {$asset->status}).");
            }
        }

        // Format return date
        $fullReturnDate = null;
        if (!empty($returnDate)) {
            $time = $returnTime ?? '17:00:00';
            $fullReturnDate = $returnDate . ' ' . $time;
        }

        // Create request
        return AssetRequest::create([
            'user_id' => $userId,
            'asset_id' => $assetId,
            'quantity' => $quantity,
            'request_date' => now(),
            'return_date' => $fullReturnDate,
            'reason' => $reason,
            'status' => 'pending'
        ]);
    }

    /**
     * Approve Asset Request (Admin menyetujui)
     * - Kurangi stok sesuai quantity yang di-request
     * - Update status asset ke deployed jika stok habis, atau tetap available jika masih ada sisa
     */
    public function approveAssetRequest(AssetRequest $assetRequest): void
    {
        $asset = $assetRequest->asset;

        // Safety check: stok masih cukup?
        if ($asset->quantity < $assetRequest->quantity) {
            throw new \Exception('Gagal! Stok barang ini sudah habis atau tidak cukup.');
        }

        // Kurangi stok
        $asset->decrement('quantity', $assetRequest->quantity);

        // Update status aset
        if ($asset->quantity == 0) {
            // Stok habis: status deployed, set user & tanggal
            $asset->update([
                'status' => 'deployed',
                'user_id' => $assetRequest->user_id,
                'assigned_date' => now(),
                'return_date' => $assetRequest->return_date
            ]);
        } else {
            // Masih ada sisa: tetap available (bulk stock)
            $asset->update(['status' => 'available']);
        }

        // Update request status
        $assetRequest->update(['status' => 'approved']);

        // Catat history
        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'approved',
            'notes' => "Disetujui untuk: {$assetRequest->user->name}. Mengurangi stok sebanyak {$assetRequest->quantity}."
        ]);
    }

    /**
     * Reject Asset Request (Admin menolak)
     */
    public function rejectAssetRequest(AssetRequest $assetRequest, string $adminNote): void
    {
        $assetRequest->update([
            'status' => 'rejected',
            'admin_note' => $adminNote
        ]);

        AssetHistory::create([
            'asset_id' => $assetRequest->asset_id,
            'user_id' => auth()->id(),
            'action' => 'rejected',
            'notes' => "Ditolak: {$adminNote}"
        ]);
    }

    /**
     * Build Asset Query dengan Filter
     * Centralize search, status filter, dan sorting logic
     */
    public function buildAssetQuery(array $filters = [])
    {
        $query = Asset::with(['holder', 'latestApprovedRequest']);

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        // Sorting (by name, stock, date, atau STATUS)
        $sort = $filters['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'stock_low':
                $query->orderBy('quantity', 'asc');
                break;
            case 'stock_high':
                $query->orderBy('quantity', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'status_available': // Sort by Available first
                $query->orderByRaw("FIELD(status, 'available', 'deployed', 'maintenance', 'broken')")
                      ->latest();
                break;
            case 'status_deployed': // Sort by Deployed first
                $query->orderByRaw("FIELD(status, 'deployed', 'available', 'maintenance', 'broken')")
                      ->latest();
                break;
            case 'status_maintenance': // Sort by Maintenance first
                $query->orderByRaw("FIELD(status, 'maintenance', 'broken', 'available', 'deployed')")
                      ->latest();
                break;
            case 'status_broken': // Sort by Broken first
                $query->orderByRaw("FIELD(status, 'broken', 'maintenance', 'available', 'deployed')")
                      ->latest();
                break;
            default:
                $query->latest();
        }

        return $query;
    }

    /**
     * Convert File to Base64 Data URL
     * Utility untuk embed images langsung ke PDF
     */
    public function fileToBase64(string $filePath): string
    {
        if (!file_exists($filePath)) {
            return ''; // Return empty jika file tidak ada
        }

        try {
            $fileContent = file_get_contents($filePath);
            $base64 = base64_encode($fileContent);
            
            // Determine MIME type berdasarkan extension
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml'
            ];
            
            $mimeType = $mimeTypes[$ext] ?? mime_content_type($filePath) ?? 'image/png';
            
            return "data:{$mimeType};base64,{$base64}";
        } catch (\Exception $e) {
            // Fallback: return empty string jika error
            return '';
        }
    }

    /**
     * Generate QR Code sebagai Data URL (SVG base64)
     * Ini compatible dengan DomPDF untuk embedded images
     */
    public function generateQrCodeDataUrl(Asset $asset): string
    {
        // URL detail aset yang akan di-encode di QR code
        $detailUrl = route('assets.scan', ['id' => $asset->id]);
        
        try {
            // Generate QR code menggunakan bacon/bacon-qr-code
            $renderer = new ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                new SvgImageBackEnd()
            );
            
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($detailUrl);
            
            // Convert SVG ke base64 data URL
            $base64 = base64_encode($qrCodeSvg);
            return 'data:image/svg+xml;base64,' . $base64;
        } catch (\Exception $e) {
            // Fallback ke API jika local generation gagal
            $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($detailUrl);
            return $qrCodeUrl;
        }
    }

}
