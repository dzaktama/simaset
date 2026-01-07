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
    // ... (Method dashboard tetap sama) ...
    public function dashboard()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return view('home', [
                'title' => 'Dashboard Admin',
                'stats' => [
                    'total' => Asset::count(),
                    'available' => Asset::where('status', 'available')->count(),
                    'deployed' => Asset::where('status', 'deployed')->count(),
                    'maintenance' => Asset::whereIn('status', ['maintenance', 'broken'])->count(),
                    'pending_requests' => AssetRequest::where('status', 'pending')->count(),
                ],
                'recentRequests' => AssetRequest::with(['user', 'asset'])->where('status', 'pending')->latest()->take(5)->get(),
                'activities' => AssetHistory::with(['user', 'asset'])->latest()->take(6)->get()
            ]);
        } else {
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
            'title' => 'Katalog Aset',
            'assets' => $query->paginate(12)->withQueryString(),
            'users' => User::all()
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
    public function create() {
        return view('assets.create', ['title' => 'Input Aset Baru', 'users' => User::all()]);
    }
    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|max:255', 'serial_number' => 'required|unique:assets', 'status' => 'required',
            'user_id' => 'nullable|exists:users,id', 'purchase_date' => 'nullable|date', 'description' => 'nullable', 'condition_notes' => 'nullable',
            'image' => 'image|file|max:2048', 'image2' => 'image|file|max:2048', 'image3' => 'image|file|max:2048',
        ]);
        foreach (['image', 'image2', 'image3'] as $key) { if ($request->file($key)) $validatedData[$key] = $request->file($key)->store('asset-images'); }
        $asset = Asset::create($validatedData);
        AssetHistory::create(['asset_id' => $asset->id, 'user_id' => auth()->id(), 'action' => 'created', 'notes' => 'Aset baru ditambahkan.']);
        return redirect('/assets')->with('success', 'Aset berhasil ditambahkan!');
    }
    public function edit(Asset $asset) {
        return view('assets.edit', ['title' => 'Edit Data Aset', 'asset' => $asset, 'users' => User::all()]);
    }
    public function update(Request $request, Asset $asset) {
        $rules = [
            'name' => 'required|max:255', 'status' => 'required', 'user_id' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date', 'description' => 'nullable', 'condition_notes' => 'nullable',
            'image' => 'image|file|max:2048', 'image2' => 'image|file|max:2048', 'image3' => 'image|file|max:2048',
        ];
        if($request->serial_number != $asset->serial_number) $rules['serial_number'] = 'required|unique:assets';
        $validatedData = $request->validate($rules);
        foreach (['image', 'image2', 'image3'] as $key) {
            if ($request->file($key)) {
                if ($asset->$key) Storage::delete($asset->$key);
                $validatedData[$key] = $request->file($key)->store('asset-images');
            }
        }
        if ($asset->status != $validatedData['status']) {
            AssetHistory::create(['asset_id' => $asset->id, 'user_id' => auth()->id(), 'action' => 'status_change', 'notes' => "Status: {$asset->status} -> {$validatedData['status']}"]);
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

    public function approveRequest($requestId) {
        $req = AssetRequest::findOrFail($requestId);
        $req->update(['status' => 'approved']);
        $asset = Asset::findOrFail($req->asset_id);
        $asset->update(['status' => 'deployed', 'user_id' => $req->user_id]);
        AssetHistory::create(['asset_id' => $asset->id, 'user_id' => auth()->id(), 'action' => 'deployed', 'notes' => "Dipinjamkan ke: " . $req->user->name]);
        return back()->with('success', 'Permintaan disetujui.');
    }
    public function rejectRequest($requestId) {
        AssetRequest::findOrFail($requestId)->update(['status' => 'rejected']);
        return back()->with('success', 'Permintaan ditolak.');
    }
    public function printReport() {
        $pdf = Pdf::loadView('pdf.assets_report', ['assets' => Asset::with('holder')->orderBy('name')->get(), 'title' => 'Laporan Aset']);
        return $pdf->stream('laporan-aset.pdf');
    }
}