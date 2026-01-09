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
        // Validasi Keputusan Admin
        $request->validate([
            'final_condition' => 'required|in:available,maintenance,broken'
        ]);

        $returnItem = AssetReturn::with(['assetRequest', 'asset', 'user'])->findOrFail($id);
        $asset = $returnItem->asset;
        $originalRequest = $returnItem->assetRequest;

        // 1. Cek Validasi Status
        if ($returnItem->status != 'pending') {
            return back()->with('error', 'Data ini sudah diproses sebelumnya.');
        }

        // 2. [LOGIC FIXED] Kembalikan Stok Sesuai Jumlah Pinjam
        $qtyReturned = $originalRequest->quantity; 
        $asset->increment('quantity', $qtyReturned);

        // 3. Update Status Aset Berdasarkan Keputusan Admin (Bukan User)
        $asset->update([
            'status' => $request->final_condition, // Admin yang menentukan status akhir
            'user_id' => null, // Lepas kepemilikan
            'assigned_date' => null,
            'return_date' => null,
            'condition_notes' => "Ex-Pengembalian: " . ($returnItem->notes ?? '-') // Catat history kondisi
        ]);

        // 4. Tutup Tiket
        $returnItem->update([
            'status' => 'approved',
            'admin_id' => auth()->id()
        ]);
        
        $originalRequest->update(['status' => 'returned']);

        // 5. Catat History Lengkap
        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(), // Admin
            'action' => 'returned',
            'notes' => "Diterima Admin. Kondisi Akhir: {$request->final_condition}. User Note: {$returnItem->notes}. Qty Kembali: {$qtyReturned}"
        ]);

        return back()->with('success', 'Aset berhasil diverifikasi dan stok telah dikembalikan.');
    }
}