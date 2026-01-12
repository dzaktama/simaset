<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\AssetRequest;
use App\Models\AssetHistory;
use App\Services\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }
    /**
     * Menampilkan Dashboard Utama
     */
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Ambil data untuk modal
            $assets = Asset::with('holder')->latest();

            return view('home', [
                'title' => 'Dashboard Admin',
                'stats' => [
                    'total' => Asset::count(),
                    'available' => Asset::where('status', 'available')->count(),
                    'deployed' => Asset::where('status', 'deployed')->count(), // Ini yang kita pakai untuk card ke-3
                    'maintenance' => Asset::whereIn('status', ['maintenance', 'broken'])->count(),
                    'pending_requests' => AssetRequest::where('status', 'pending')->count(), // Tetap disimpan untuk notif
                ],
                // Data List untuk Modal
                'listTotal' => Asset::with('holder')->latest()->get(),
                'listAvailable' => Asset::where('status', 'available')->latest()->get(),
                'listDeployed' => Asset::where('status', 'deployed')->with('holder')->latest()->get(), // List Barang Dipinjam
                'listMaintenance' => Asset::whereIn('status', ['maintenance', 'broken'])->with('holder')->latest()->get(),
                
                // Data Dashboard Lainnya
                'listPending' => AssetRequest::with(['user', 'asset'])->where('status', 'pending')->latest()->get(),
                'recentRequests' => AssetRequest::with(['user', 'asset'])->where('status', 'pending')->latest()->take(5)->get(),
                'activities' => AssetHistory::with(['user', 'asset'])->latest()->take(6)->get()
            ]);
        } else {
            // Dashboard User (Tetap sama)
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
        $filters = [
            'search' => $request->search,
            'status' => $request->status ?? 'all',
            'sort' => $request->sort ?? 'latest'
        ];

        $assets = $this->assetService->buildAssetQuery($filters)->paginate(10)->withQueryString();

        return view('assets.index', [
            'title' => 'Katalog Aset IT',
            'assets' => $assets
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
        $suggestedSN = $this->assetService->generateSerialNumber();

        return view('assets.create', [
            'title' => 'Input Aset Baru',
            'users' => User::all(),
            'suggestedSN' => $suggestedSN
        ]);
    }

    /**
     * Simpan Aset Baru (Admin Menambah Aset)
     * CATATAN: Untuk pengajuan peminjaman karyawan, gunakan AssetRequestController::store()
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'serial_number' => 'required|unique:assets',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:available,deployed,maintenance,broken',
            'description' => 'nullable|string',
            'condition_notes' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'assigned_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'image' => 'nullable|image|file|max:2048',
            'image2' => 'nullable|image|file|max:2048',
            'image3' => 'nullable|image|file|max:2048',
        ]);

        // Handle image uploads
        $requestFiles = [];
        foreach (['image', 'image2', 'image3'] as $key) {
            if ($request->file($key)) {
                $requestFiles[$key] = $request->file($key);
            }
        }

        $validatedData = $this->assetService->handleImageUploads(
            new Asset(),
            $validatedData,
            $requestFiles
        );

        // Format dates
        if (!empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = \Carbon\Carbon::parse($validatedData['assigned_date'])->format('Y-m-d H:i:s');
        }
        if (!empty($validatedData['return_date'])) {
            $validatedData['return_date'] = \Carbon\Carbon::parse($validatedData['return_date'])->format('Y-m-d H:i:s');
        }

        // Force user & dates to null jika status available
        if ($validatedData['status'] === 'available') {
            $validatedData['user_id'] = null;
            $validatedData['assigned_date'] = null;
            $validatedData['return_date'] = null;
        }

        Asset::create($validatedData);

        return redirect('/assets')->with('success', 'Aset baru berhasil ditambahkan!');
    }

    /**
     * Form Edit Aset
     */
    public function edit(Asset $asset) {
        return view('assets.edit', ['title' => 'Edit Data Aset', 'asset' => $asset, 'users' => User::all()]);
    }

    /**
     * Update Aset (Termasuk Smart Logic Status & Tanggal & Split Stock)
     */
    public function update(Request $request, Asset $asset)
    {
        $rules = [
            'name' => 'required|max:255',
            'status' => 'required',
            'quantity' => 'required|integer|min:0',
            'user_id' => 'nullable|exists:users,id',
            'manual_quantity' => 'nullable|integer|min:1',
            'assigned_date' => 'nullable|date', 
            'return_date' => 'nullable|date',   
            'purchase_date' => 'nullable|date',
            'description' => 'nullable',
            'condition_notes' => 'nullable',
            'image' => 'nullable|image|file|max:2048',
            'image2' => 'nullable|image|file|max:2048',
            'image3' => 'nullable|image|file|max:2048',
        ];

        if ($request->serial_number !== $asset->serial_number) {
            $rules['serial_number'] = 'required|unique:assets';
        }

        $validatedData = $request->validate($rules);

        // Handle image uploads
        $requestFiles = [];
        foreach (['image', 'image2', 'image3'] as $key) {
            if ($request->file($key)) {
                $requestFiles[$key] = $request->file($key);
            }
        }

        $validatedData = $this->assetService->handleImageUploads(
            $asset,
            $validatedData,
            $requestFiles
        );

        // Format dates
        if (!empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = \Carbon\Carbon::parse($validatedData['assigned_date'])->format('Y-m-d H:i:s');
        }
        if (!empty($validatedData['return_date'])) {
            $validatedData['return_date'] = \Carbon\Carbon::parse($validatedData['return_date'])->format('Y-m-d H:i:s');
        }

        // Force null jika status available
        if ($validatedData['status'] === 'available') {
            $validatedData['user_id'] = null;
            $validatedData['assigned_date'] = null;
            $validatedData['return_date'] = null;
        }

        // LOGIC: Split Stock
        if ($request->user_id && $request->manual_quantity && $request->manual_quantity < $asset->quantity) {
            try {
                $this->assetService->splitStock(
                    $asset,
                    $request->manual_quantity,
                    $request->user_id,
                    $validatedData['assigned_date'] ?? null,
                    $validatedData['return_date'] ?? null,
                    auth()->id()
                );

                return redirect('/assets')->with('success', 
                    "Berhasil! Stok dipecah: " . ($asset->quantity - $request->manual_quantity) . 
                    " di Gudang, " . $request->manual_quantity . " dipinjam User."
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal split stock: ' . $e->getMessage());
            }
        }

        // LOGIC: Status auto-adjustment
        if ($validatedData['status'] !== 'available') {
            if ($validatedData['user_id'] && $validatedData['status'] === 'available') {
                $validatedData['status'] = 'deployed';
            }
            if (!$validatedData['user_id'] && $validatedData['status'] === 'deployed') {
                $validatedData['status'] = 'available';
            }
        }

        if ($validatedData['status'] === 'deployed' && empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = now();
        }

        // Log status change
        if ($asset->status !== $validatedData['status']) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'status_change',
                'notes' => "Status berubah: {$asset->status} → {$validatedData['status']}"
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
        // Cek Keamanan: Jangan hapus kalo lagi dipinjam orang!
        if ($asset->status === 'deployed' && $asset->user_id !== null) {
            return redirect()->back()->with('error', 'GAGAL HAPUS: Aset sedang dipinjam user. Kembalikan dulu (set Available) baru bisa dihapus.');
        }

        // Hapus gambar fisik
        if ($asset->image) Storage::disk('public')->delete($asset->image);
        if ($asset->image2) Storage::disk('public')->delete($asset->image2);
        if ($asset->image3) Storage::disk('public')->delete($asset->image3);

        $asset->delete();

        return redirect('/assets')->with('success', 'Aset berhasil dihapus.');
    }

    /**
     * [SCAN QR CODE] Tampilkan Detail Aset dari QR
     * Route ini dipanggil saat QR code di-scan
     * Bisa langsung redirect atau tampilkan halaman detail
     */
    public function scanQr(Asset $asset)
    {
        return view('assets.detail', [
            'title' => 'Detail Aset - ' . $asset->name,
            'asset' => $asset
        ]);
    }
}
