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
     * DASHBOARD UTAMA (Logic Admin & Karyawan Dipisah)
     */
    public function dashboard()
    {
        $user = auth()->user();

        // --- LOGIC ADMIN ---
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
                'title' => 'Dashboard Administrator',
                'stats' => $stats,
                'recentRequests' => $recentRequests,
                'activities' => $activities
            ]);
        } 
        
        // --- LOGIC KARYAWAN (IMPROVED) ---
        else {
            // 1. Hitung Aset yang SEDANG DIPEGANG (Active)
            $activeAssetsCount = Asset::where('user_id', $user->id)
                                    ->where('status', 'deployed')
                                    ->count();
            
            // 2. Hitung Permintaan yang masih PENDING
            $pendingRequestsCount = AssetRequest::where('user_id', $user->id)
                                    ->where('status', 'pending')
                                    ->count();

            // 3. Ambil 5 History Request Terakhir
            $myRequests = AssetRequest::with('asset')
                            ->where('user_id', $user->id)
                            ->latest()
                            ->take(5)
                            ->get();

            // 4. Ambil list aset yang dipegang (untuk tabel mini di dashboard)
            $myActiveAssets = Asset::where('user_id', $user->id)
                                ->latest()
                                ->take(3)
                                ->get();

            return view('home', [
                'title' => 'Dashboard Karyawan',
                'activeAssetsCount' => $activeAssetsCount,
                'pendingRequestsCount' => $pendingRequestsCount,
                'myRequests' => $myRequests,
                'myActiveAssets' => $myActiveAssets
            ]);
        }
    }

    /**
     * HALAMAN KATALOG ASET (Untuk Menu "Pinjam Aset Baru")
     */
    public function index(Request $request)
    {
        // Tambahkan 'activeLoan' agar kita bisa ambil tanggal kembali
        $query = Asset::with(['holder', 'activeLoan'])->latest();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%');
        }

        // gadipake dulu biar gampang testing
        // if (auth()->user()->role !== 'admin') {
        //    $query->where('status', 'available');
        // }

        return view('assets.index', [
            'title' => 'Katalog Aset IT',
            'assets' => $query->paginate(10)->withQueryString(),
            'users' => User::all()
        ]);
    }

    /**
     * HALAMAN ASET SAYA (Detail Barang yg Dipegang)
     */
    public function myAssets()
    {
        $myAssets = Asset::where('user_id', auth()->id())->latest()->get();

        return view('assets.my_assets', [
            'title' => 'Inventaris Saya',
            'assets' => $myAssets
        ]);
    }

    // --- LOGIC CRUD & REQUEST (Tetap sama, fungsi inti) ---

    public function create() {
        return view('assets.create', ['title' => 'Input Aset Baru', 'users' => User::all()]);
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
            'condition_notes' => 'nullable', // Tambahan field baru
            'image' => 'image|file|max:2048',
            'image2' => 'image|file|max:2048', // Tambahan foto
            'image3' => 'image|file|max:2048', // Tambahan foto
        ]);

        // Logic simpan gambar (Looping biar rapi)
        $images = ['image', 'image2', 'image3'];
        foreach ($images as $imgField) {
            if ($request->file($imgField)) {
                $validatedData[$imgField] = $request->file($imgField)->store('asset-images');
            }
        }

        $asset = Asset::create($validatedData);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'notes' => 'Aset baru ditambahkan dengan detail lengkap.'
        ]);

        return redirect('/assets')->with('success', 'Aset berhasil ditambahkan!');
    }

    public function edit(Asset $asset)
    {
        // PERBAIKAN ERROR UNDEFINED VARIABLE $user
        // Masalahnya di view edit.blade.php memanggil $user tapi controller tidak kirim.
        // Kita kirim data semua user untuk dropdown "Holder"
        
        return view('assets.edit', [
            'title' => 'Edit Data Aset',
            'asset' => $asset,
            'users' => User::all() // Ini variabel $users (jamak)
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
            'condition_notes' => 'nullable',
            'image' => 'image|file|max:2048',
            'image2' => 'image|file|max:2048',
            'image3' => 'image|file|max:2048',
        ];

        if($request->serial_number != $asset->serial_number) {
            $rules['serial_number'] = 'required|unique:assets';
        }

        $validatedData = $request->validate($rules);

        // Update Gambar (Hapus lama jika ada baru)
        $images = ['image', 'image2', 'image3'];
        foreach ($images as $imgField) {
            if ($request->file($imgField)) {
                if ($asset->$imgField) {
                    Storage::delete($asset->$imgField); // Hapus foto lama
                }
                $validatedData[$imgField] = $request->file($imgField)->store('asset-images');
            }
        }

        // Catat history jika status berubah
        if ($asset->status != $validatedData['status']) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'status_change',
                'notes' => "Status diupdate: {$asset->status} -> {$validatedData['status']}"
            ]);
        }

        $asset->update($validatedData);
        return redirect('/assets')->with('success', 'Data aset berhasil diperbarui!');
    }

    public function destroy(Asset $asset) {
        if ($asset->image) Storage::delete($asset->image);
        $asset->delete();
        return redirect('/assets')->with('success', 'Aset dihapus.');
    }

    public function requestAsset(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        
        // REVISI LOGIKA: Barang Maintenance/Broken gabisa dipinjam. 
        // Tapi Available/Deployed BISA (Deployed = Booking).
        if(in_array($asset->status, ['maintenance', 'broken'])) {
            return back()->with('loginError', 'Maaf, aset sedang dalam perbaikan/rusak.');
        }

        // Cek double request
        $existingRequest = AssetRequest::where('user_id', auth()->id())
                            ->where('asset_id', $id)
                            ->where('status', 'pending')
                            ->first();
        
        if($existingRequest) {
            return back()->with('loginError', 'Anda sudah mengajukan permintaan untuk aset ini.');
        }

        $request->validate([
            'return_date' => 'nullable|date|after:today',
            'reason' => 'required|string|max:255',
        ]);

        // Tentukan tipe request di notes/reason jika barang sedang dipinjam
        $reason = $request->reason;
        if($asset->status == 'deployed') {
            $reason = "[BOOKING ANTREAN] " . $reason;
        }

        AssetRequest::create([
            'user_id' => auth()->id(),
            'asset_id' => $id,
            'request_date' => now(),
            'return_date' => $request->return_date,
            'status' => 'pending',
            'reason' => $reason
        ]);

        return redirect('/my-assets')->with('success', 'Permintaan berhasil dikirim! Admin akan memproses antrean Anda.');
    }

    public function approveRequest($requestId) {
        $request = AssetRequest::findOrFail($requestId);
        $request->update(['status' => 'approved']);
        $asset = Asset::findOrFail($request->asset_id);
        $asset->update(['status' => 'deployed', 'user_id' => $request->user_id]);
        AssetHistory::create(['asset_id' => $asset->id, 'user_id' => auth()->id(), 'action' => 'deployed', 'notes' => "Dipinjamkan ke: " . $request->user->name]);
        AssetRequest::where('asset_id', $asset->id)->where('id', '!=', $requestId)->where('status', 'pending')->update(['status' => 'rejected']);
        return back()->with('success', 'Permintaan disetujui.');
    }

    public function rejectRequest($requestId) {
        $request = AssetRequest::findOrFail($requestId);
        $request->update(['status' => 'rejected']);
        return back()->with('success', 'Permintaan ditolak.');
    }

    public function printReport() {
        $assets = Asset::with('holder')->orderBy('name')->get();
        $pdf = Pdf::loadView('pdf.assets_report', ['assets' => $assets, 'title' => 'Laporan Aset']);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('laporan-aset-vitech.pdf');
    }
}