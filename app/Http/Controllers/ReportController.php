<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Services\AssetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    /**
     * Helper untuk convert sort key ke label yang readable
     */
    private function getSortLabel(string $sort): string
    {
        return match($sort) {
            'newest' => 'Terbaru',
            'oldest' => 'Terlama',
            'stock_low' => 'Stok Minimum',
            'stock_high' => 'Stok Maksimum',
            'name_asc' => 'Nama A-Z',
            'status_available' => 'Status: Available',
            'status_deployed' => 'Status: Deployed',
            'status_maintenance' => 'Status: Maintenance',
            'status_broken' => 'Status: Broken',
            default => ucfirst(str_replace('_', ' ', $sort)),
        };
    }

    /**
     * Halaman Index Laporan (Form + Preview)
     */
    public function index()
    {
        return view('reports.index', [
            'title' => 'Generator Laporan Aset',
            'totalAssets' => Asset::count(),
            'availableAssets' => Asset::where('status', 'available')->count(),
            'deployedAssets' => Asset::where('status', 'deployed')->count(),
        ]);
    }

    /**
     * Laporan Dashboard (Admin)
     */
    public function report(Request $request)
    {
        $assets = Asset::with('holder')->get();
        $requests = AssetRequest::with(['user', 'asset'])->latest()->get();

        $summary = [
            'total_assets' => $assets->count(),
            'available' => $assets->where('status', 'available')->count(),
            'deployed' => $assets->where('status', 'deployed')->count(),
            'maintenance' => $assets->whereIn('status', ['maintenance', 'broken'])->count(),
            'total_requests' => $requests->count(),
            'pending_requests' => $requests->where('status', 'pending')->count(),
        ];

        return view('reports.index', [
            'title' => 'Laporan & Audit Aset',
            'assets' => $assets,
            'requests' => $requests,
            'summary' => $summary
        ]);
    }

    /**
     * Export PDF dengan Preview (via iframe)
     * Menerima parameter: search, status, sort, orientation, custom_title, admin_notes, show_images
     */
    public function exportPdf(Request $request)
    {
        // Build query dengan service
        $filters = [
            'search' => $request->query('search'),
            'status' => $request->query('status', 'all'),
            'sort' => $request->query('sort', 'newest')
        ];

        $assets = $this->assetService->buildAssetQuery($filters)->get();

        // Convert asset images ke base64 untuk consistency
        $assets->each(function ($asset) {
            if ($asset->image) {
                $imagePath = storage_path('app/public/' . $asset->image);
                $asset->image_base64 = $this->assetService->fileToBase64($imagePath);
            } else {
                $asset->image_base64 = '';
            }
        });

        $data = [
            'assets' => $assets,
            'date' => now()->setTimezone('Asia/Jakarta')->translatedFormat('d F Y'),
            'printTime' => now()->setTimezone('Asia/Jakarta')->format('H:i'),
            'title' => 'Laporan Aset IT - ' . now()->setTimezone('Asia/Jakarta')->format('Y-m-d'),
            'filterStatus' => ucfirst($filters['status']),
            'filterSort' => $this->getSortLabel($filters['sort']),
            'filterSearch' => $filters['search'],
            'customTitle' => $request->query('custom_title', 'Laporan Aset IT'),
            'adminNotes' => $request->query('admin_notes', '-'),
            'showImages' => $request->query('show_images', 1),
            'orientation' => $request->query('orientation', 'portrait'),
            // Add base64 logo
            'logoBase64' => $this->assetService->fileToBase64(public_path('img/logoVitechAsia.png')),
            'publicPath' => public_path(),
            'storagePath' => storage_path('app/public')
        ];

        return view('pdf.assets_report', $data);
    }

    /**
     * Cetak Laporan PDF (Direct Download/Stream)
     * Opsional: Jika ingin langsung download PDF (bukan preview di iframe)
     */
    public function printReport(Request $request)
    {
        // Build query dengan service
        $filters = [
            'search' => $request->query('search'),
            'status' => $request->query('status', 'all'),
            'sort' => $request->query('sort', 'newest')
        ];

        $assets = $this->assetService->buildAssetQuery($filters)->get();

        // Convert asset images ke base64 untuk PDF embedding
        $assets->each(function ($asset) {
            if ($asset->image) {
                $imagePath = storage_path('app/public/' . $asset->image);
                $asset->image_base64 = $this->assetService->fileToBase64($imagePath);
            } else {
                $asset->image_base64 = '';
            }
        });

        $data = [
            'assets' => $assets,
            'date' => now()->setTimezone('Asia/Jakarta')->translatedFormat('d F Y'),
            'printTime' => now()->setTimezone('Asia/Jakarta')->format('H:i'),
            'title' => 'Laporan Aset IT',
            'filterStatus' => ucfirst($filters['status']),
            'filterSort' => $this->getSortLabel($filters['sort']),
            'filterSearch' => $filters['search'],
            'customTitle' => $request->query('custom_title', 'Laporan Aset IT'),
            'adminNotes' => $request->query('admin_notes', '-'),
            'showImages' => $request->query('show_images', 1),
            'orientation' => $request->query('orientation', $request->query('orientation', 'landscape')),
            // Add paths for PDF image embedding
            'logoBase64' => $this->assetService->fileToBase64(public_path('img/logoVitechAsia.png')),
            'publicPath' => public_path(),
            'storagePath' => storage_path('app/public')
        ];

        $pdf = Pdf::loadView('pdf.assets_report', $data);
        $pdf->setPaper('a4', $data['orientation']);

        return $pdf->stream('Laporan_Aset_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Download Laporan PDF
     * Langsung download tanpa preview
     */
    public function downloadPdf(Request $request)
    {
        // Build query dengan service
        $filters = [
            'search' => $request->query('search'),
            'status' => $request->query('status', 'all'),
            'sort' => $request->query('sort', 'newest')
        ];

        $assets = $this->assetService->buildAssetQuery($filters)->get();

        // Convert asset images ke base64 untuk PDF embedding
        $assets->each(function ($asset) {
            if ($asset->image) {
                $imagePath = storage_path('app/public/' . $asset->image);
                $asset->image_base64 = $this->assetService->fileToBase64($imagePath);
            } else {
                $asset->image_base64 = '';
            }
        });

        $data = [
            'assets' => $assets,
            'date' => now()->setTimezone('Asia/Jakarta')->translatedFormat('d F Y'),
            'printTime' => now()->setTimezone('Asia/Jakarta')->format('H:i'),
            'title' => 'Laporan Aset IT',
            'filterStatus' => ucfirst($filters['status']),
            'filterSort' => $this->getSortLabel($filters['sort']),
            'filterSearch' => $filters['search'],
            'customTitle' => $request->query('custom_title', 'Laporan Aset IT'),
            'adminNotes' => $request->query('admin_notes', '-'),
            'showImages' => $request->query('show_images', 1),
            'orientation' => $request->query('orientation', 'landscape'),
            // Add paths for PDF image embedding
            'logoBase64' => $this->assetService->fileToBase64(public_path('img/logoVitechAsia.png')),
            'publicPath' => public_path(),
            'storagePath' => storage_path('app/public')
        ];

        $pdf = Pdf::loadView('pdf.assets_report', $data);
        $pdf->setPaper('a4', $data['orientation']);

        // Generate filename dengan timestamp
        $filename = 'Laporan_Aset_' . date('Ymd_His') . '.pdf';

        // Return PDF untuk download (bukan stream)
        return $pdf->download($filename);
    }
}
