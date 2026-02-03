<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;
use Carbon\Carbon;

class ChartController extends Controller
{
    protected $analyticsService;

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
        $returned = AssetRequest::whereNotNull('returned_at')->count();
        $late = AssetRequest::whereNotNull('returned_at')
            ->whereColumn('returned_at', '>', 'return_date')
            ->count();
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

        try {
            $data = $this->analyticsService->getChartData($type, $mode, $startDate, $endDate);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
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

        try {
            $data = $this->analyticsService->getDetailData($type, $startDate, $endDate);
            return response()->json($data);
        } catch (\Exception $e) {
             return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
