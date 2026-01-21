<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\AssetRequest;
use App\Models\AssetHistory;
use App\Services\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class AssetController extends Controller
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    /**
     * Menampilkan Dashboard Utama
     * [MODIFIKASI] Menggabungkan Logic Admin Search Log & Logic Karyawan Baru
     */
    public function dashboard(Request $request)
    {

        $user = auth()->user();
        
        // [FIX] Cek Role Efektif (Prioritaskan Impersonation Session)
        $role = session('impersonate_role', $user->role);

        // Jika Role adalah Admin atau Super Admin, Tampilkan Dashboard Admin
        if (in_array($role, ['admin', 'super_admin'])) {
            
            // --- LOGIC ADMIN (DIPERTAHANKAN) ---
            $logQuery = AssetHistory::with(['user', 'asset']);

            if ($request->has('search_log') && $request->search_log != '') {
                $search = $request->search_log;
                $logQuery->where(function($q) use ($search) {
                    $q->whereHas('user', function($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('asset', function($a) use ($search) {
                        $a->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
                });
            }

            $activities = $logQuery->latest()->paginate(10, ['*'], 'history_page')->withQueryString();

            // Kategori Chart
            $categories = Asset::select('category', DB::raw('count(*) as total'))
                ->groupBy('category')
                ->pluck('total', 'category')
                ->toArray();
            
            // Hitung Broken Assets
            $brokenAssets = Asset::whereIn('status', ['broken', 'missing'])->count();

            return view('home', [
                'title' => 'Dashboard Admin',
                'stats' => [
                    'total' => Asset::count(),
                    'available' => Asset::where('status', 'available')->count(),
                    'deployed' => Asset::where('status', 'deployed')->count(),
                    'maintenance' => Asset::whereIn('status', ['maintenance', 'broken'])->count(),
                    'pending_requests' => AssetRequest::where('status', 'pending')->count(),
                ],
                'listTotal' => Asset::with('holder')->latest()->get(),
                'listAvailable' => Asset::where('status', 'available')->latest()->get(),
                'listDeployed' => Asset::where('status', 'deployed')->with('holder')->latest()->get(),
                'listMaintenance' => Asset::whereIn('status', ['maintenance', 'broken'])->with('holder')->latest()->get(),
                
                'listPending' => AssetRequest::with(['user', 'asset'])->where('status', 'pending')->latest()->get(),
                'recentRequests' => AssetRequest::with(['user', 'asset'])->where('status', 'pending')->latest()->take(5)->get(),
                
                'activities' => $activities,
                'categories' => $categories,
                'totalAssets' => Asset::count(), // Tambahan variabel direct
                'deployedAssets' => Asset::where('status', 'deployed')->count(),
                'maintenanceAssets' => Asset::where('status', 'maintenance')->count(),
                'brokenAssets' => $brokenAssets,
                'recentActivities' => AssetHistory::with(['asset', 'user'])->latest()->take(5)->get() // Versi simple untuk view baru
            ]);

        } elseif ($role == 'service_center') {
             // --- LOGIC SERVICE CENTER ---
             
             // Hitung Stats Maintenance
             $stats = [
                 'on_process' => \App\Models\Maintenance::where('status', 'on_process')->count(),
                 'completed_month' => \App\Models\Maintenance::where('status', 'completed')
                                     ->whereMonth('completion_date', now()->month)
                                     ->whereYear('completion_date', now()->year)
                                     ->count(),
                 'cost_month' => \App\Models\Maintenance::where('status', 'completed')
                                     ->whereMonth('completion_date', now()->month)
                                     ->whereYear('completion_date', now()->year)
                                     ->sum('cost'),
                 'broken' => Asset::where('status', 'broken')->count(),
             ];

             // 5 Aktivitas Terbaru
             $recentMaintenances = \App\Models\Maintenance::with('asset')->latest()->take(5)->get();

             return view('dashboard.service_center_view', [
                 'title' => 'Dashboard Service Center',
                 'stats' => $stats,
                 'recentMaintenances' => $recentMaintenances
             ]);

        } else {
            // --- LOGIC KARYAWAN (DIPERBAIKI) ---
            
            // 1. Hitung Total Unit yang Sedang Dipinjam (Dari AssetRequest)
            $myAssetsCount = AssetRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereNull('returned_at')
                ->sum('quantity');

            // 2. Hitung Permintaan Pending
            $pendingRequests = AssetRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count();

            // 3. Ambil 5 Riwayat Terakhir (untuk Tabel Dashboard Baru)
            $recentActivities = AssetRequest::with('asset')
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            // Variabel lama (myActiveAssets) tetap dikirim untuk kompatibilitas view lama jika ada
            $myActiveAssets = Asset::where('user_id', $user->id)->latest()->take(3)->get(); 

            return view('home', [
                'title' => 'Dashboard Karyawan',
                'myAssetsCount' => $myAssetsCount, // Variabel Baru (Correct Logic)
                'activeAssetsCount' => $myAssetsCount, // Variabel Lama (Alias)
                'pendingRequests' => $pendingRequests, // Variabel Baru
                'pendingRequestsCount' => $pendingRequests, // Variabel Lama (Alias)
                'recentActivities' => $recentActivities, // Untuk Tabel Baru
                'myRequests' => $recentActivities, // Variabel Lama (Alias)
                'myActiveAssets' => $myActiveAssets // Tetap dikirim jaga-jaga
            ]);
        }
    }

    /**
     * Menampilkan Peta Lokasi Aset
     */
    public function locationMap()
    {
        $assets = Asset::select(
            'id', 'name', 'serial_number', 'category', 'lorong', 'rak', 
            'image', 'status', 'condition_notes', 'description'
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
            'category' => $request->category ?? 'all',
            'sort' => $request->sort ?? 'latest'
        ];

        $assets = $this->assetService->buildAssetQuery($filters)->paginate(10)->withQueryString();
        
        $categories = $this->assetService->getCategories();

        return view('assets.index', [
            'title' => 'Katalog Aset IT',
            'assets' => $assets,
            'categories' => $categories
        ]);
    }

    /**
     * Halaman Aset Saya (FIXED: Baca dari tabel Peminjaman)
     */
    public function myAssets()
    {
        // Ambil data dari tabel AssetRequest (Peminjaman Aktif)
        // Syarat: User yang login + Status Approved + Belum dikembalikan
        $myAssets = AssetRequest::with('asset')
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->latest('borrowed_at')
            ->get();

        return view('assets.my_assets', [
            'myAssets' => $myAssets,
            'title' => 'Aset Saya'
        ]);
    }

    /**
     * Form Tambah Aset Baru
     */
    public function create()
    {
        $suggestedSN = $this->assetService->generateSerialNumber();
        $categories = $this->assetService->getCategories();

        return view('assets.create', [
            'title' => 'Input Aset Baru',
            'users' => User::all(),
            'suggestedSN' => $suggestedSN,
            'categories' => $categories
        ]);
    }

    /**
     * Simpan Aset Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'quantity' => 'required|integer|min:1',
            'purchase_date' => 'required|date',
            'purchase_price' => 'nullable|numeric|min:0', // Baru
            'useful_life_years' => 'nullable|integer|min:1', // Baru
            'residual_value' => 'nullable|numeric|min:0', // Baru
            'image' => 'nullable|image|max:2048',
            'image2' => 'nullable|image|max:2048',
            'image3' => 'nullable|image|max:2048',
        ]);

        // 1. GENERATE PREFIX (AAA)
        // Ambil 3 huruf pertama dari nama aset (Uppercase)
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $request->name), 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = strtoupper(str_pad($prefix, 3, 'X')); // Fallback jika nama < 3 huruf
        }

        // 2. CARI URUTAN TERAKHIR (LAST NUMBER)
        $lastAsset = Asset::where('serial_number', 'regexp', "^$prefix-[0-9]{5}$")
                          ->orderBy('serial_number', 'desc')
                          ->first();

        // 3. TENTUKAN START NUMBER
        $startNumber = 1;
        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->serial_number, 4);
            $startNumber = $lastNumber + 1;
        }

        // 4. SIAPKAN DATA UMUM
        // Handle Upload Image Sekali Saja
        $imageData = [];
        if ($request->hasFile('image')) $imageData['image'] = $request->file('image')->store('assets', 'public');
        if ($request->hasFile('image2')) $imageData['image2'] = $request->file('image2')->store('assets', 'public');
        if ($request->hasFile('image3')) $imageData['image3'] = $request->file('image3')->store('assets', 'public');

        $commonData = $request->except(['image', 'image2', 'image3', 'quantity', 'serial_number']);
        $commonData['status'] = $request->status ?? 'available';
        
        // Financial Defaults
        $commonData['purchase_price'] = $request->purchase_price ?? 0;
        $commonData['useful_life_years'] = $request->useful_life_years ?? 4;
        $commonData['residual_value'] = $request->residual_value ?? 0;
        
        $location = ($request->lorong ?? '-') . ' - Rak ' . ($request->rak ?? '-');
        $commonData['lorong'] = $request->lorong;
        $commonData['rak'] = $request->rak;
        $commonData['location'] = $location;

        // Merge Image Paths
        $commonData = array_merge($commonData, $imageData);

        // 5. LOOP UNTUK CREATE DATA MASSAL (BULK INSERT)
        // Jika User input Quantity 10, maka buat 10 record dengan Qty masing-masing 1
        $inputQty = (int) $request->quantity;
        $createdSNs = [];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $inputQty; $i++) {
                $currentNumber = $startNumber + $i;
                $serialNumber = sprintf('%s-%05d', $prefix, $currentNumber);
                
                // Tiap record qty = 1
                $assetData = $commonData;
                $assetData['serial_number'] = $serialNumber;
                $assetData['quantity'] = 1; // FORCE 1
                
                $asset = Asset::create($assetData);
                
                // Catat History
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'user_id' => auth()->id(),
                    'action' => 'created', 
                    'notes' => 'Aset baru ditambahkan (Bulk Insert #' . ($i + 1) . ')'
                ]);

                $createdSNs[] = $serialNumber;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }

        $msg = "Aset berhasil disimpan! Total: $inputQty unit.";
        if ($inputQty == 1) {
            $msg .= " SN: " . $createdSNs[0];
        } else {
            $firstSN = $createdSNs[0];
            $lastSN = end($createdSNs);
            $msg .= " Range SN: $firstSN s/d $lastSN";
        }

        return redirect()->route('assets.index')->with('success', $msg);
    }

    /**
     * Tampilkan Detail Aset
     */
    public function show(Asset $asset)
    {
        return view('assets.detail', [
            'title' => 'Detail Aset - ' . $asset->name,
            'asset' => $asset,
            'history' => $asset->histories()->latest()->get()
        ]);
    }

    /**
     * Form Edit Aset
     */
    public function edit(Asset $asset) {
        $categories = $this->assetService->getCategories();

        return view('assets.edit', [
            'title' => 'Edit Data Aset', 
            'asset' => $asset, 
            'users' => User::all(),
            'categories' => $categories
        ]);
    }

    /**
     * Update Aset
     */
    public function update(Request $request, Asset $asset)
    {
        $rules = [
            'name' => 'required|max:255',
            'category' => 'required',
            'status' => 'required',
            'quantity' => 'required|integer|min:0',
            'user_id' => 'nullable|exists:users,id',
            'manual_quantity' => 'nullable|integer|min:1',
            'assigned_date' => 'nullable|date', 
            'return_date' => 'nullable|date',   
            'assigned_date' => 'nullable|date', 
            'return_date' => 'nullable|date',   
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0', // Baru
            'useful_life_years' => 'nullable|integer|min:1', // Baru
            'residual_value' => 'nullable|numeric|min:0', // Baru
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

        $lorong = $request->lorong ?? '-';
        $rak = $request->rak ?? '-';
        $validatedData['location'] = "$lorong - Rak $rak";

        if (!empty($validatedData['assigned_date'])) {
            $validatedData['assigned_date'] = \Carbon\Carbon::parse($validatedData['assigned_date'])->format('Y-m-d H:i:s');
        }
        if (!empty($validatedData['return_date'])) {
            $validatedData['return_date'] = \Carbon\Carbon::parse($validatedData['return_date'])->format('Y-m-d H:i:s');
        }

        if ($validatedData['status'] === 'available') {
            $validatedData['user_id'] = null;
            $validatedData['assigned_date'] = null;
            $validatedData['return_date'] = null;
        }

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
        if ($asset->status === 'deployed' && $asset->user_id !== null) {
            return redirect()->back()->with('error', 'GAGAL HAPUS: Aset sedang dipinjam user.');
        }

        if ($asset->image) Storage::disk('public')->delete($asset->image);
        if ($asset->image2) Storage::disk('public')->delete($asset->image2);
        if ($asset->image3) Storage::disk('public')->delete($asset->image3);

        // Hapus history terkait
        AssetHistory::where('asset_id', $asset->id)->delete(); 
        
        $asset->delete();

        return redirect('/assets')->with('success', 'Aset berhasil dihapus.');
    }

    /**
     * Scan QR
     */
    public function scanQr(Asset $asset)
    {
        return view('assets.detail', [
            'title' => 'Detail Aset - ' . $asset->name,
            'asset' => $asset
        ]);
    }

    /**
     * Scan QR Image (Helper)
     */
    public function scanQrImage($id)
    {
        // Gunakan API Eksternal yang stabil agar gambar selalu muncul (Tanpa dependensi server)
        $url = route('assets.show', $id);
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($url);
        
        return redirect($qrApiUrl);
    }

    /**
     * Charts Data API (DIPERTAHANKAN)
     */
    public function chartsData(Request $request)
    {
        $range = $request->query('range', 'monthly');
        $endDate = Carbon::now();

        // --- LOGIKA FILTER WAKTU (SAMA SEPERTI DI ATAS) ---
        if ($range === 'daily' || $range === 'weekly') {
            $startDate = $endDate->copy()->subDays(29);
            $groupBy = "DATE(created_at)";
            $dateFormat = "Y-m-d";
            $labelFormat = "d M";
            $step = '1 day';

        } elseif ($range === 'yearly') {
            $endDate = $endDate->copy()->endOfYear();
            $startDate = $endDate->copy()->subYears(4)->startOfYear();
            $groupBy = "YEAR(created_at)";
            $dateFormat = "Y";
            $labelFormat = "Y";
            $step = '1 year';

        } else {
            $endDate = $endDate->copy()->endOfMonth();
            $startDate = $endDate->copy()->subMonths(11)->startOfMonth();
            $groupBy = "DATE_FORMAT(created_at, '%Y-%m')";
            $dateFormat = "Y-m";
            $labelFormat = "M Y";
            $step = '1 month';
        }

        $labels = [];
        $assetData = [];
        
        $period = \Carbon\CarbonPeriod::create($startDate, $step, $endDate);
        foreach ($period as $date) {
            $key = $date->format($dateFormat);
            $labels[] = $date->format($labelFormat);
            $assetData[$key] = 0;
        }

        $assets = \App\Models\Asset::select(
                DB::raw("$groupBy as date_key"),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date_key')
            ->get();

        foreach ($assets as $asset) {
            if (isset($assetData[$asset->date_key])) {
                $assetData[$asset->date_key] = (int) $asset->count;
            }
        }

        // Data Pie Chart (Status) - Tidak Terpengaruh Range
        $statusCounts = \App\Models\Asset::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return response()->json([
            'series' => [
                'labels' => $labels,
                'data' => array_values($assetData),
            ],
            'statusCounts' => [
                'available' => $statusCounts['available'] ?? 0,
                'deployed' => $statusCounts['deployed'] ?? 0,
                'maintenance' => $statusCounts['maintenance'] ?? 0,
                'broken' => ($statusCounts['broken'] ?? 0) + ($statusCounts['lost'] ?? 0),
            ]
        ]);
    }

    /**
     * Borrow Stats API (DIPERTAHANKAN)
     */
    public function borrowStats(Request $request)
    {
        $range = $request->query('range', 'monthly');
        
        $endDate = Carbon::now();
        
        // --- LOGIKA FILTER WAKTU ---
        if ($range === 'daily' || $range === 'weekly') {
            // MODE HARIAN (30 Hari Terakhir)
            // Jika tombol kirim 'daily' atau 'weekly', masuk sini
            $startDate = $endDate->copy()->subDays(29); 
            $groupBy = "DATE(created_at)";
            $dateFormat = "Y-m-d";
            $labelFormat = "d M"; // 20 Jan
            $step = '1 day';

        } elseif ($range === 'yearly') {
            // MODE TAHUNAN (5 Tahun Terakhir)
            $endDate = $endDate->copy()->endOfYear();
            $startDate = $endDate->copy()->subYears(4)->startOfYear();
            $groupBy = "YEAR(created_at)";
            $dateFormat = "Y";
            $labelFormat = "Y"; // 2026
            $step = '1 year';

        } else {
            // MODE BULANAN (12 Bulan Terakhir) - DEFAULT
            $endDate = $endDate->copy()->endOfMonth();
            $startDate = $endDate->copy()->subMonths(11)->startOfMonth();
            $groupBy = "DATE_FORMAT(created_at, '%Y-%m')";
            $dateFormat = "Y-m";
            $labelFormat = "M Y"; // Jan 2026
            $step = '1 month';
        }

        // 1. Siapkan Array Data Kosong (Agar grafik urut & rapi)
        $labels = [];
        $approvedData = [];
        $rejectedData = [];
        
        $period = \Carbon\CarbonPeriod::create($startDate, $step, $endDate);
        foreach ($period as $date) {
            $key = $date->format($dateFormat);
            $labels[] = $date->format($labelFormat);
            $approvedData[$key] = 0;
            $rejectedData[$key] = 0;
        }

        // 2. Query Database
        $requests = \App\Models\AssetRequest::select(
                DB::raw("$groupBy as date_key"),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date_key')
            ->get();

        // 3. Mapping Data ke Array
        foreach ($requests as $req) {
            if (isset($approvedData[$req->date_key])) {
                $approvedData[$req->date_key] = (int) $req->approved_count;
                $rejectedData[$req->date_key] = (int) $req->rejected_count;
            }
        }

        return response()->json([
            'series' => [
                'labels' => $labels,
                'approved' => array_values($approvedData),
                'rejected' => array_values($rejectedData),
            ]
        ]);
    }

    /**
     * Chart Details (DIPERTAHANKAN)
     */
    public function chartDetails(Request $request)
    {
        $metric = $request->query('metric', 'assets');
        $label = $request->query('label');
        $range = $request->query('range', 'monthly');

        $items = [];

        if ($metric === 'assets') {
            if ($range === 'daily') {
                $date = \Carbon\Carbon::createFromFormat('d M', $label)->setYear(now()->year)->format('Y-m-d');
                $assets = Asset::whereDate('created_at', $date)->get();
            } elseif ($range === 'yearly') {
                $year = (int)$label;
                $assets = Asset::whereYear('created_at', $year)->get();
            } else {
                $dt = \Carbon\Carbon::createFromFormat('M Y', $label);
                $assets = Asset::whereYear('created_at', $dt->year)->whereMonth('created_at', $dt->month)->get();
            }

            foreach ($assets as $a) {
                $items[] = ['id' => $a->id, 'name' => $a->name, 'sn' => $a->serial_number, 'created_at' => $a->created_at->toDateTimeString()];
            }
        } else {
            if ($range === 'daily') {
                $date = \Carbon\Carbon::createFromFormat('d M', $label)->setYear(now()->year)->format('Y-m-d');
                $reqs = AssetRequest::with('asset','user')->whereDate('created_at', $date)->where('status','approved')->get();
            } elseif ($range === 'yearly') {
                $year = (int)$label;
                $reqs = AssetRequest::with('asset','user')->whereYear('created_at', $year)->where('status','approved')->get();
            } else {
                $dt = \Carbon\Carbon::createFromFormat('M Y', $label);
                $reqs = AssetRequest::with('asset','user')->whereYear('created_at', $dt->year)->whereMonth('created_at', $dt->month)->where('status','approved')->get();
            }

            foreach ($reqs as $r) {
                $items[] = ['id' => $r->id, 'asset' => $r->asset->name ?? '-', 'user' => $r->user->name ?? '-', 'qty' => $r->quantity, 'created_at' => $r->created_at->toDateTimeString()];
            }
        }

        return response()->json(['items' => $items]);
    }
}