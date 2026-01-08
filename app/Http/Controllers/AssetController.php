<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\AssetRequest;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class AssetController extends Controller
{
    /**
     * Menampilkan Dashboard Utama
     */
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Data untuk Admin (Stats + Lists untuk Modal)
            return view('home', [
                'title' => 'Dashboard Admin',
                'stats' => [
                    'total' => Asset::count(),
                    'available' => Asset::where('status', 'available')->count(),
                    'deployed' => Asset::where('status', 'deployed')->count(),
                    'maintenance' => Asset::whereIn('status', ['maintenance', 'broken'])->count(),
                    'pending_requests' => AssetRequest::where('status', 'pending')->count(),
                ],
                // Data List untuk Modal Detail
                'listTotal' => Asset::with('holder')->latest()->get(),
                'listAvailable' => Asset::where('status', 'available')->latest()->get(),
                'listMaintenance' => Asset::whereIn('status', ['maintenance', 'broken'])->with('holder')->latest()->get(),
                'listPending' => AssetRequest::with(['user', 'asset'])->where('status', 'pending')->latest()->get(),
                
                // Data untuk Tabel Utama (Limit 5 biar rapi)
                'recentRequests' => AssetRequest::with(['user', 'asset'])->where('status', 'pending')->latest()->take(5)->get(),
                'activities' => AssetHistory::with(['user', 'asset'])->latest()->take(6)->get()
            ]);
        } else {
            // Data untuk Karyawan
            return view('home', [
                'title' => 'Dashboard Karyawan',
                'activeAssetsCount' => Asset::where('user_id', $user->id)->where('status', 'deployed')->count(),
                'pendingRequestsCount' => AssetRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
                'myRequests' => AssetRequest::with('asset')->where('user_id', $user->id)->latest()->take(5)->get(),
                'myActiveAssets' => Asset::where('user_id', $user->id)->latest()->take(3)->get()
            ]);
        }
    }

    /**
     * Menampilkan Daftar Semua Aset (Katalog)
     */
    public function index(Request $request)
    {
        // Start Query
        $query = Asset::with(['holder', 'latestApprovedRequest']);

        // 1. Fitur Search (Nama, SN, Deskripsi)
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // 2. Filter Status
        if ($request->has('status') && $request->status != 'all') {
             $query->where('status', $request->status);
        }

        // 3. Fitur Sorting (Urutkan)
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest': $query->oldest(); break; // Terlama
                case 'stock_low': $query->orderBy('quantity', 'asc'); break; // Stok Sedikit
                case 'stock_high': $query->orderBy('quantity', 'desc'); break; // Stok Banyak
                case 'name_asc': $query->orderBy('name', 'asc'); break; // Nama A-Z
                default: $query->latest(); break; // Terbaru (Default)
            }
        } else {
            $query->latest();
        }

        return view('assets.index', [
            'title' => 'Katalog Aset IT',
            'assets' => $query->paginate(10)->withQueryString()
        ]);
    }

    /**
     * Menampilkan Halaman 'Aset Saya' (Karyawan)
     */
    public function myAssets()
    {
        return view('assets.my_assets', [
            'title' => 'Aset Saya',
            'assets' => Asset::where('user_id', auth()->id())->latest()->get()
        ]);
    }

    /**
     * Form Tambah Aset Baru
     */
    public function create()
    {
        // LOGIC AUTO-GENERATE SERIAL NUMBER
        $lastAsset = Asset::latest('id')->first();
        $nextId = $lastAsset ? ($lastAsset->id + 1) : 1;
        $generatedSN = 'INV-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('assets.create', [
            'title' => 'Input Aset Baru',
            'users' => User::all(),
            'suggestedSN' => $generatedSN
        ]);
    }

    /**
     * Simpan Aset Baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'serial_number' => 'required|unique:assets',
            'quantity' => 'required|integer|min:1',
            'status' => 'required',
            'user_id' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date',
            'description' => 'nullable',
            'condition_notes' => 'nullable',
            'image' => 'image|file|max:2048',
            'image2' => 'image|file|max:2048',
            'image3' => 'image|file|max:2048',
        ]);

        // Upload Gambar
        foreach (['image', 'image2', 'image3'] as $key) {
            if ($request->file($key)) {
                $validatedData[$key] = $request->file($key)->store('asset-images', 'public');
            }
        }

        $asset = Asset::create($validatedData);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'notes' => 'Aset baru ditambahkan. Stok awal: ' . $validatedData['quantity']
        ]);

        return redirect('/assets')->with('success', 'Aset berhasil ditambahkan!');
    }

    /**
     * Form Edit Aset
     */
    public function edit(Asset $asset) {
        return view('assets.edit', ['title' => 'Edit Data Aset', 'asset' => $asset, 'users' => User::all()]);
    }

    /**
     * Update Aset (Termasuk Smart Logic Status & Tanggal)
     */
   public function update(Request $request, Asset $asset)
    {
        // validasi input dasar
        $rules = [
            'name' => 'required|max:255',
            'status' => 'required',
            'quantity' => 'required|integer|min:0',
            'user_id' => 'nullable|exists:users,id',
            'manual_quantity' => 'nullable|integer|min:1',
            'assigned_date' => 'nullable', 
            'return_date' => 'nullable',   
            'purchase_date' => 'nullable|date',
            'description' => 'nullable',
            'condition_notes' => 'nullable',
            'image' => 'image|file|max:2048',
            'image2' => 'image|file|max:2048',
            'image3' => 'image|file|max:2048',
        ];

        // cek kalo serial number diganti baru divalidasi unik
        if($request->serial_number != $asset->serial_number) {
            $rules['serial_number'] = 'required|unique:assets';
        }

        $validatedData = $request->validate($rules);

        // handle upload gambar
        foreach (['image', 'image2', 'image3'] as $key) {
            if ($request->file($key)) {
                if ($asset->$key) Storage::disk('public')->delete($asset->$key);
                $validatedData[$key] = $request->file($key)->store('asset-images', 'public');
            }
        }

        // format tanggal
        if (!empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = \Carbon\Carbon::parse($validatedData['assigned_date'])->format('Y-m-d H:i:s');
        }
        if (!empty($validatedData['return_date'])) {
            $validatedData['return_date'] = \Carbon\Carbon::parse($validatedData['return_date'])->format('Y-m-d H:i:s');
        }

        // paksa user kosong kalo status available biar ga konflik
        if ($validatedData['status'] == 'available') {
            $validatedData['user_id'] = null;
            $validatedData['assigned_date'] = null;
            $validatedData['return_date'] = null;
        }

        // logika split aset (pecah stok)
        if ($request->user_id && $request->manual_quantity && $request->manual_quantity < $asset->quantity) {
            
            $stokAwal = $asset->quantity;
            $stokDiambil = $request->manual_quantity;
            $sisaStok = $stokAwal - $stokDiambil;

            // update induk jadi sisa stok
            $dataInduk = $validatedData;
            $dataInduk['quantity'] = $sisaStok;
            $dataInduk['user_id'] = null;       
            $dataInduk['status'] = 'available'; 
            $dataInduk['assigned_date'] = null;
            $dataInduk['return_date'] = null;

            $asset->update($dataInduk);

            // catat history induk
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'split_stock',
                'notes' => "Stok dipecah: {$stokAwal} -> {$sisaStok}. ({$stokDiambil} unit dipisah untuk user lain)"
            ]);

            // buat aset baru pecahan
            $newAsset = $asset->replicate(); 
            
            // perbaikan error: pake fallback ke sn lama kalo ga ada di validated
            $baseSN = $validatedData['serial_number'] ?? $asset->serial_number;
            $newAsset->serial_number = $baseSN . '-S' . rand(100,999); 
            
            $newAsset->quantity = $stokDiambil;
            $newAsset->user_id = $request->user_id; 
            $newAsset->status = 'deployed';
            $newAsset->assigned_date = $validatedData['assigned_date'] ?? now();
            $newAsset->return_date = $validatedData['return_date'] ?? null;
            
            $newAsset->image = null; 
            $newAsset->image2 = null; 
            $newAsset->image3 = null;
            
            $newAsset->save();

            // catat history aset baru
            AssetHistory::create([
                'asset_id' => $newAsset->id,
                'user_id' => auth()->id(),
                'action' => 'manual_override',
                'notes' => "Aset hasil pecahan (Split) dari induk. Dipinjamkan ke user ID: " . $request->user_id
            ]);

            return redirect('/assets')->with('success', "Berhasil! Stok dipecah: {$sisaStok} di Gudang, {$stokDiambil} dipinjam User.");
        }

        // logika update standar
        if ($validatedData['status'] != 'available') {
            if ($validatedData['user_id'] && $validatedData['status'] == 'available') {
                $validatedData['status'] = 'deployed';
            }
            if (!$validatedData['user_id'] && $validatedData['status'] == 'deployed') {
                $validatedData['status'] = 'available';
            }
        }
        
        if ($validatedData['status'] == 'deployed' && empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = now();
        }

        if ($asset->status != $validatedData['status']) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'status_change',
                'notes' => "Status berubah: {$asset->status} -> {$validatedData['status']}"
            ]);
        }

        $asset->update($validatedData);

        return redirect('/assets')->with('success', 'Data aset berhasil diperbarui!');
    }

    /**
     * Hapus Aset
     */
    public function destroy(Asset $asset) 
    {
        // 1. Cek Keamanan: Jangan hapus kalo lagi dipinjam orang!
        if ($asset->status == 'deployed' && $asset->user_id != null) {
            return redirect()->back()->with('error', 'GAGAL HAPUS: Aset sedang dipinjam user. Kembalikan dulu (set Available) baru bisa dihapus.');
        }

        // 2. Hapus Gambar Fisik di Server biar hemat storage
        if ($asset->image) Storage::disk('public')->delete($asset->image);
        if ($asset->image2) Storage::disk('public')->delete($asset->image2);
        if ($asset->image3) Storage::disk('public')->delete($asset->image3);

        // 3. Hapus Data dari DB
        $asset->delete();

        return redirect('/assets')->with('success', 'Aset berhasil dihapus selamanya.');
    }
    public function reportIndex()
    {
        return view('reports.index', [
            'title' => 'Generator Laporan Aset',
            'totalAssets' => Asset::count(),
            'availableAssets' => Asset::where('status', 'available')->count(),
            'deployedAssets' => Asset::where('status', 'deployed')->count(),
        ]);
    }
    /**
     * Cetak Laporan PDF
     */
    public function printReport(Request $request)
{
    // mulai query dari model asset
    $query = Asset::query();

    // logika search biar hasil print sesuai ketikan di kolom pencarian
    // ini ngecek nama aset sama serial number nya
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%");
        });
    }

    // logika filter status misal cuma mau ngeprint yang available aja
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // logika sorting biar urutan barisnya sama kaya di website
    if ($request->filled('sort')) {
        if ($request->sort == 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
    } else {
        $query->orderBy('created_at', 'desc');
    }

    // ambil datanya
    $assets = $query->get();

    // load view pdf nya
    // ganti 'pages.report.pdf' sesuai lokasi file blade pdf kamu
    $pdf = Pdf::loadView('pages.report.pdf', [
        'assets' => $assets,
        'selectedStatus' => $request->status // kirim ini kalo mau nampilin status apa yang lagi difilter di judul pdf
    ]);

    return $pdf->stream('Laporan_Aset_IT.pdf');
}
}