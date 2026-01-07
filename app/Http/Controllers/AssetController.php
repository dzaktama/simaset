<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Dashboard Utama (Home)
     */
    public function dashboard()
    {
        return view('home', [
            'title' => 'Dashboard Overview',
            'totalAssets' => Asset::count(),
            'availableAssets' => Asset::where('status', 'available')->count(),
            'deployedAssets' => Asset::where('status', 'deployed')->count(),
            'maintenanceAssets' => Asset::whereIn('status', ['maintenance', 'broken'])->count(),
            'recentAssets' => Asset::with('holder')->latest()->take(5)->get()
        ]);
    }

    /**
     * Menampilkan List Semua Aset (Halaman Inventory)
     */
    public function index()
    {
        // Fitur Search & Pagination
        $assets = Asset::with('holder')->latest();

        if (request('search')) {
            $assets->where('name', 'like', '%' . request('search') . '%')
                   ->orWhere('serial_number', 'like', '%' . request('search') . '%');
        }

        return view('assets.index', [
            'title' => 'Daftar Semua Aset',
            'assets' => $assets->paginate(10)->withQueryString() // Pagination 10 baris
        ]);
    }

    /**
     * Form Tambah Aset Baru
     */
    public function create()
    {
        return view('assets.create', [
            'title' => 'Input Aset Baru',
            'users' => User::all() // Kirim data user buat dropdown 'Pemegang'
        ]);
    }

    /**
     * Proses Simpan ke Database
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'serial_number' => 'required|unique:assets',
            'status' => 'required',
            'user_id' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date',
            'description' => 'nullable',
            'image' => 'image|file|max:2048' // Validasi Foto Max 2MB
        ]);

        // Logic Upload Gambar
        if ($request->file('image')) {
            $validatedData['image'] = $request->file('image')->store('asset-images');
        }

        Asset::create($validatedData);

        return redirect('/assets')->with('success', 'Aset baru berhasil ditambahkan!');
    }

    /**
     * Form Edit Aset
     */
    public function edit(Asset $asset)
    {
        return view('assets.edit', [
            'title' => 'Edit Data Aset',
            'asset' => $asset,
            'users' => User::all()
        ]);
    }

    /**
     * Proses Update Data
     */
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

        // Cek serial number unik (kecuali punya dia sendiri)
        if($request->serial_number != $asset->serial_number) {
            $rules['serial_number'] = 'required|unique:assets';
        }

        $validatedData = $request->validate($rules);

        // Update Gambar jika ada yang baru
        if ($request->file('image')) {
            if ($asset->image) {
                Storage::delete($asset->image); // Hapus gambar lama
            }
            $validatedData['image'] = $request->file('image')->store('asset-images');
        }

        $asset->update($validatedData);

        return redirect('/assets')->with('success', 'Data aset berhasil diperbarui!');
    }

    /**
     * Hapus Aset
     */
    public function destroy(Asset $asset)
    {
        if ($asset->image) {
            Storage::delete($asset->image);
        }
        $asset->delete();
        return redirect('/assets')->with('success', 'Aset telah dihapus dari sistem.');
    }
}