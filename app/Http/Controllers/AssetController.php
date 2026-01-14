<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\AssetRequest;
use App\Models\AssetHistory;
use App\Services\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Pastikan Facade ini di-import

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
     * Menampilkan Peta Lokasi Aset
     */
    public function locationMap()
    {
        // Ambil semua aset yang memiliki lokasi (lorong & rak tidak null)
        // Kita select kolom spesifik agar load lebih ringan di JS Map
        $assets = Asset::select(
            'id', 
            'name', 
            'serial_number', 
            'category', 
            'lorong', 
            'rak', 
            'image', 
            'status', 
            'condition_notes', 
            'description'
        )
        ->whereNotNull('lorong')
        ->whereNotNull('rak')
        ->where('lorong', '!=', '')
        ->where('rak', '!=', '')
        ->get();

        return view('assets.map', compact('assets'));
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
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'quantity' => 'required|integer|min:1',
            'purchase_date' => 'required|date',
            'image' => 'nullable|image|max:2048',  // Foto Utama
            'image2' => 'nullable|image|max:2048', // Foto Tambahan 1
            'image3' => 'nullable|image|max:2048', // Foto Tambahan 2
        ]);

        // 2. Generate Serial Number (Logic Tetap Sama)
        $prefix = strtoupper(substr($request->name, 0, 3));
        $lastAsset = Asset::where('serial_number', 'like', $prefix . '-%')
                          ->orderByRaw('CAST(SUBSTRING(serial_number, 5) AS UNSIGNED) DESC')
                          ->first();
        
        $number = $lastAsset ? (int) substr($lastAsset->serial_number, 4) + 1 : 1;
        
        // Perbaikan spasi
        $serialNumber = $prefix . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);

        // 3. Logic Upload Multi Image
        $data = $request->except(['image', 'image2', 'image3']); 
        
        // Handle Image 1
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('assets', 'public');
        }
        // Handle Image 2
        if ($request->hasFile('image2')) {
            $data['image2'] = $request->file('image2')->store('assets', 'public');
        }
        // Handle Image 3
        if ($request->hasFile('image3')) {
            $data['image3'] = $request->file('image3')->store('assets', 'public');
        }

        // Tambahkan data manual lainnya
        $data['serial_number'] = $serialNumber;
        $data['status'] = $request->status ?? 'available';
        
        // Simpan juga lorong & rak terpisah
        $data['lorong'] = $request->lorong;
        $data['rak'] = $request->rak;
        // Lokasi gabungan (Opsional, buat backward compatibility)
        $data['location'] = ($request->lorong ?? '-') . ' - Rak ' . ($request->rak ?? '-');

        // 4. Simpan
        Asset::create($data);

        return redirect()->route('assets.index')->with('success', 'Aset berhasil disimpan! SN: ' . $serialNumber);
    }

    /**
     * Tampilkan Detail Aset
     */
    public function show(Asset $asset)
    {
        return view('assets.detail', [
            'title' => 'Detail Aset - ' . $asset->name,
            'asset' => $asset
        ]);
    }

    /**
     * Form Edit Aset
     */
    public function edit(Asset $asset) {
        return view('assets.edit', ['title' => 'Edit Data Aset', 'asset' => $asset, 'users' => User::all()]);
    }

    /**
     * Update Aset
     */
    public function update(Request $request, Asset $asset)
    {
        // 1. Validasi
        $rules = [
            'name' => 'required|max:255',
            'category' => 'required',
            'status' => 'required',
            'quantity' => 'required|integer|min:0',
            'user_id' => 'nullable|exists:users,id',
            'manual_quantity' => 'nullable|integer|min:1',
            'assigned_date' => 'nullable|date', 
            'return_date' => 'nullable|date',   
            'purchase_date' => 'nullable|date',
            'description' => 'nullable',
            'condition_notes' => 'nullable',
            'rak' => 'nullable|string', 
            'lorong' => 'nullable|string', 
            'image' => 'nullable|image|file|max:2048',
            'image2' => 'nullable|image|file|max:2048',
            'image3' => 'nullable|image|file|max:2048',
        ];

        if ($request->serial_number !== $asset->serial_number) {
            $rules['serial_number'] = 'required|unique:assets';
        }

        $validatedData = $request->validate($rules);

        // 2. Handle Upload Gambar
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

        // 3. Update Format Lokasi Gabungan
        $lorong = $request->lorong ?? '-';
        $rak = $request->rak ?? '-';
        $validatedData['location'] = "$lorong - Rak $rak";

        // 4. Format Tanggal
        if (!empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = \Carbon\Carbon::parse($validatedData['assigned_date'])->format('Y-m-d H:i:s');
        }
        if (!empty($validatedData['return_date'])) {
            $validatedData['return_date'] = \Carbon\Carbon::parse($validatedData['return_date'])->format('Y-m-d H:i:s');
        }

        // Logic Status Available
        if ($validatedData['status'] === 'available') {
            $validatedData['user_id'] = null;
            $validatedData['assigned_date'] = null;
            $validatedData['return_date'] = null;
        }

        // 5. Logic Split Stock (Jika aset dipecah)
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

        // Logic Status Otomatis
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

        // Log History jika status berubah
        if ($asset->status !== $validatedData['status']) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'status_change',
                'notes' => "Status berubah: {$asset->status} → {$validatedData['status']}"
            ]);
        }

        // 6. Simpan Update
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
     */
    public function scanQr(Asset $asset)
    {
        return view('assets.detail', [
            'title' => 'Detail Aset - ' . $asset->name,
            'asset' => $asset
        ]);
    }

    /**
     * Helper untuk Scan QR Image (Return Text/HTML/Image)
     * Digunakan oleh Peta Lokasi
     */
    public function scanQrImage($id)
    {
        if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            // Return raw image PNG response
            $qrCode = QrCode::format('png')->size(200)->margin(1)->generate(route('assets.show', $id));
            return response($qrCode)->header('Content-type','image/png');
        }
        return response('QR Library Missing', 404);
    }

    /**
     * Endpoint untuk data chart di dashboard
     */
    public function chartsData(Request $request)
    {
        $range = $request->query('range', 'monthly'); // default monthly

        // Status counts sekarang
        $statusCounts = [
            'available' => Asset::where('status', 'available')->count(),
            'deployed' => Asset::where('status', 'deployed')->count(),
            'maintenance' => Asset::where('status', 'maintenance')->count(),
            'broken' => Asset::where('status', 'broken')->count(),
            'total' => Asset::count()
        ];

        $series = [
            'labels' => [],
            'data' => []
        ];

        if ($range === 'daily') {
            // last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $day = now()->subDays($i)->format('Y-m-d');
                $series['labels'][] = now()->subDays($i)->format('d M');
                $series['data'][] = Asset::whereDate('created_at', $day)->count();
            }
        } elseif ($range === 'yearly') {
            // last 5 years
            $currentYear = now()->year;
            for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
                $series['labels'][] = (string)$y;
                $series['data'][] = Asset::whereYear('created_at', $y)->count();
            }
        } else {
            // monthly (default) - last 12 months
            for ($m = 11; $m >= 0; $m--) {
                $dt = now()->subMonths($m);
                $series['labels'][] = $dt->format('M Y');
                $series['data'][] = Asset::whereYear('created_at', $dt->year)
                                        ->whereMonth('created_at', $dt->month)
                                        ->count();
            }
        }

        return response()->json([
            'statusCounts' => $statusCounts,
            'series' => $series,
            'range' => $range
        ]);
    }

    /**
     * Data peminjaman (approved + rejected requests) untuk chart
     */
    public function borrowStats(Request $request)
    {
        $range = $request->query('range', 'monthly');

        $series = ['labels' => [], 'approved' => [], 'rejected' => []];

        if ($range === 'hourly') {
            // last 24 hours
            for ($i = 23; $i >= 0; $i--) {
                $hour = now()->subHours($i);
                $series['labels'][] = $hour->format('H:i');
                $series['approved'][] = \App\Models\AssetRequest::whereDate('created_at', $hour->format('Y-m-d'))
                                                                ->whereHour('created_at', $hour->hour)
                                                                ->where('status','approved')
                                                                ->count();
                $series['rejected'][] = \App\Models\AssetRequest::whereDate('created_at', $hour->format('Y-m-d'))
                                                                ->whereHour('created_at', $hour->hour)
                                                                ->where('status','rejected')
                                                                ->count();
            }
        } elseif ($range === 'daily') {
            // last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $day = now()->subDays($i)->format('Y-m-d');
                $series['labels'][] = now()->subDays($i)->format('d M');
                $series['approved'][] = \App\Models\AssetRequest::whereDate('created_at', $day)->where('status','approved')->count();
                $series['rejected'][] = \App\Models\AssetRequest::whereDate('created_at', $day)->where('status','rejected')->count();
            }
        } elseif ($range === 'yearly') {
            $currentYear = now()->year;
            for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
                $series['labels'][] = (string)$y;
                $series['approved'][] = \App\Models\AssetRequest::whereYear('created_at', $y)->where('status','approved')->count();
                $series['rejected'][] = \App\Models\AssetRequest::whereYear('created_at', $y)->where('status','rejected')->count();
            }
        } else {
            // monthly last 12 months
            for ($m = 11; $m >= 0; $m--) {
                $dt = now()->subMonths($m);
                $series['labels'][] = $dt->format('M Y');
                $series['approved'][] = \App\Models\AssetRequest::whereYear('created_at', $dt->year)
                                                                ->whereMonth('created_at', $dt->month)
                                                                ->where('status','approved')
                                                                ->count();
                $series['rejected'][] = \App\Models\AssetRequest::whereYear('created_at', $dt->year)
                                                                ->whereMonth('created_at', $dt->month)
                                                                ->where('status','rejected')
                                                                ->count();
            }
        }

        // Totals
        $totalApproved = \App\Models\AssetRequest::where('status','approved')->count();
        $totalRejected = \App\Models\AssetRequest::where('status','rejected')->count();

        return response()->json([
            'series' => $series,
            'totalApproved' => $totalApproved,
            'totalRejected' => $totalRejected,
            'range' => $range
        ]);
    }

    /**
     * Detail items for a clicked chart point (drilldown)
     */
    public function chartDetails(Request $request)
    {
        $metric = $request->query('metric', 'assets');
        $label = $request->query('label');
        $range = $request->query('range', 'monthly');

        $items = [];

        if ($metric === 'assets') {
            // Parse label depending on range
            if ($range === 'daily') {
                $date = \Carbon\Carbon::createFromFormat('d M', $label)->setYear(now()->year)->format('Y-m-d');
                $assets = Asset::whereDate('created_at', $date)->get();
            } elseif ($range === 'yearly') {
                $year = (int)$label;
                $assets = Asset::whereYear('created_at', $year)->get();
            } else {
                // monthly label like 'Mar 2026'
                $dt = \Carbon\Carbon::createFromFormat('M Y', $label);
                $assets = Asset::whereYear('created_at', $dt->year)->whereMonth('created_at', $dt->month)->get();
            }

            foreach ($assets as $a) {
                $items[] = ['id' => $a->id, 'name' => $a->name, 'sn' => $a->serial_number, 'created_at' => $a->created_at->toDateTimeString()];
            }
        } else {
            // borrows (approved requests)
            if ($range === 'daily') {
                $date = \Carbon\Carbon::createFromFormat('d M', $label)->setYear(now()->year)->format('Y-m-d');
                $reqs = \App\Models\AssetRequest::with('asset','user')->whereDate('created_at', $date)->where('status','approved')->get();
            } elseif ($range === 'yearly') {
                $year = (int)$label;
                $reqs = \App\Models\AssetRequest::with('asset','user')->whereYear('created_at', $year)->where('status','approved')->get();
            } else {
                $dt = \Carbon\Carbon::createFromFormat('M Y', $label);
                $reqs = \App\Models\AssetRequest::with('asset','user')->whereYear('created_at', $dt->year)->whereMonth('created_at', $dt->month)->where('status','approved')->get();
            }

            foreach ($reqs as $r) {
                $items[] = ['id' => $r->id, 'asset' => $r->asset->name ?? '-', 'user' => $r->user->name ?? '-', 'qty' => $r->quantity, 'created_at' => $r->created_at->toDateTimeString()];
            }
        }

        return response()->json(['items' => $items]);
    }
}