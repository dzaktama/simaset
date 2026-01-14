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
        // [FITUR BARU] Ambil kategori unik untuk filter dropdown
        $categories = Asset::select('category')
                           ->distinct()
                           ->whereNotNull('category')
                           ->where('category', '!=', '')
                           ->orderBy('category')
                           ->pluck('category');

        return view('reports.index', [
            'title' => 'Generator Laporan Aset',
            'categories' => $categories, // Dikirim ke view
            'totalAssets' => Asset::count(),
            'availableAssets' => Asset::where('status', 'available')->count(),
            'deployedAssets' => Asset::where('status', 'deployed')->count(),
        ]);
    }

    /**
     * Laporan Dashboard (Admin) - FUNGSI INI TETAP ADA (TIDAK DIHAPUS)
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
        // 1. QUERY DATA
        $query = Asset::with('holder');

        // Filter Pencarian (Nama / SN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Filter Kategori (BARU)
        if ($request->filled('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        // Filter Status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Sorting (Logika Lengkap sesuai request)
        $sort = $request->query('sort', 'newest');
        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'stock_low': $query->orderBy('quantity', 'asc'); break;
            case 'stock_high': $query->orderBy('quantity', 'desc'); break;
            case 'oldest': $query->oldest(); break;
            // Sorting berdasarkan prioritas status
            case 'status_available': $query->orderByRaw("CASE WHEN status = 'available' THEN 1 ELSE 2 END"); break;
            case 'status_deployed': $query->orderByRaw("CASE WHEN status = 'deployed' THEN 1 ELSE 2 END"); break;
            case 'status_maintenance': $query->orderByRaw("CASE WHEN status = 'maintenance' THEN 1 ELSE 2 END"); break;
            case 'status_broken': $query->orderByRaw("CASE WHEN status = 'broken' THEN 1 ELSE 2 END"); break;
            default: $query->latest(); break; // newest
        }

        $assets = $query->get();

        // 2. Convert Images to Base64 (Wajib agar muncul di PDF/Print)
        $assets->each(function ($asset) {
            if ($asset->image) {
                // Cek path, prioritas storage public
                $imagePath = storage_path('app/public/' . $asset->image);
                if (file_exists($imagePath)) {
                    $asset->image_base64 = $this->assetService->fileToBase64($imagePath);
                } else {
                    // Fallback jika pakai symbolic link atau path lain
                    $asset->image_base64 = ''; // Kosongkan jika file fisik tidak ada
                }
            } else {
                $asset->image_base64 = '';
            }
        });

        // 3. Siapkan Data untuk View
        $data = [
            'assets' => $assets,
            'date' => now()->setTimezone('Asia/Jakarta')->translatedFormat('d F Y'),
            'printTime' => now()->setTimezone('Asia/Jakarta')->format('H:i'),
            'title' => 'Laporan Aset IT',
            
            // Variabel Filter & Config
            'filterCategory' => $request->category == 'all' ? 'Semua Kategori' : $request->category,
            'filterStatus' => ucfirst($request->status ?? 'Semua'),
            'filterSort' => $this->getSortLabel($sort),
            'filterSearch' => $request->search,
            'search' => $request->search, // Cadangan agar view tidak error
            
            'customTitle' => $request->query('custom_title', 'Laporan Aset IT'),
            'adminNotes' => $request->query('admin_notes', '-'),
            'showImages' => $request->query('show_images', 1),
            'orientation' => $request->query('orientation', 'portrait'),
            
            // Helper Path Gambar Statis (Logo)
            'logoBase64' => $this->assetService->fileToBase64(public_path('img/logoVitechAsia.png')),
            'publicPath' => public_path(),
            'storagePath' => storage_path('app/public')
        ];

        $pdf = Pdf::loadView('pdf.assets_report', $data)
                  ->setPaper('a4', $data['orientation']);

        // 4. Logic Download vs Preview
        // Jika ada parameter 'download=1', paksa download file.
        if ($request->has('download') && $request->download == 1) {
            $filename = 'Laporan_Aset_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        }

        // Default: Stream (Preview di Iframe)
        return $pdf->stream('preview.pdf');
    }

    /**
     * Cetak Laporan PDF (Direct Download/Stream) - FUNGSI LAMA TETAP ADA
     */
    public function printReport(Request $request)
    {
        return $this->exportPdf($request); // Redirect ke logic utama biar tidak duplikat
    }

    /**
     * Download Laporan PDF - FUNGSI LAMA TETAP ADA
     */
    public function downloadPdf(Request $request)
    {
        // Paksa mode download
        $request->merge(['download' => 1]);
        return $this->exportPdf($request);
    }
}