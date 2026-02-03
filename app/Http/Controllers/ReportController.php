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
        // LOGIC UMUM: Setup Variabel Dasar
        $logoPath = public_path('img/logoVitechAsia.png');
        $logoBase64 = '';
        try {
            if(file_exists($logoPath)) {
                $logoBase64 = $this->assetService->fileToBase64($logoPath);
            }
        } catch(\Exception $e) { $logoBase64 = ''; }

        $commonData = [
            'date' => now()->setTimezone('Asia/Jakarta')->translatedFormat('d F Y'),
            'printTime' => now()->setTimezone('Asia/Jakarta')->format('H:i'),
            'logoBase64' => $logoBase64,
            'customTitle' => $request->query('custom_title', 'Laporan'),
            'adminNotes' => $request->query('admin_notes', '-'),
            'filterStatus' => ucfirst($request->status == 'all' ? 'Semua Status' : $request->status),
            'orientation' => $request->query('orientation', 'portrait'),
            'search' => $request->search,
        ];

        // --- CABANG 1: LAPORAN PEMINJAMAN ---
        if ($request->type === 'borrowing') {
            $query = AssetRequest::with(['user', 'asset']);

            // Filter Tanggal
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    $startDate . ' 00:00:00', 
                    $endDate . ' 23:59:59'
                ]);
                $commonData['filterDateRange'] = \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y');
            } else {
                $commonData['filterDateRange'] = 'Semua Waktu';
            }

            // Filter Pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('asset', fn($a) => $a->where('name', 'like', "%{$search}%")->orWhere('serial_number', 'like', "%{$search}%"));
                });
            }

            // Filter Status (Jika ada)
            if ($request->filled('status') && $request->status != 'all') {
                // Mapping status jika perlu, atau langsung pakai
                $query->where('status', $request->status);
            }

            $requests = $query->latest();
            
            // [OPTIMISASI] Limit data jika hanya Preview (bukan Download)
            $requests = $requests->get();
            
            $data = array_merge($commonData, [
                'title' => 'Laporan Riwayat Peminjaman',
                'requests' => $requests,
                'isPreview' => $isPreview
            ]);

            $pdf = Pdf::loadView('pdf.borrowing_report', $data)
                      ->setPaper('a4', $request->query('orientation', 'portrait'));

        } else {
            // --- CABANG 2: LAPORAN ASET (DEFAULT) ---
            
            $query = Asset::with('holder');

            // Filter Pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('serial_number', 'like', "%{$search}%");
                });
            }

            // Filter Kategori
            if ($request->filled('category') && $request->category != 'all') {
                $query->where('category', $request->category);
            }

            // Filter Status
            if ($request->filled('status') && $request->status != 'all') {
                $query->where('status', $request->status);
            }

            // Filter Tanggal (Opsional untuk Aset: Berdasarkan Tanggal Masuk/Deploy?)
            // Implementasi: Biasanya Laporan Stok adalah Snapshot saat ini, jadi tanggal range kurang relevan kecuali untuk "Aset Masuk".
            // Namun, jika user mau filter aset yang DIINPUT pada tanggal tertentu:
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            // Sorting
            $sort = $request->query('sort', 'newest');
            switch ($sort) {
                case 'name_asc': $query->orderBy('name', 'asc'); break;
                case 'stock_low': $query->orderBy('quantity', 'asc'); break;
                case 'stock_high': $query->orderBy('quantity', 'desc'); break;
                case 'oldest': $query->oldest(); break;
                case 'status_available': $query->orderByRaw("CASE WHEN status = 'available' THEN 1 ELSE 2 END"); break;
                case 'status_deployed': $query->orderByRaw("CASE WHEN status = 'deployed' THEN 1 ELSE 2 END"); break;
                case 'status_maintenance': $query->orderByRaw("CASE WHEN status = 'maintenance' THEN 1 ELSE 2 END"); break;
                case 'status_broken': $query->orderByRaw("CASE WHEN status = 'broken' THEN 1 ELSE 2 END"); break;
                default: $query->latest(); break;
            }

            // [REVISI] Tampilkan SEMUA data sesuai request user (Unimited)
            $assets = $query->get();

            // Convert Images Logic
            $assets->each(function ($asset) {
                if ($asset->image) {
                    $imagePath = storage_path('app/public/' . $asset->image);
                    if (file_exists($imagePath)) {
                        $asset->image_base64 = $this->assetService->fileToBase64($imagePath);
                    } else {
                        $asset->image_base64 = '';
                    }
                } else {
                    $asset->image_base64 = '';
                }
            });

            $data = array_merge($commonData, [
                'title' => 'Laporan Aset IT',
                'assets' => $assets,
                'filterCategory' => $request->category == 'all' ? 'Semua Kategori' : $request->category,
                'filterSort' => $this->getSortLabel($sort),
                'showImages' => $request->query('show_images', 1),
            ]);

            $pdf = Pdf::loadView('pdf.assets_report', $data)
                      ->setPaper('a4', $request->query('orientation', 'portrait'));
        }

        // OUTPUT
        if ($request->has('download') && $request->download == 1) {
            $prefix = $request->type === 'borrowing' ? 'Laporan_Peminjaman_' : 'Laporan_Aset_';
            $filename = $prefix . now()->format('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        }

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
    /**
     * Export Excel/CSV (Data Only)
     */
    /**
     * Export Excel/CSV (Data Only)
     */
    public function exportExcel(Request $request)
    {
        $format = $request->query('format', 'xlsx');
        $ext = $format === 'csv' ? 'csv' : 'xlsx'; // xlsx for valid OpenXML
        $fileName = ($request->type === 'borrowing' ? 'Laporan_Peminjaman_' : 'Laporan_Aset_') . now()->format('Y-m-d_H-i-s') . '.' . $ext;

        if ($format === 'csv') {
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];
    
            $callback = function() use ($request) {
                $file = fopen('php://output', 'w');
                $this->writeExportData($file, $request, 'csv');
                fclose($file);
            };
    
            return response()->stream($callback, 200, $headers);
        } else {
            // Valid XLSX using Custom Service
            $data = [];
            
            // --- BRANCH 1: BORROWING ---
            if ($request->type === 'borrowing') {
                // Header
                $data[] = ['No', 'Peminjam', 'Aset', 'Serial Number', 'Tanggal Pinjam', 'Tanggal Kembali', 'Status', 'Keperluan'];

                $query = AssetRequest::with(['user', 'asset']);
                
                // Re-apply filters (DRY: Consider refactoring filter logic later)
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                }
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                          ->orWhereHas('asset', fn($a) => $a->where('name', 'like', "%{$search}%")->orWhere('serial_number', 'like', "%{$search}%"));
                    });
                }
                if ($request->filled('status') && $request->status != 'all') {
                    $query->where('status', $request->status);
                }

                $requests = $query->latest()->get();

                foreach ($requests as $index => $req) {
                    $data[] = [
                        $index + 1,
                        $req->user->name ?? 'Unknown',
                        $req->asset->name ?? '-',
                        $req->asset->serial_number ?? '-',
                        $req->created_at->format('d/m/Y H:i'),
                        $req->return_date ? \Carbon\Carbon::parse($req->return_date)->format('d/m/Y') : '-',
                        ucfirst($req->status),
                        $req->notes ?? '-'
                    ];
                }

            } else {
                // --- BRANCH 2: ASSET (Default) ---
                // Header
                $data[] = ['No', 'Nama Aset', 'Serial Number', 'Kategori', 'Lokasi', 'Kondisi', 'Status', 'Stok', 'Pengguna Saat Ini'];

                $query = Asset::with('holder');

                // Filter Logic
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('serial_number', 'like', "%{$search}%");
                    });
                }
                if ($request->filled('category') && $request->category != 'all') {
                    $query->where('category', $request->category);
                }
                if ($request->filled('status') && $request->status != 'all') {
                    $query->where('status', $request->status);
                }
                
                // Sorting
                $sort = $request->query('sort', 'newest');
                switch ($sort) {
                    case 'name_asc': $query->orderBy('name', 'asc'); break;
                    case 'stock_low': $query->orderBy('quantity', 'asc'); break;
                    case 'stock_high': $query->orderBy('quantity', 'desc'); break;
                    case 'oldest': $query->oldest(); break;
                    case 'status_available': $query->orderByRaw("CASE WHEN status = 'available' THEN 1 ELSE 2 END"); break;
                    case 'status_deployed': $query->orderByRaw("CASE WHEN status = 'deployed' THEN 1 ELSE 2 END"); break;
                    case 'status_maintenance': $query->orderByRaw("CASE WHEN status = 'maintenance' THEN 1 ELSE 2 END"); break;
                    case 'status_broken': $query->orderByRaw("CASE WHEN status = 'broken' THEN 1 ELSE 2 END"); break;
                    default: $query->latest(); break;
                }

                $assets = $query->get();

                foreach ($assets as $index => $asset) {
                    $data[] = [
                        $index + 1,
                        $asset->name,
                        $asset->serial_number,
                        $asset->category ?? '-',
                        ($asset->lorong ?? '-') . ' / ' . ($asset->rak ?? '-'),
                        $asset->condition_notes ?? '-',
                        ucfirst($asset->status),
                        $asset->quantity,
                        $asset->holder->name ?? '-'
                    ];
                }
            }

            return \App\Services\SimpleXlsx::download($data, $fileName);
        }
    }
}