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
        // Load holder, latestApprovedRequest, dan enable filter
        $query = Asset::with(['holder', 'latestApprovedRequest'])->latest();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter Status
        if ($request->has('status') && $request->status != 'all') {
             $query->where('status', $request->status);
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
        $rules = [
            'name' => 'required|max:255',
            'status' => 'required',
            'quantity' => 'required|integer|min:0',
            'user_id' => 'nullable|exists:users,id',
            'manual_quantity' => 'nullable|integer|min:1', // Input Baru Poin 12
            'assigned_date' => 'nullable', 
            'return_date' => 'nullable',   
            'purchase_date' => 'nullable|date',
            'description' => 'nullable',
            'condition_notes' => 'nullable',
            'image' => 'image|file|max:2048',
            'image2' => 'image|file|max:2048',
            'image3' => 'image|file|max:2048',
        ];

        if($request->serial_number != $asset->serial_number) {
            $rules['serial_number'] = 'required|unique:assets';
        }

        $validatedData = $request->validate($rules);

        // --- POIN 12: LOGIK SPLIT ASET (JIKA STOK DIPECAH) ---
        // Skenario: Admin pilih User DAN input jumlah pinjam < total stok saat ini
        if ($request->user_id && $request->manual_quantity && $request->manual_quantity < $validatedData['quantity']) {
            
            // 1. Kurangi Stok Aset Utama (Induk)
            $sisaStok = $validatedData['quantity'] - $request->manual_quantity;
            $validatedData['quantity'] = $sisaStok; // Update data untuk aset induk
            $validatedData['user_id'] = null; // Aset induk tetap di gudang/available
            $validatedData['status'] = 'available'; // Pastikan induk tetap available

            // 2. Buat Aset Baru (Pecahan) untuk User
            $newAsset = $asset->replicate(); // Kloning data aset lama
            $newAsset->serial_number = $validatedData['serial_number'] . '-deployed-' . rand(100,999); // Unik SN
            $newAsset->quantity = $request->manual_quantity;
            $newAsset->user_id = $request->user_id;
            $newAsset->status = 'deployed';
            
            // Format Tanggal untuk Aset Baru
            if (!empty($request->assigned_date)) {
                $newAsset->assigned_date = \Carbon\Carbon::parse($request->assigned_date)->format('Y-m-d H:i:s');
            } else {
                $newAsset->assigned_date = now();
            }
            
            if (!empty($request->return_date)) {
                $newAsset->return_date = \Carbon\Carbon::parse($request->return_date)->format('Y-m-d H:i:s');
            }

            $newAsset->save();

            // Log History untuk Aset Baru
            AssetHistory::create([
                'asset_id' => $newAsset->id,
                'user_id' => auth()->id(),
                'action' => 'manual_override',
                'notes' => "Aset dipecah dari induk. Dipinjamkan manual ke user ID: " . $request->user_id
            ]);
            
            // Pesan sukses khusus split
            $splitMessage = "Aset berhasil dipecah! {$request->manual_quantity} unit dipindahkan ke user, sisa {$sisaStok} unit tetap di gudang.";
        }

        // --- LOGIC STANDAR (JIKA TIDAK DIPECAH) ---
        
        // 1. Format Tanggal
        if (!empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = \Carbon\Carbon::parse($validatedData['assigned_date'])->format('Y-m-d H:i:s');
        }
        if (!empty($validatedData['return_date'])) {
            $validatedData['return_date'] = \Carbon\Carbon::parse($validatedData['return_date'])->format('Y-m-d H:i:s');
        }

        // 2. Smart Logic: Status Otomatis (Hanya jika TIDAK terjadi split di atas)
        // Kalau tadi sudah split, $validatedData['user_id'] sudah di-null-kan agar induk aman
        if ($validatedData['user_id'] && $validatedData['status'] == 'available') {
            $validatedData['status'] = 'deployed';
        }
        if (!$validatedData['user_id'] && $validatedData['status'] == 'deployed') {
            $validatedData['status'] = 'available';
        }
        if ($validatedData['status'] == 'deployed' && empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = now();
        }
        if ($validatedData['status'] == 'available') {
            $validatedData['assigned_date'] = null;
            $validatedData['return_date'] = null;
        }

        // 3. Upload Gambar
        foreach (['image', 'image2', 'image3'] as $key) {
            if ($request->file($key)) {
                if ($asset->$key) Storage::disk('public')->delete($asset->$key);
                $validatedData[$key] = $request->file($key)->store('asset-images', 'public');
            }
        }

        // 4. Log History (Induk)
        if ($asset->status != $validatedData['status']) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'status_change',
                'notes' => "Status: {$asset->status} -> {$validatedData['status']}"
            ]);
        }
        
        // Simpan Perubahan pada Aset Induk (Entah itu dikurangi stoknya atau dipindah full)
        $asset->update($validatedData);

        return redirect('/assets')->with('success', $splitMessage ?? 'Data aset berhasil diperbarui!');
    }

    /**
     * Hapus Aset
     */
    public function destroy(Asset $asset) {
        if ($asset->image) Storage::delete($asset->image);
        if ($asset->image2) Storage::delete($asset->image2);
        if ($asset->image3) Storage::delete($asset->image3);
        $asset->delete();
        return redirect('/assets')->with('success', 'Aset dihapus.');
    }

    /**
     * Cetak Laporan PDF
     */
    public function printReport(Request $request)
    {
        $query = Asset::with(['holder', 'latestApprovedRequest'])->orderBy('name');

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $assets = $query->get();
        $printTime = now()->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y, H:i:s') . ' WIB';

        $pdf = Pdf::loadView('pdf.assets_report', [
            'assets' => $assets,
            'title' => 'Laporan Aset IT - Vitech Asia',
            'printTime' => $printTime,
            'filterStatus' => $request->status ?? 'Semua Status'
        ]);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('laporan-aset-' . date('Y-m-d-His') . '.pdf');
    }
}