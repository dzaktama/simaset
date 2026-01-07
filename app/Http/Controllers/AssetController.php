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
    // DASHBOARD
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $stats = [
                'total' => Asset::count(),
                'available' => Asset::where('status', 'available')->count(),
                'deployed' => Asset::where('status', 'deployed')->count(),
                'maintenance' => Asset::whereIn('status', ['maintenance', 'broken'])->count(),
                'pending_requests' => AssetRequest::where('status', 'pending')->count(),
            ];

            $recentRequests = AssetRequest::with(['user', 'asset'])
                                ->where('status', 'pending')
                                ->latest()
                                ->take(5)
                                ->get();

            $activities = AssetHistory::with(['user', 'asset'])
                            ->latest()
                            ->take(6)
                            ->get();

            return view('home', [
                'title' => 'Dashboard Admin',
                'stats' => $stats,
                'recentRequests' => $recentRequests,
                'activities' => $activities
            ]);
        } else {
            $myAssetsCount = Asset::where('user_id', $user->id)->count();
            $myRequests = AssetRequest::with('asset')
                            ->where('user_id', $user->id)
                            ->latest()
                            ->take(5)
                            ->get();

            return view('home', [
                'title' => 'Dashboard Karyawan',
                'myAssetsCount' => $myAssetsCount,
                'myRequests' => $myRequests
            ]);
        }
    }

    // LIST SEMUA ASET (Untuk Admin & Peminjaman User)
    public function index(Request $request)
    {
        $query = Asset::with('holder')->latest();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%');
        }

        // Karyawan hanya boleh lihat barang AVAILABLE
        if (auth()->user()->role !== 'admin') {
            $query->where('status', 'available');
        }

        return view('assets.index', [
            'title' => 'Daftar Aset',
            'assets' => $query->paginate(10)->withQueryString()
        ]);
    }

    // ASET SAYA (Khusus Karyawan)
    public function myAssets()
    {
        $myAssets = Asset::where('user_id', auth()->id())->latest()->get();

        return view('assets.my_assets', [
            'title' => 'Aset Saya',
            'assets' => $myAssets
        ]);
    }

    // --- CRUD ADMIN ---

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

        $asset = Asset::create($validatedData);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'notes' => 'Aset baru ditambahkan.'
        ]);

        return redirect('/assets')->with('success', 'Aset berhasil ditambahkan!');
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

        if ($asset->status != $validatedData['status']) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'status_change',
                'notes' => "Status diubah: {$asset->status} -> {$validatedData['status']}"
            ]);
        }

        $asset->update($validatedData);
        return redirect('/assets')->with('success', 'Aset berhasil diperbarui!');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->image) Storage::delete($asset->image);
        $asset->delete();
        return redirect('/assets')->with('success', 'Aset dihapus.');
    }

    // --- TRANSAKSI ---

    public function requestAsset(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        if($asset->status != 'available') {
            return back()->with('loginError', 'Aset tidak tersedia.');
        }

        $existingRequest = AssetRequest::where('user_id', auth()->id())
                            ->where('asset_id', $id)
                            ->where('status', 'pending')
                            ->first();
        
        if($existingRequest) {
            return back()->with('loginError', 'Permintaan sudah diajukan sebelumnya.');
        }

        AssetRequest::create([
            'user_id' => auth()->id(),
            'asset_id' => $id,
            'request_date' => now(),
            'status' => 'pending',
            'reason' => 'Permintaan via Web'
        ]);

        return redirect('/my-assets')->with('success', 'Permintaan terkirim!');
    }

    public function approveRequest($requestId)
    {
        $request = AssetRequest::findOrFail($requestId);
        $request->update(['status' => 'approved']);

        $asset = Asset::findOrFail($request->asset_id);
        $asset->update([
            'status' => 'deployed',
            'user_id' => $request->user_id
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'deployed',
            'notes' => "Dipinjamkan ke: " . $request->user->name
        ]);

        AssetRequest::where('asset_id', $asset->id)
                    ->where('id', '!=', $requestId)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);

        return back()->with('success', 'Disetujui.');
    }

    public function rejectRequest($requestId)
    {
        $request = AssetRequest::findOrFail($requestId);
        $request->update(['status' => 'rejected']);
        return back()->with('success', 'Ditolak.');
    }

    public function printReport()
    {
        $assets = Asset::with('holder')->orderBy('name')->get();
        $pdf = Pdf::loadView('pdf.assets_report', [
            'assets' => $assets,
            'title' => 'Laporan Aset'
        ]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('laporan-aset.pdf');
    }
}