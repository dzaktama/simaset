<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AssetReturn;
use App\Models\AssetHistory;
use Illuminate\Http\Request;

class AssetReturnController extends Controller
{
    /**
     * [USER] Form Pengajuan Pengembalian
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_request_id' => 'required|exists:asset_requests,id',
            'condition' => 'required|in:good,broken,maintenance',
            'notes' => 'nullable|string|max:255',
            'return_date' => 'required|date'
        ]);

        $assetRequest = AssetRequest::findOrFail($validated['asset_request_id']);

        // Cek Double Return (Anti-Bug)
        if ($assetRequest->status == 'returned' || $assetRequest->status == 'pending_return') {
            return back()->with('error', 'Aset ini sudah dikembalikan atau sedang menunggu verifikasi.');
        }

        // Simpan Data Pengembalian
        AssetReturn::create([
            'asset_request_id' => $assetRequest->id,
            'user_id' => auth()->id(),
            'asset_id' => $assetRequest->asset_id,
            'return_date' => $validated['return_date'] . ' ' . now()->format('H:i:s'), // Gabung jam server
            'condition' => $validated['condition'],
            'notes' => $validated['notes'],
            'status' => 'pending'
        ]);

        // Update Status Request Jadi "Pending Return" (Biar gak bisa dipinjam/dibalikin lagi)
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
        $assetReq = AssetRequest::findOrFail($return->asset_request_id);

        if ($return->status != 'pending') {
            return back()->with('error', 'Data ini sudah diproses sebelumnya.');
        }

        // 1. Logika Stok & Kondisi
        if ($return->condition == 'good') {
            // Jika bagus, stok nambah & status available
            $asset->increment('quantity', $assetReq->quantity); // Kembalikan sejumlah yg dipinjam
            $asset->update(['status' => 'available']);
        } else {
            // Jika rusak, stok TIDAK nambah ke 'available', tapi status aset jadi maintenance/broken
            // Opsional: Anda bisa buat kolom 'broken_stock' di tabel assets jika mau tracking detail
            $asset->update(['status' => $return->condition]); 
        }

        // 2. Finalisasi Status
        $return->update([
            'status' => 'approved',
            'admin_id' => auth()->id()
        ]);

        // 3. Tutup Tiket Peminjaman
        $assetReq->update([
            'status' => 'returned',
            'return_date' => now() // Tanggal real pengembalian
        ]);

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