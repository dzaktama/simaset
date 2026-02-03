<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    // Constructor removed to allow custom redirect logic

    /**
     * Dashboard Gudang & Lokasi
     * Menampilkan statistik sebaran aset per lokasi.
     */
    public function index()
    {
        // [SECURITY] Manual Gate Check
        if (\Illuminate\Support\Facades\Gate::denies('asset.edit')) {
            return redirect()->route('dashboard')->with('error', 'Akses Ditolak! Anda tidak memiliki izin mengakses Manajemen Gudang.');
        }

        // 1. Ambil Statistik Lokasi (Group by location)
        // Abaikan lokasi null atau string kosong
        $locationStats = Asset::select('location', DB::raw('count(*) as total'))
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->get();

        // 2. Daftar Aset Terbaru (Limit 5) yang baru ditambahkan/diupdate
        $recentAssets = Asset::latest()->take(5)->get();

        // 3. Riwayat Perpindahan Terakhir (Limit 5) - Filter action 'move' atau 'relocation'
        // Asumsi kita akan pakai action 'moved' untuk mutasi
        $recentMovements = AssetHistory::with(['asset', 'user'])
            ->where('action', 'moved')
            ->latest()
            ->take(5)
            ->get();

        return view('warehouse.index', [
            'title' => 'Manajemen Gudang',
            'locationStats' => $locationStats,
            'recentAssets' => $recentAssets,
            'recentMovements' => $recentMovements
        ]);
    }

    /**
     * Form Mutasi Aset (Pindah Lokasi)
     */
    public function createMove(Request $request, $id = null)
    {
        // [SECURITY] Manual Gate Check
        if (\Illuminate\Support\Facades\Gate::denies('asset.edit')) {
            return redirect()->back()->with('error', 'Akses Ditolak! Hanya Staff Gudang / Admin yang boleh memindahkan aset.');
        }

        $selectedAsset = null;
        if ($id) {
            $selectedAsset = Asset::find($id);
        }

        // Tampilkan view form mutasi
        return view('warehouse.move', [
            'title' => 'Mutasi Aset',
            'selectedAsset' => $selectedAsset,
            // Kirim daftar opsi lokasi unik yang sudah ada di DB sebagai saran
            'existingLocations' => Asset::select('location')->distinct()->whereNotNull('location')->pluck('location')
        ]);
    }

    /**
     * Proses Simpan Perpindahan Aset
     */
    public function storeMove(Request $request)
    {
        // [SECURITY] Manual Gate Check
        if (\Illuminate\Support\Facades\Gate::denies('asset.edit')) {
            return redirect()->route('dashboard')->with('error', 'Akses Ilegal! Percobaan mutasi aset tanpa izin telah dicatat.');
        }

        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'target_location' => 'required|string|max:100',
            'notes' => 'nullable|string|max:255'
        ]);

        $asset = Asset::findOrFail($request->asset_id);
        $oldLocation = $asset->location;
        $newLocation = $request->target_location;

        if ($oldLocation === $newLocation) {
            return back()->with('error', 'Lokasi tujuan sama dengan lokasi saat ini.');
        }

        // Parse Lorong and Rak from "Area X - Rak Y" format
        // Expected Format: "Area A - Rak R-01" or "Area A - Rak R-01 (Notes)"
        $lorong = null;
        $rak = null;

        if (preg_match('/Area\s+([A-Z]+)\s+-\s+Rak\s+([A-Z0-9\-]+)/i', $newLocation, $matches)) {
            $lorong = 'Area ' . strtoupper($matches[1]);
            $rak = strtoupper($matches[2]); 
        }

        // 1. Update Lokasi Aset
        $asset->update([
            'location' => $newLocation,
            'lorong' => $lorong,
            'rak' => $rak
        ]);

        // 2. Catat di History
        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'moved', // Keyword khusus mutasi
            'notes' => "Memindahkan aset dari [{$oldLocation}] ke [{$newLocation}]. " . ($request->notes ? "Catatan: " . $request->notes : "")
        ]);

        return redirect()->route('warehouse.index')->with('success', 'Aset berhasil dipindahkan ke ' . $newLocation);
    }

    /**
     * Halaman Riwayat Perpindahan (Audit Log)
     */
    public function history()
    {
        $movements = AssetHistory::with(['asset', 'user'])
            ->where('action', 'moved')
            ->latest()
            ->paginate(20);

        return view('warehouse.history', [
            'title' => 'Riwayat Perpindahan',
            'movements' => $movements
        ]);
    }
}
