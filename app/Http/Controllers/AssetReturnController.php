<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetReturn;
use App\Models\AssetHistory;
use App\Models\AssetRequest;
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
            'asset_request_id' => 'required|exists:asset_requests,id',
            'return_date' => 'required|date',
            'condition' => 'required|in:good,broken,maintenance',
            'notes' => 'nullable|string|max:255', // Ganti 'reason' jadi 'notes' biar konsisten
        ]);

        // 2. Ambil Data Request Asal
        $assetRequest = AssetRequest::with('asset')->findOrFail($request->asset_request_id);

        // 3. Cek Double Request (Anti-Bug)
        $existingReturn = AssetReturn::where('asset_request_id', $assetRequest->id)
                                     ->where('status', 'pending')
                                     ->first();
        if($existingReturn) {
            return back()->with('error', 'Pengembalian sedang diproses. Mohon tunggu verifikasi admin.');
        }

        // 4. Buat Record Pengembalian
        AssetReturn::create([
            'asset_request_id' => $assetRequest->id, // Simpan ID request biar bisa trace jumlah qty
            'user_id' => auth()->id(),
            'asset_id' => $assetRequest->asset_id,
            'return_date' => $validated['return_date'] . ' ' . now()->format('H:i:s'),
            'condition' => $validated['condition'],
            'notes' => $validated['notes'] ?? '-', 
            'status' => 'pending'
        ]);

        // Update status di tabel request biar user ga bisa klik kembalikan lagi
        $assetRequest->update(['status' => 'pending_return']);

        return back()->with('success', 'Pengembalian diajukan! Serahkan barang ke Admin untuk verifikasi.');
    }

    /**
     * [ADMIN] Verifikasi & Terima Barang (Revisi Logika Kritis)
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

        // Cek status agar tidak diproses ganda
        if ($return->status != 'pending') {
            return back()->with('error', 'Data ini sudah diproses sebelumnya.');
        }

        // 1. Logika Stok & Kondisi
        if ($return->condition == 'good') {
            // Jika kondisi bagus, stok gudang bertambah
            $asset->increment('quantity', $qtyToReturn);
            $asset->update(['status' => 'available']);
        } else {
            // Jika rusak/maintenance, stok gudang (available) JANGAN ditambah.
            // Status aset diubah sesuai kondisi (broken/maintenance).
            $asset->update(['status' => $return->condition]); 
        }

        // 2. Finalisasi Status Pengembalian
        $return->update([
            'status' => 'approved',
            'admin_id' => auth()->id()
        ]);

        // 3. Tutup Tiket Peminjaman (Jika datanya masih ada)
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
            'notes' => "Aset dikembalikan oleh {$return->user->name}. Kondisi: {$return->condition}. Verifikasi oleh Admin."
        ]);

        return back()->with('success', 'Pengembalian diverifikasi. Stok aset diperbarui.');
    }
}