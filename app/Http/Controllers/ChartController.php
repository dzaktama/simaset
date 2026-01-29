<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChartController extends Controller
{
    /**
     * Display the Analytics Page
     */
    public function index()
    {
        return view('analytics.index', [
            'title' => 'Pusat Analisis Data'
        ]);
    }

    /**
     * AJAX Endpoint to fetch chart data
     */
    public function getData(Request $request)
    {
        $type = $request->query('type');
        $mode = $request->query('mode', 'month'); // day, month, year
        $startDateReq = $request->query('startDate');
        $endDateReq = $request->query('endDate');

        // Set default dates if not provided
        if ($startDateReq && $endDateReq) {
            $startDate = Carbon::parse($startDateReq)->startOfDay();
            $endDate = Carbon::parse($endDateReq)->endOfDay();
        } else {
            // Default based on mode
            $endDate = now()->endOfDay();
            $startDate = match($mode) {
                'day' => now()->startOfMonth(),
                'month' => now()->startOfYear(),
                'year' => now()->subYears(5),
                default => now()->startOfYear()
            };
        }

        switch ($type) {
            // 1. Biaya Maintenance
            case 'maintenanceCost':
            case 'maintenanceCost':
                // Grouping format based on mode
                $dateFormat = match($mode) {
                    'day' => '%Y-%m-%d',
                    'month' => '%Y-%m',
                    default => '%Y'
                };
                
                $data = Maintenance::selectRaw("DATE_FORMAT(completion_date, '$dateFormat') as date, SUM(cost) as total")
                    ->where('status', 'completed')
                    ->whereBetween('completion_date', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                
                return response()->json([
                    'labels' => $data->pluck('date')->map(function($d) use ($mode) {
                        if($mode == 'day') return Carbon::parse($d)->format('d/m');
                        if($mode == 'month') return Carbon::createFromFormat('Y-m', $d)->translatedFormat('M Y');
                        return $d;
                    }),
                    'data' => $data->pluck('total'),
                    'label' => 'Total Biaya (Rp)'
                ]);

            // 2. Aset Paling Sering Dipinjam (Top 5)
            case 'topAssets':
                $data = AssetRequest::select('asset_id', DB::raw('count(*) as total'))
                    ->where('status', 'approved')
                    ->with('asset')
                    ->groupBy('asset_id')
                    ->orderByDesc('total')
                    ->take(5)
                    ->get();

                return response()->json([
                    'labels' => $data->map(fn($item) => $item->asset->name ?? 'Unknown'),
                    'data' => $data->pluck('total'),
                    'label' => 'Total Peminjaman'
                ]);

            // 3. Kepatuhan Pengembalian (Late vs On Time)
            case 'returnCompliance':
                // Late: Returned > Return Date
                $late = AssetRequest::whereNotNull('returned_at')
                    ->whereColumn('returned_at', '>', 'return_date')
                    ->count();
                // OnTime: Returned <= Return Date
                $onTime = AssetRequest::whereNotNull('returned_at')
                    ->whereColumn('returned_at', '<=', 'return_date')
                    ->count();

                return response()->json([
                    'labels' => ['Tepat Waktu', 'Terlambat'],
                    'data' => [$onTime, $late],
                    'colors' => ['#10B981', '#EF4444']
                ]);

            // 4. Aset Sering Rusak (Maintenance by Category)
            case 'assetReliability':
                // Join Maintenance -> Asset -> Category
                $data = Maintenance::select('assets.category', DB::raw('count(*) as total'))
                    ->join('assets', 'maintenances.asset_id', '=', 'assets.id')
                    ->groupBy('assets.category')
                    ->orderByDesc('total')
                    ->get();

                return response()->json([
                    'labels' => $data->pluck('category'),
                    'data' => $data->pluck('total'),
                    'label' => 'Jumlah Perbaikan'
                ]);

            // 5. Distribusi Aset per Departemen
            case 'departmentDist':
                // Join Asset -> User -> Department
                // Assuming User model has 'department' column. If not, use Role or simple user grouping.
                // Let's check User model first. If no department, we'll group by Role.
                $hasDept = \Schema::hasColumn('users', 'department');
                $col = $hasDept ? 'users.department' : 'users.role';

                $data = Asset::join('users', 'assets.user_id', '=', 'users.id') // Holder
                    ->select($col, DB::raw('count(*) as total'))
                    ->groupBy($col)
                    ->get();

                return response()->json([
                    'labels' => $data->pluck($hasDept ? 'department' : 'role'),
                    'data' => $data->pluck('total'),
                ]);

            // 6. Umur Aset (Aging)
            case 'assetAging':
                $now = now();
                $less1 = Asset::where('purchase_date', '>=', $now->copy()->subYear())->count();
                $oneToThree = Asset::whereBetween('purchase_date', [$now->copy()->subYears(3), $now->copy()->subYear()])->count();
                $moreThree = Asset::where('purchase_date', '<', $now->copy()->subYears(3))->count();

                return response()->json([
                    'labels' => ['< 1 Tahun', '1-3 Tahun', '> 3 Tahun'],
                    'data' => [$less1, $oneToThree, $moreThree],
                    'colors' => ['#3B82F6', '#F59E0B', '#6B7280']
                ]);
            
            // 7. Status Tiket Kerusakan
            case 'ticketStats':
                $data = Maintenance::select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->get();
                
                return response()->json([
                    'labels' => $data->pluck('status')->map(fn($s) => ucfirst(str_replace('_', ' ', $s))),
                    'data' => $data->pluck('total')
                ]);

            // 8. Total Valuasi Aset
            case 'assetValuation':
                // Simple version: Total Purchase Value
                $totalValue = Asset::sum('purchase_price');
                $depreciatedValue = 0; // Placeholder for complex logic
                
                return response()->json([
                    'labels' => ['Nilai Beli', 'Estimasi Depresiasi'],
                    'data' => [$totalValue, $depreciatedValue], // Area chart needs series
                    'singleValue' => $totalValue
                ]);

            // 9. User Paling Aktif
            case 'topUsers':
                $data = AssetRequest::select('user_id', DB::raw('count(*) as total'))
                    ->with('user')
                    ->groupBy('user_id')
                    ->orderByDesc('total')
                    ->take(5)
                    ->get();
                
                return response()->json([
                    'labels' => $data->map(fn($d) => $d->user->name ?? 'Unknown'),
                    'data' => $data->pluck('total')
                ]);

            case 'purchaseTrend':
                 $dateFormat = match($mode) {
                    'day' => '%Y-%m-%d',
                    'month' => '%Y-%m',
                    default => '%Y'
                };

                $data = Asset::selectRaw("DATE_FORMAT(purchase_date, '$dateFormat') as date, COUNT(*) as total")
                    ->whereBetween('purchase_date', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                return response()->json([
                     'labels' => $data->pluck('date')->map(function($d) use ($mode) {
                        if($mode == 'day') return Carbon::parse($d)->format('d/m');
                        if($mode == 'month') return Carbon::createFromFormat('Y-m', $d)->translatedFormat('M Y');
                        return $d;
                    }),
                    'data' => $data->pluck('total'),
                    'label' => 'Unit Dibeli',
                ]);

            case 'borrowingTrend':
                $dateFormat = match($mode) {
                    'day' => '%Y-%m-%d',
                    'month' => '%Y-%m',
                    default => '%Y'
                };

                // Query for Approved
                $approvedData = AssetRequest::selectRaw("DATE_FORMAT(request_date, '$dateFormat') as date, COUNT(*) as total")
                    ->where('status', 'approved')
                    ->whereBetween('request_date', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->pluck('total', 'date');

                // Query for Rejected
                $rejectedData = AssetRequest::selectRaw("DATE_FORMAT(request_date, '$dateFormat') as date, COUNT(*) as total")
                    ->where('status', 'rejected')
                    ->whereBetween('request_date', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->pluck('total', 'date');

                // Generate Date Labels
                $labels = [];
                $approveArr = [];
                $rejectArr = [];

                $period = new \Carbon\CarbonPeriod($startDate, '1 ' . $mode, $endDate);
                foreach ($period as $date) {
                    $key = $date->format(match($mode) { 'day' => 'Y-m-d', 'month' => 'Y-m', default => 'Y' });
                    $label = match($mode) { 
                        'day' => $date->format('d/m'), 
                        'month' => $date->translatedFormat('M Y'), 
                        default => $date->format('Y') 
                    };
                    
                    $labels[] = $label;
                    $approveArr[] = $approvedData[$key] ?? 0;
                    $rejectArr[] = $rejectedData[$key] ?? 0;
                }
                
                return response()->json([
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Disetujui',
                            'data' => $approveArr,
                            'borderColor' => '#10B981', // green-500
                            'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                            'fill' => true,
                            'tension' => 0.4
                        ],
                        [
                            'label' => 'Ditolak',
                            'data' => $rejectArr,
                            'borderColor' => '#EF4444', // red-500
                            'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                            'fill' => true,
                            'tension' => 0.4
                        ]
                    ]
                ]);
        }

        return response()->json(['error' => 'Invalid Request'], 400);
    }

    /**
     * Endpoint for Modal Details (Table Data)
     */
    public function getDetail(Request $request)
    {
        $type = $request->query('type');
        $mode = $request->query('mode', 'month');
        $startDateReq = $request->query('startDate');
        $endDateReq = $request->query('endDate');

         if ($startDateReq && $endDateReq) {
            $startDate = Carbon::parse($startDateReq)->startOfDay();
            $endDate = Carbon::parse($endDateReq)->endOfDay();
        } else {
            $endDate = now()->endOfDay();
            $startDate = match($mode) {
                'day' => now()->startOfMonth(),
                'month' => now()->startOfYear(),
                'year' => now()->subYears(5),
                default => now()->startOfYear()
            };
        }

        $headers = [];
        $rows = [];
        $title = 'Detail Data';

        try {
            switch ($type) {
                case 'maintenanceCost':
                    $title = 'Rincian Biaya Maintenance';
                    $headers = ['Tanggal', 'Aset', 'Masalah', 'Biaya'];
                    $items = Maintenance::with('asset')
                        ->where('status', 'completed')
                        ->where('completion_date', '>=', $startDate)
                        ->orderByDesc('completion_date')
                        ->get();
                    $rows = $items->map(fn($i) => [
                        $i->completion_date ? $i->completion_date->format('d/m/Y') : '-',
                        $i->asset->name ?? '-',
                        Str::limit($i->problem_description, 40),
                        'Rp ' . number_format($i->cost, 0, ',', '.')
                    ]);
                    break;

                case 'topAssets':
                    $title = 'Top 20 Aset Paling Sering Dipinjam';
                    $headers = ['Nama Aset', 'Serial Number', 'Total Peminjaman'];
                    $items = AssetRequest::select('asset_id', DB::raw('count(*) as total'))
                        ->where('status', 'approved')
                        ->with('asset')
                        ->groupBy('asset_id')
                        ->orderByDesc('total')
                        ->take(20)
                        ->get();
                    $rows = $items->map(fn($i) => [
                        $i->asset->name ?? '-',
                        $i->asset->serial_number ?? '-',
                        $i->total . ' Kali'
                    ]);
                    break;

                case 'returnCompliance':
                    $title = 'Daftar Keterlambatan Pengembalian (Terbaru)';
                    $headers = ['Peminjam', 'Aset', 'Jadwal Kembali', 'Dikembalikan', 'Terlambat'];
                    $items = AssetRequest::with(['user', 'asset'])
                        ->whereNotNull('returned_at')
                        ->whereColumn('returned_at', '>', 'return_date')
                        ->latest('returned_at')
                        ->take(50)
                        ->get();
                    $rows = $items->map(fn($i) => [
                        $i->user->name ?? '-',
                        $i->asset->name ?? '-',
                        $i->return_date ? Carbon::parse($i->return_date)->format('d/m/Y') : '-',
                        $i->returned_at ? Carbon::parse($i->returned_at)->format('d/m/Y H:i') : '-',
                        $i->return_date ? Carbon::parse($i->return_date)->diffForHumans($i->returned_at, true) : '-'
                    ]);
                    break;

                case 'assetReliability':
                    $title = 'Riwayat Perbaikan Aset (50 Terakhir)';
                    $headers = ['Tanggal', 'Kategori', 'Aset', 'Masalah'];
                    $items = Maintenance::with('asset')
                        ->where('status', 'completed')
                        ->latest()
                        ->take(50)
                        ->get();
                    $rows = $items->map(fn($i) => [
                        $i->created_at->format('d/m/Y'),
                        $i->asset->category ?? '-',
                        $i->asset->name ?? '-',
                        Str::limit($i->problem_description, 40)
                    ]);
                    break;

                case 'borrowingTrend':
                    $title = 'Riwayat Transaksi Peminjaman (100 Terakhir)';
                    $headers = ['Tanggal Request', 'Peminjam', 'Aset', 'Keperluan'];
                    $items = AssetRequest::with(['user', 'asset'])
                        ->where('status', 'approved')
                        ->latest()
                        ->take(100)
                        ->get();
                    $rows = $items->map(fn($i) => [
                        $i->created_at->format('d/m/Y H:i'),
                        $i->user->name ?? '-',
                        $i->asset->name ?? '-',
                        Str::limit($i->purpose, 40)
                    ]);
                    break;
                
                case 'purchaseTrend':
                    $title = 'Daftar Aset (Diurutkan Tanggal Beli)';
                    $headers = ['Tanggal Beli', 'Nama Aset', 'Harga', 'Status'];
                    $items = Asset::orderByDesc('purchase_date')
                        ->take(100)
                        ->get();
                    $rows = $items->map(fn($i) => [
                        Carbon::parse($i->purchase_date)->format('d/m/Y'),
                        $i->name,
                        'Rp ' . number_format($i->purchase_price, 0, ',', '.'),
                        ucfirst($i->status)
                    ]);
                    break;

                case 'topUsers':
                    $title = 'Peringkat Peminjam Teraktif';
                    $headers = ['Nama User', 'Email', 'Total Peminjaman'];
                    $items = AssetRequest::select('user_id', DB::raw('count(*) as total'))
                         ->where('status', 'approved')
                         ->with('user')
                         ->groupBy('user_id')
                         ->orderByDesc('total')
                         ->take(50)
                         ->get();
                    $rows = $items->map(fn($i) => [
                        $i->user->name ?? '-',
                        $i->user->email ?? '-',
                        $i->total . ' Kali'
                    ]);
                    break;

                case 'assetAging':
                    $title = 'Daftar Aset Berdasarkan Umur (Tua ke Muda)';
                    $headers = ['Nama Aset', 'Tgl Beli', 'Umur (Tahun)', 'Kondisi'];
                    $items = Asset::orderBy('purchase_date')->take(100)->get();
                    $rows = $items->map(fn($i) => [
                         $i->name,
                         Carbon::parse($i->purchase_date)->format('d/m/Y'),
                         Carbon::parse($i->purchase_date)->diffInYears(now()) . ' Tahun',
                         ucfirst($i->condition) // Assuming 'condition' column exists or use status
                    ]);
                    break;
                
                 case 'departmentDist':
                    $title = 'Distribusi Aset per Departemen/Role';
                    $headers = ['Departemen/Role', 'Jumlah Aset'];
                    // Logic similar to getData
                    $hasDept = \Schema::hasColumn('users', 'department');
                    $col = $hasDept ? 'users.department' : 'users.role';
                    $items = Asset::join('users', 'assets.user_id', '=', 'users.id')
                        ->select($col, DB::raw('count(*) as total'))
                        ->groupBy($col)
                        ->orderByDesc('total')
                        ->get();
                    $rows = $items->map(fn($i) => [
                        ucfirst($i->{$hasDept ? 'department' : 'role'}),
                        $i->total . ' Unit'
                    ]);
                    break;

                 case 'ticketStats':
                    $title = 'Status Tiket Perbaikan';
                    $headers = ['Status', 'Jumlah Tiket'];
                    $items = Maintenance::select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')
                        ->get();
                    $rows = $items->map(fn($i) => [
                        ucfirst(str_replace('_', ' ', $i->status)),
                        $i->total . ' Tiket'
                    ]);
                    break;
                
                default:
                    $title = 'Data Detail';
                    $headers = ['Info'];
                    $rows = [['Detail belum tersedia untuk tipe grafik ini.']];
                    break;
            }
        } catch (\Exception $e) {
             return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows
        ]);
    }
}
