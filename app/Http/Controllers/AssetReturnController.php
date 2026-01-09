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
     * [USER] Mengajukan Pengembalian Aset
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        // Kita ubah validasi 'asset_id' jadi 'asset_request_id' sesuai form
        $request->validate([
            'asset_request_id' => 'required|exists:asset_requests,id',
            'condition' => 'required|in:good,broken,maintenance',
            'notes' => 'nullable|string|max:255',
            'return_date' => 'required|date', 
        ]);

        // 2. Ambil Data Peminjaman Aslinya
        $assetRequest = AssetRequest::findOrFail($request->asset_request_id);
        
        // 3. LOGIC CHECK: Cegah Tanggal Balik Sebelum Tanggal Pinjam
        // Ambil tanggal pinjam (request_date atau assigned_date)
        $tglPinjam = Carbon::parse($assetRequest->created_at)->startOfDay(); 
        $tglBalik = Carbon::parse($request->return_date)->startOfDay();

        if ($tglBalik->lt($tglPinjam)) {
            return back()->with('error', 'Error: Tanggal pengembalian tidak boleh lebih lampau dari tanggal peminjaman (' . $tglPinjam->format('d M Y') . ').');
        }

        // 4. Cek Double Return (Anti-Spam)
        if ($assetRequest->status == 'returned' || $assetRequest->status == 'pending_return') {
            return back()->with('error', 'Aset ini sudah dalam proses pengembalian.');
        }

        // 5. Simpan Data Pengembalian
        AssetReturn::create([
            'asset_request_id' => $assetRequest->id,
            'user_id' => auth()->id(),
            'asset_id' => $assetRequest->asset_id, // Ambil ID Aset dari relasi request
            'return_date' => $request->return_date . ' ' . now()->format('H:i:s'), // Gabung jam server
            'condition' => $request->condition,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        // Update status di request asal agar tombol "Kembalikan" hilang/berubah
        $assetRequest->update(['status' => 'pending_return']);

        return back()->with('success', 'Pengajuan pengembalian berhasil! Harap serahkan fisik barang ke Admin.');
    }

    /**
     * [ADMIN] Verifikasi & Terima Barang
     */
    public function verify(Request $request, $id)
    {
        $return = AssetReturn::findOrFail($id);
        $asset = Asset::findOrFail($return->asset_id);
        $assetReq = AssetRequest::findOrFail($return->asset_request_id);

        if ($return->status != 'pending') {
            return back()->with('error', 'Data ini sudah diproses.');
        }

        // 1. Update Stok & Status Aset Utama
        if ($return->condition == 'good') {
            $asset->increment('quantity', $assetReq->quantity); // Balikin stok
            // Cek logic status: Jika stok > 0, set available (kecuali ada logic lain)
            $asset->update(['status' => 'available']);
        } else {
            // Jika rusak, status aset jadi rusak/maintenance
            $asset->update(['status' => $return->condition]); 
            // Opsional: Stok tetap di-increment tapi statusnya rusak, atau dipisah ke gudang rusak
        }

        // 2. Finalisasi Status Return
        $return->update([
            'status' => 'approved',
            'admin_id' => auth()->id() // Admin yang memverifikasi
        ]);

        // 3. Tutup Tiket Peminjaman
        $assetReq->update([
            'status' => 'returned',
            'return_date' => now()
        ]);

        // 4. Catat History
        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'returned',
            'notes' => "Pengembalian diverifikasi Admin. Kondisi: {$return->condition}."
        ]);

        return back()->with('success', 'Aset berhasil diterima dan stok diperbarui.');
    }
}