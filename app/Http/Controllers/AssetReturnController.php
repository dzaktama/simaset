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
    /**
     * [USER] Form Pengajuan Pengembalian
     */
    public function store(Request $request)
    {
        // 1. Ambil data Request Asal dulu buat validasi tanggal
        $assetRequest = AssetRequest::findOrFail($request->asset_request_id);
        
        // Parsing tanggal pinjam untuk validasi
        $tglPinjam = Carbon::parse($assetRequest->request_date)->format('Y-m-d');

        // 2. Validasi Input
        $validated = $request->validate([
            'asset_request_id' => 'required|exists:asset_requests,id',
            'condition' => 'required|in:good,broken,maintenance',
            'notes' => 'nullable|string|max:255',
            // VALIDASI LOGIC: Tanggal kembali tidak boleh sebelum tanggal pinjam
            'return_date' => 'required|date|after_or_equal:' . $tglPinjam,
        ], [
            'return_date.after_or_equal' => 'Tanggal pengembalian tidak boleh lebih lampau dari tanggal peminjaman (' . Carbon::parse($tglPinjam)->format('d M Y') . ').'
        ]);

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

        if ($finalCondition == 'available') {
            // Jika bagus/layak, stok nambah & status available
            $asset->increment('quantity', $qtyToReturn);
            $asset->update(['status' => 'available']);
        } else {
            // Jika rusak/maintenance, stok TIDAK nambah ke 'available'
            // Status aset berubah jadi broken/maintenance
            $asset->update(['status' => $finalCondition]); 
        }

        // 2. Finalisasi Status
        $return->update([
            'status' => 'approved',
            'admin_id' => auth()->id()
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
            'notes' => "Aset dikembalikan oleh {$return->user->name}. Kondisi Akhir: " . ucfirst($finalCondition)
        ]);

        return back()->with('success', 'Pengembalian diverifikasi. Stok aset diperbarui.');
    }
}