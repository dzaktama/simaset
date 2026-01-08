<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AssetHistory; // Penting untuk log history
use Illuminate\Http\Request;

class AssetRequestController extends Controller
{
    /**
     * [KARYAWAN] Mengajukan Peminjaman
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'quantity' => 'required|integer|min:1', 
            'return_date' => 'nullable|date|after_or_equal:today',
            'reason' => 'required|string|max:255',
        ]);

        // 2. Cek Stok (Pencegahan Awal)
        $asset = Asset::findOrFail($request->asset_id);
        
        if ($request->quantity > $asset->quantity) {
            return back()->with('error', "Gagal! Stok tidak mencukupi. Tersisa {$asset->quantity} unit.");
        }

        // 3. Simpan Request
        AssetRequest::create([
            'user_id' => auth()->id(),
            'asset_id' => $validatedData['asset_id'],
            'quantity' => $validatedData['quantity'],
            'request_date' => now(),
            'return_date' => $validatedData['return_date'] ?? null,
            'reason' => $validatedData['reason'],
            'status' => 'pending'
        ]);

        return back()->with('success', 'Permintaan berhasil dikirim! Menunggu persetujuan Admin.');
    }

    /**
     * [ADMIN] Menyetujui Peminjaman (Stok Berkurang)
     */
    public function approve($id)
    {
        $request = AssetRequest::findOrFail($id);
        $asset = Asset::findOrFail($request->asset_id);

        // 1. Cek Stok Lagi (Untuk keamanan saat admin klik approve)
        if ($asset->quantity < $request->quantity) {
            return back()->with('error', 'Gagal! Stok barang ini sudah habis atau tidak cukup.');
        }

        // 2. KURANGI STOK (POIN B: Sesuai jumlah pinjam)
        $asset->decrement('quantity', $request->quantity);

        // 3. Update Status Aset
        // Jika stok habis (0), ubah status jadi deployed. 
        // Jika masih ada sisa, biarkan available agar orang lain bisa pinjam sisanya.
        if ($asset->quantity == 0) {
            $asset->update([
                'status' => 'deployed',
                'user_id' => $request->user_id, // Set pemegang terakhir
                'assigned_date' => now(),
                'return_date' => $request->return_date
            ]);
        } else {
            // Jika ini barang bulk (stok banyak), status tetap available 
            // tapi kita catat tanggalnya saja di background
            $asset->update([
                'status' => 'available' 
            ]);
        }

        // 4. Update Status Request
        $request->update(['status' => 'approved']);

        // 5. Catat History
        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(), // Admin yang approve
            'action' => 'approved',
            'notes' => "Disetujui untuk: {$request->user->name}. Mengurangi stok sebanyak {$request->quantity}."
        ]);

        return back()->with('success', 'Permintaan disetujui. Stok aset telah dikurangi.');
    }

    /**
     * [ADMIN] Menolak Peminjaman
     */
    public function reject(Request $req, $id)
    {
        $request = AssetRequest::findOrFail($id);

        // Validasi Alasan
        $req->validate([
            'admin_note' => 'required|string|max:255'
        ]);

        // Update Status Request
        $request->update([
            'status' => 'rejected',
            'admin_note' => $req->admin_note
        ]);

        // Catat History
        AssetHistory::create([
            'asset_id' => $request->asset_id,
            'user_id' => auth()->id(),
            'action' => 'rejected',
            'notes' => "Ditolak: " . $req->admin_note
        ]);

        return back()->with('success', 'Permintaan berhasil ditolak.');
    }
}