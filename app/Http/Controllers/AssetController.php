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
                'listTotal' => Asset::with('holder')->latest()->get(), // Semua Aset
                'listAvailable' => Asset::where('status', 'available')->latest()->get(), // Aset Tersedia
                'listMaintenance' => Asset::whereIn('status', ['maintenance', 'broken'])->with('holder')->latest()->get(), // Aset Rusak
                'listPending' => AssetRequest::with(['user', 'asset'])->where('status', 'pending')->latest()->get(), // Semua Request Pending
                
                // Data untuk Tabel Utama (Limit 5 biar rapi)
                'recentRequests' => AssetRequest::with(['user', 'asset'])->where('status', 'pending')->latest()->take(5)->get(),
                'activities' => AssetHistory::with(['user', 'asset'])->latest()->take(6)->get()
            ]);
        } else {
            // Data untuk Karyawan (Tetap)
            return view('home', [
                'title' => 'Dashboard Karyawan',
                'activeAssetsCount' => Asset::where('user_id', $user->id)->where('status', 'deployed')->count(),
                'pendingRequestsCount' => AssetRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
                'myRequests' => AssetRequest::with('asset')->where('user_id', $user->id)->latest()->take(5)->get(),
                'myActiveAssets' => Asset::where('user_id', $user->id)->latest()->take(3)->get()
            ]);
        }
    }

    // --- UPDATE 1: INDEX (Load data peminjam & tanggal) ---
    public function index(Request $request)
    {
        // Load holder DAN latestApprovedRequest untuk ambil return_date
        $query = Asset::with(['holder', 'latestApprovedRequest'])->latest();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Hapus filter status agar barang deployed tetap terlihat (untuk dibooking)
        // Kita hanya sembunyikan barang rusak parah (opsional)
        // if (auth()->user()->role !== 'admin') {
        //    $query->where('status', '!=', 'broken'); 
        // }

        return view('assets.index', [
            'title' => 'Katalog Aset IT',
            // Gunakan with('holder') agar data peminjam terbawa ke view
            'assets' => Asset::with('holder')->latest()->filter(request(['search', 'status']))->paginate(10)->withQueryString()
        ]);
    }

    public function myAssets()
    {
        return view('assets.my_assets', [
            'title' => 'Aset Saya',
            'assets' => Asset::where('user_id', auth()->id())->latest()->get()
        ]);
    }

    // ... (Method create, store, edit, update, destroy tetap sama) ...
    public function create()
    {
        // LOGIC AUTO-GENERATE SERIAL NUMBER
        // 1. Ambil aset terakhir yang dibuat
        $lastAsset = Asset::latest('id')->first();
        
        // 2. Tentukan nomor urut berikutnya
        if ($lastAsset) {
            // Ambil ID terakhir + 1
            $nextId = $lastAsset->id + 1;
        } else {
            // Jika belum ada data, mulai dari 1
            $nextId = 1;
        }

        // 3. Format: INV-TAHUNBULAN-NOMORURUT (4 digit)
        // Contoh: INV-202401-0005
        $generatedSN = 'INV-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('assets.create', [
            'title' => 'Input Aset Baru',
            'users' => User::all(),
            'suggestedSN' => $generatedSN // Kirim variabel ini ke View
        ]);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'serial_number' => 'required|unique:assets',
            'quantity' => 'required|integer|min:1', // Validasi Baru
            'status' => 'required',
            'user_id' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date',
            'description' => 'nullable',
            'condition_notes' => 'nullable',
            'image' => 'image|file|max:2048',
            'image2' => 'image|file|max:2048',
            'image3' => 'image|file|max:2048',
        ]);

        // Logic Upload Gambar (Sama seperti sebelumnya)
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
    public function edit(Asset $asset) {
        return view('assets.edit', ['title' => 'Edit Data Aset', 'asset' => $asset, 'users' => User::all()]);
    }
    public function update(Request $request, Asset $asset)
    {
        $rules = [
            'name' => 'required|max:255',
            'status' => 'required',
            'quantity' => 'required|integer|min:0',
            'user_id' => 'nullable|exists:users,id',
            'assigned_date' => 'nullable', // Hapus 'date' agar tidak konflik format
            'return_date' => 'nullable',   // Hapus 'date' agar tidak konflik format
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

        // ------------------------------------------------------------------
        // 1. FORMAT TANGGAL MANUAL (FIX ISSUE DATABASE)
        // Mengubah "2024-01-01T14:00" menjadi "2024-01-01 14:00:00"
        // ------------------------------------------------------------------
        if (!empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = \Carbon\Carbon::parse($validatedData['assigned_date'])->format('Y-m-d H:i:s');
        }
        
        if (!empty($validatedData['return_date'])) {
            $validatedData['return_date'] = \Carbon\Carbon::parse($validatedData['return_date'])->format('Y-m-d H:i:s');
        }

        // ------------------------------------------------------------------
        // 2. SMART LOGIC: STATUS OTOMATIS
        // ------------------------------------------------------------------
        
        // Jika Admin pilih User tapi lupa set Status -> Paksa Deployed
        if ($validatedData['user_id'] && $validatedData['status'] == 'available') {
            $validatedData['status'] = 'deployed';
        }

        // Jika Admin hapus User tapi lupa set Status -> Paksa Available
        if (!$validatedData['user_id'] && $validatedData['status'] == 'deployed') {
            $validatedData['status'] = 'available';
        }

        // Jika Status Deployed tapi Tanggal Pinjam Kosong -> Isi Default Sekarang
        if ($validatedData['status'] == 'deployed' && empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = now();
        }

        // Jika Status Available -> Reset Tanggal
        if ($validatedData['status'] == 'available') {
            $validatedData['assigned_date'] = null;
            $validatedData['return_date'] = null;
        }

        // ------------------------------------------------------------------
        // 3. UPLOAD GAMBAR & SIMPAN
        // ------------------------------------------------------------------
        foreach (['image', 'image2', 'image3'] as $key) {
            if ($request->file($key)) {
                if ($asset->$key) Storage::disk('public')->delete($asset->$key);
                $validatedData[$key] = $request->file($key)->store('asset-images', 'public');
            }
        }

        // History Status
        if ($asset->status != $validatedData['status']) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'status_change',
                'notes' => "Status: {$asset->status} -> {$validatedData['status']}"
            ]);
        }

        // History Override
        if ($asset->user_id != $validatedData['user_id']) {
            $newHolder = $validatedData['user_id'] ? \App\Models\User::find($validatedData['user_id'])->name : 'Gudang';
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'manual_override',
                'notes' => "Admin memindahkan aset ke: $newHolder"
            ]);
        }

        $asset->update($validatedData);

        return redirect('/assets')->with('success', 'Data aset berhasil diperbarui!');
    }
    public function destroy(Asset $asset) {
        if ($asset->image) Storage::delete($asset->image);
        if ($asset->image2) Storage::delete($asset->image2);
        if ($asset->image3) Storage::delete($asset->image3);
        $asset->delete();
        return redirect('/assets')->with('success', 'Aset dihapus.');
    }

    // --- UPDATE 2: LOGIC BOOKING (REQUEST ASSET) ---
    public function requestAsset(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        
        // Block request HANYA jika barang Maintenance atau Rusak
        if(in_array($asset->status, ['maintenance', 'broken'])) {
            return back()->with('loginError', 'Aset sedang dalam perbaikan atau rusak.');
        }

        // Cek duplikat request
        $existing = AssetRequest::where('user_id', auth()->id())
                        ->where('asset_id', $id)
                        ->where('status', 'pending')
                        ->first();
        
        if($existing) return back()->with('loginError', 'Anda sudah mengajukan permintaan/booking untuk aset ini.');

        // Tentukan pesan sukses berdasarkan status
        $message = ($asset->status == 'available') 
            ? 'Permintaan peminjaman berhasil dikirim!' 
            : 'Booking berhasil! Anda masuk antrean peminjaman.';

        AssetRequest::create([
            'user_id' => auth()->id(),
            'asset_id' => $id,
            'request_date' => now(),
            'status' => 'pending',
            'reason' => $request->reason ?? 'Permintaan Web',
            'return_date' => $request->return_date
        ]);

        return redirect('/my-assets')->with('success', $message);
    }

    public function approveRequest($requestId)
    {
        $req = AssetRequest::findOrFail($requestId);
        
        // Update status request
        $req->update(['status' => 'approved']);
        
        // Update status aset DAN TANGGALNYA
        $asset = Asset::findOrFail($req->asset_id);
        $asset->update([
            'status' => 'deployed',
            'user_id' => $req->user_id,
            'assigned_date' => now(),           // Set waktu peminjaman sekarang
            'return_date' => $req->return_date  // Ambil dari request user
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'deployed',
            'notes' => "Dipinjamkan ke: " . $req->user->name
        ]);

        return back()->with('success', 'Permintaan disetujui.');
    }
    public function rejectRequest(Request $request, $id)
    {
        $assetRequest = AssetRequest::findOrFail($id);
        
        // Validasi alasan wajib diisi
        $request->validate([
            'admin_note' => 'required|string|max:255'
        ]);

        $assetRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note
        ]);

        // Catat di history
        AssetHistory::create([
            'asset_id' => $assetRequest->asset_id,
            'user_id' => auth()->id(),
            'action' => 'rejected',
            'notes' => "Ditolak: " . $request->admin_note
        ]);

        return back()->with('success', 'Permintaan ditolak dengan alasan yang dikirim.');
    }
    public function printReport(Request $request)
    {
        // 1. Base Query
        $query = Asset::with(['holder', 'latestApprovedRequest'])->orderBy('name');

        // 2. Logic Filter (Status)
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 3. Ambil Data
        $assets = $query->get();

        // 4. Timestamp Realtime (WIB)
        $printTime = now()->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y, H:i:s') . ' WIB';

        // 5. Generate PDF
        $pdf = Pdf::loadView('pdf.assets_report', [
            'assets' => $assets,
            'title' => 'Laporan Aset IT - Vitech Asia',
            'printTime' => $printTime,
            'filterStatus' => $request->status ?? 'Semua Status'
        ]);

        // Set ukuran kertas Landscape agar muat banyak kolom
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-aset-' . date('Y-m-d-His') . '.pdf');
    }
}