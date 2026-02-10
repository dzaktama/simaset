<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AssetReturn;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AssetReturnController extends Controller
{
    public function __construct()
    {
        // Index & Verify butuh izin 'return.verify' (Admin/Teknisi)
        // Update juga diprotect karena dipakai verify (via resource update/store logic kadang verified via update)
        // Store (User mengajukan) dibiarkan terbuka untuk Auth
        $this->middleware('can:return.verify')->only(['index', 'verify', 'update']);
    }
    /**
     * [ADMIN/TEKNISI] List Pengembalian yang perlu diverifikasi
     */
    public function index()
    {
        // Ambil data pengembalian yang statusnya 'pending' (Perlu verifikasi)
        // Dan juga yang sudah selesai untuk history, tapi prioritas pending di atas
        $returns = AssetReturn::with(['asset', 'user', 'assetRequest'])
                    ->orderByRaw("FIELD(status, 'pending') DESC")
                    ->orderBy('created_at', 'DESC')
                    ->paginate(10);

        return view('returns.index', compact('returns'));
    }

    /**
     * [USER] Form Pengajuan Pengembalian
     */
    public function store(Request $request)
    {
        // 1. Ambil data Request Asal
        $assetRequest = AssetRequest::findOrFail($request->asset_request_id);
        
        // 2. Validasi Input (Tanggal dikembalikan otomatis hari ini)
        $validated = $request->validate([
            'asset_request_id' => 'required|exists:asset_requests,id',
            'condition' => 'required|in:good,broken,maintenance',
            'notes' => 'nullable|string|max:255',
            'photo_proof_1' => 'required|image|max:2048', // Wajib 1
            'photo_proof_2' => 'nullable|image|max:2048', 
            'photo_proof_3' => 'nullable|image|max:2048', 
        ], [
            'photo_proof_1.required' => 'Wajib menyertakan minimal 1 foto bukti kondisi aset.',
            'photo_proof_1.image' => 'File harus berupa gambar.',
            'photo_proof_1.max' => 'Ukuran foto maksimal 2MB per file.'
        ]);

        // Cek Double Return (Anti-Bug)
        if ($assetRequest->status == 'returned' || $assetRequest->status == 'pending_return') {
            return back()->with('error', 'Aset ini sudah dikembalikan atau sedang menunggu verifikasi.');
        }

        // [NEW] Upload Hingga 3 Foto
        $photos = [];
        for ($i = 1; $i <= 3; $i++) {
            $key = "photo_proof_$i";
            if ($request->hasFile($key)) {
                $photos[$key] = $request->file($key)->store('returns', 'public');
            } else {
                $photos[$key] = null;
            }
        }

        // Simpan Data Pengembalian
        AssetReturn::create([
            'asset_request_id' => $assetRequest->id,
            'user_id' => auth()->id(),
            'asset_id' => $assetRequest->asset_id,
            'return_date' => now(), // Otomatis waktu server saat klik
            'condition' => $validated['condition'],
            'notes' => $validated['notes'],
            'photo_proof_1' => $photos['photo_proof_1'],
            'photo_proof_2' => $photos['photo_proof_2'],
            'photo_proof_3' => $photos['photo_proof_3'],
            'status' => 'pending'
        ]);

        // Update Status Request Jadi "Pending Return"
        $assetRequest->update(['status' => 'pending_return']);

        return back()->with('success', 'Pengembalian diajukan! Harap serahkan barang ke Admin untuk verifikasi.');
    }

    /**
     * [ADMIN] Verifikasi Pengembalian (Stok Bertambah Disini)
     */
    public function verify(Request $request, $id)
    {
        $return = AssetReturn::findOrFail($id);
        $asset = Asset::findOrFail($return->asset_id);
        
        // [FIX ERROR] Safety Check Quantity
        // Ambil jumlah dari request asal. Jika datanya hilang (null), default ke 1.
        $qtyToReturn = 1;
        if ($return->assetRequest) {
            $qtyToReturn = $return->assetRequest->quantity;
        }

        if ($return->status != 'pending') {
            return back()->with('error', 'Data ini sudah diproses sebelumnya.');
        }

        // 1. Logika Stok & Kondisi
        // Admin bisa override kondisi final via input radio button
        $finalCondition = $request->input('final_condition', $return->condition); 
        $fine = $request->input('fine', 0);

        if ($finalCondition == 'available') {
            // Jika bagus/layak, stok nambah & status available
            $asset->increment('quantity', $qtyToReturn);
            $asset->update(['status' => 'available']);
        } else {
            // Jika rusak/maintenance, stok TIDAK nambah ke 'available'
            // Status aset berubah jadi broken/maintenance
            $asset->update(['status' => $finalCondition]); 
            
            // [NEW] Auto-Create Maintenance Ticket
            if (in_array($finalCondition, ['maintenance', 'broken'])) {
                \App\Models\Maintenance::create([
                    'asset_id' => $asset->id,
                    'user_id' => auth()->id(), // Admin yang lapor
                    'vendor_name' => 'Internal Review', // Default
                    'start_date' => now(),
                    'problem_description' => "Kondisi aset: " . ucfirst($finalCondition) . ". Catatan User: " . ($return->notes ?? '-'),
                    'status' => 'pending'
                ]);
            }
        }

        // 2. Finalisasi Status
        $return->update([
            'status' => 'approved',
            'admin_id' => auth()->id(),
            'condition' => $finalCondition,
            'fine' => $fine
        ]);

        // 3. Tutup Tiket Peminjaman
        if ($return->assetRequest) {
            $return->assetRequest->update([
                'status' => 'returned',
                'return_date' => now()
            ]);
        }

        // 4. Catat History
        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'returned',
            'notes' => "Aset dikembalikan oleh {$return->user->name}. Kondisi Akhir: " . ucfirst($finalCondition) . ($fine > 0 ? ". Denda: Rp " . number_format($fine) : "")
        ]);

        return back()->with('success', 'Pengembalian diverifikasi. Stok aset diperbarui.');
    }
}