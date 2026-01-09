<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetReturn;
use App\Models\AssetHistory;
use Illuminate\Http\Request;

class AssetReturnController extends Controller
{
    /**
     * [USER] Mengajukan Pengembalian Aset
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'return_date' => 'required|date',
            'condition' => 'required|in:baik,rusak,perlu_service',
            'reason' => 'required|string|max:255',
        ]);

        // 2. Cek apakah aset memang dipegang user ini
        $asset = Asset::where('id', $request->asset_id)
                      ->where('user_id', auth()->id())
                      ->firstOrFail();

        // 3. Cek apakah sudah ada request pending (biar ga double)
        $existingRequest = AssetReturn::where('asset_id', $asset->id)
                                      ->where('status', 'pending')
                                      ->first();
        
        if($existingRequest) {
            return back()->with('error', 'Anda sudah mengajukan pengembalian untuk aset ini. Harap tunggu konfirmasi admin.');
        }

        // 4. Buat Record Pengembalian
        AssetReturn::create([
            'user_id' => auth()->id(),
            'asset_id' => $asset->id,
            'return_date' => $validated['return_date'],
            'condition' => $validated['condition'],
            'reason' => $validated['reason'],
            'status' => 'pending'
        ]);

        // Opsional: Ubah status aset sementara agar tidak bisa dipinjam orang lain dulu?
        // Untuk sekarang biarkan status 'deployed' tapi kita tahu ada request return.

        return back()->with('success', 'Permintaan pengembalian berhasil dikirim. Admin akan mengecek barangnya.');
    }

    /**
     * [ADMIN] Menyetujui Pengembalian (Barang kembali ke Gudang)
     */
    public function approve($id)
    {
        $returnRequest = AssetReturn::findOrFail($id);
        $asset = Asset::findOrFail($returnRequest->asset_id);

        // 1. Update Asset (Kembalikan ke Gudang)
        $asset->update([
            'user_id' => null,           // Copot kepemilikan user
            'status' => ($returnRequest->condition == 'baik') ? 'available' : 'maintenance', // Jika rusak, masuk maintenance
            'assigned_date' => null,
            'return_date' => null,
            'condition_notes' => $returnRequest->reason // Update kondisi terakhir
        ]);

        // 2. Tambah Stok (Penting!)
        // Asumsi: Saat dipinjam stok berkurang, saat kembali stok nambah.
        $asset->increment('quantity', 1); 
        // Note: Jika sistem Anda multi-quantity per user, logic ini perlu disesuaikan dengan jumlah yang dipinjam.
        // Berhubung struktur saat ini 1 row = 1 kepemilikan/transaksi unik, increment 1 sudah benar.

        // 3. Update Status Request
        $returnRequest->update(['status' => 'approved']);

        // 4. Catat History
        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(), // Admin ID
            'action' => 'returned',
            'notes' => "Aset dikembalikan oleh user. Kondisi: {$returnRequest->condition}. Stok dikembalikan."
        ]);

        return back()->with('success', 'Pengembalian disetujui. Aset kembali ke stok.');
    }
}