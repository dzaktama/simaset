<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ChartController extends Controller
{
    protected $analyticsService;

    private const CACHE_SECONDS = 120;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the Analytics Page
     */
    public function index()
    {
        // 1. Total Asset
        $totalAssets = Asset::count();
        
        // 2. Valuation
        $totalValuation = Asset::sum('purchase_price');

        // 3. Compliance Rate
        // [PERF] Satu query agregat (sebelumnya 2 query COUNT)
        $compliance = AssetRequest::withoutEagerLoads()
            ->whereNotNull('returned_at')
            ->selectRaw('COUNT(*) as returned_total, SUM(CASE WHEN returned_at > return_date THEN 1 ELSE 0 END) as late_total')
            ->first();
        $returned = (int) ($compliance->returned_total ?? 0);
        $late = (int) ($compliance->late_total ?? 0);
        $complianceRate = $returned > 0 ? round((($returned - $late) / $returned) * 100) : 100;

        // 4. Active Tickets
        $activeTickets = Maintenance::whereIn('status', ['pending', 'in_progress'])->count();

        return view('analytics.index', [
            'title' => 'Pusat Analisis Data',
            'totalAssets' => $totalAssets,
            'totalValuation' => $totalValuation,
            'complianceRate' => $complianceRate,
            'activeTickets' => $activeTickets
        ]);
    }

    /**
     * AJAX Endpoint to fetch chart data
     */
    public function getData(Request $request)
    {
        $type = $request->query('type');
        $mode = in_array($request->query('mode'), ['day', 'month', 'year'], true) ? $request->query('mode') : 'month';

        try {
            [$startDate, $endDate] = $this->resolveDateRange($request, $mode);

            // [PERF] Hasil agregasi di-cache singkat: halaman analitik memanggil endpoint ini
            // ~10x sekaligus dan setiap ganti filter. Data analitik tidak harus real-time per detik.
            $cacheKey = 'analytics:' . md5(implode('|', [$type, $mode, $startDate->toDateString(), $endDate->toDateString()]));
            $data = Cache::remember($cacheKey, self::CACHE_SECONDS, fn() =>
                $this->analyticsService->getChartData($type, $mode, $startDate, $endDate)
            );

            return response()->json($data);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 422);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Gagal memuat data grafik.'], 400);
        }
    }

    /**
     * [REFACTOR] Logika rentang tanggal yang sebelumnya diduplikasi di getData() & getDetail()
     */
    private function resolveDateRange(Request $request, string $mode): array
    {
        $startDateReq = $request->query('startDate');
        $endDateReq = $request->query('endDate');

        if ($startDateReq && $endDateReq) {
            $startDate = Carbon::parse($startDateReq)->startOfDay();
            $endDate = Carbon::parse($endDateReq)->endOfDay();
            if ($startDate->gt($endDate)) {
                [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
            }
            return [$startDate, $endDate];
        }

        $endDate = now()->endOfDay();
        $startDate = match($mode) {
            'day' => now()->startOfMonth(),
            'year' => now()->subYears(5)->startOfYear(),
            default => now()->startOfYear(),
        };

        return [$startDate, $endDate];
    }

    /**
     * Endpoint for Modal Details (Table Data)
     */
    public function getDetail(Request $request)
    {
        $type = $request->query('type');
        $mode = in_array($request->query('mode'), ['day', 'month', 'year'], true) ? $request->query('mode') : 'month';

        try {
            [$startDate, $endDate] = $this->resolveDateRange($request, $mode);
            $data = $this->analyticsService->getDetailData($type, $startDate, $endDate);
            return response()->json($data);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 422);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Gagal memuat detail data.'], 500);
        }
    }
}
