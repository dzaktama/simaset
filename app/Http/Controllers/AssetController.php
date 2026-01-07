<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\AssetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * 1. DASHBOARD UTAMA (Pusat Informasi)
     */
    public function dashboard()
    {
        // Statistik untuk Cards
        $stats = [
            'total' => Asset::count(),
            'available' => Asset::where('status', 'available')->count(),
            'deployed' => Asset::where('status', 'deployed')->count(),
            'maintenance' => Asset::whereIn('status', ['maintenance', 'broken'])->count(),
            // Hitung request yang masih 'pending'
            'pending_requests' => AssetRequest::where('status', 'pending')->count(),
        ];

        // Ambil 5 Request terbaru untuk tabel notifikasi
        $recentRequests = AssetRequest::with(['user', 'asset'])
                            ->where('status', 'pending')
                            ->latest()
                            ->take(5)
                            ->get();

        return view('home', [
            'title' => 'Dashboard Utama',
            'stats' => $stats,
            'recentRequests' => $recentRequests
        ]);
    }

    /**
     * 2. HALAMAN LIST ASET (Bisa dilihat Admin & Karyawan)
     */
    public function index(Request $request)
    {
        $query = Asset::with('holder')->latest();

        // Fitur Pencarian
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%');
        }

        // --- LOGIC PENTING: FILTER KARYAWAN ---
        // Jika yang login BUKAN Admin, cuma kasih lihat barang yang 'Available'
        if (auth()->user()->role !== 'admin') {
            $query->where('status', 'available');
        }

        return view('assets.index', [
            'title' => 'Daftar Aset',
            'assets' => $query->paginate(10)->withQueryString()
        ]);
    }

    /**
     * 3. ASET SAYA (Khusus Karyawan melihat barang yang dia pegang)
     */
    public function myAssets()
    {
        $myAssets = Asset::where('user_id', auth()->id())->latest()->get();

        return view('assets.my_assets', [
            'title' => 'Aset Saya',
            'assets' => $myAssets
        ]);
    }

    // --- FITUR CRUD ADMIN (Create, Store, Edit, Update, Destroy) ---

    public function create()
    {
        return view('assets.create', [
            'title' => 'Input Aset Baru',
            'users' => User::all()
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'serial_number' => 'required|unique:assets',
            'status' => 'required',
            'user_id' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date',
            'description' => 'nullable',
            'image' => 'image|file|max:2048'
        ]);

        if ($request->file('image')) {
            $validatedData['image'] = $request->file('image')->store('asset-images');
        }

        Asset::create($validatedData);
        return redirect('/assets')->with('success', 'Aset baru berhasil ditambahkan!');
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', [
            'title' => 'Edit Data Aset',
            'asset' => $asset,
            'users' => User::all()
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        $rules = [
            'name' => 'required|max:255',
            'status' => 'required',
            'user_id' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date',
            'description' => 'nullable',
            'image' => 'image|file|max:2048'
        ];

        if($request->serial_number != $asset->serial_number) {
            $rules['serial_number'] = 'required|unique:assets';
        }

        $validatedData = $request->validate($rules);

        if ($request->file('image')) {
            if ($asset->image) Storage::delete($asset->image);
            $validatedData['image'] = $request->file('image')->store('asset-images');
        }

        $asset->update($validatedData);
        return redirect('/assets')->with('success', 'Data aset berhasil diperbarui!');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->image) Storage::delete($asset->image);
        $asset->delete();
        return redirect('/assets')->with('success', 'Aset telah dihapus.');
    }

    // --- FITUR TRANSAKSI (REQUEST & APPROVAL) ---

    /**
     * Karyawan meminta barang (Klik tombol 'Pinjam')
     */
    public function requestAsset(Request $request, $id)
    {
        // Cek dulu apakah barang masih available
        $asset = Asset::findOrFail($id);
        if($asset->status != 'available') {
            return back()->with('loginError', 'Maaf, aset ini sudah tidak tersedia.');
        }

        // Cek apakah user sudah pernah request barang ini dan statusnya masih pending
        $existingRequest = AssetRequest::where('user_id', auth()->id())
                            ->where('asset_id', $id)
                            ->where('status', 'pending')
                            ->first();
        
        if($existingRequest) {
            return back()->with('loginError', 'Anda sudah mengajukan permintaan untuk aset ini, harap tunggu konfirmasi Admin.');
        }

        // Buat Request Baru
        AssetRequest::create([
            'user_id' => auth()->id(),
            'asset_id' => $id,
            'request_date' => now(),
            'status' => 'pending',
            'reason' => 'Saya membutuhkan aset ini untuk operasional kerja.' // Bisa dikembangkan jadi inputan form
        ]);

        return redirect('/my-assets')->with('success', 'Permintaan peminjaman berhasil dikirim! Menunggu persetujuan Admin.');
    }

    /**
     * Admin Menyetujui Permintaan
     */
    public function approveRequest($requestId)
    {
        $request = AssetRequest::findOrFail($requestId);
        
        // 1. Update status request jadi 'approved'
        $request->update(['status' => 'approved']);

        // 2. Update aset jadi 'deployed' dan pindah tangan ke peminjam
        $asset = Asset::findOrFail($request->asset_id);
        $asset->update([
            'status' => 'deployed',
            'user_id' => $request->user_id
        ]);

        // 3. (Opsional) Tolak request lain untuk barang yang sama
        AssetRequest::where('asset_id', $asset->id)
                    ->where('id', '!=', $requestId)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);

        return back()->with('success', 'Permintaan disetujui. Aset telah dipindahtangankan.');
    }

    /**
     * Admin Menolak Permintaan
     */
    public function rejectRequest($requestId)
    {
        $request = AssetRequest::findOrFail($requestId);
        $request->update(['status' => 'rejected']);

        return back()->with('success', 'Permintaan telah ditolak.');
    }
}