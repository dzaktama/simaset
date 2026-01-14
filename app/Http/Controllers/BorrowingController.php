<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\Asset;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    /**
     * Daftar peminjaman (active & returned)
     */
    public function index(Request $request)
    {
        $query = AssetRequest::with(['user', 'asset']);

        // Filter by search
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhereHas('asset', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            });
        }

        // Filter by status
        if ($request->borrowing_status === 'active') {
            $query->where('status', 'approved')->where('borrowed_at', '!=', null)->where('returned_at', null);
        } elseif ($request->borrowing_status === 'returned') {
            $query->where('returned_at', '!=', null);
        } elseif ($request->borrowing_status === 'rejected') {
            $query->where('status', 'rejected');
        }

        // Sort
        $sort = $request->sort ?? 'newest';
        if ($sort === 'newest') {
            $query->latest('created_at');
        } else {
            $query->oldest('created_at');
        }


        // Ambil data dan hitung status dinamis (agar sama dengan detail)
        $borrowings = $query->paginate(15)->appends($request->query());
        // Map status logic ke setiap item
        $borrowings->getCollection()->transform(function($item) {
            $status = 'pending';
            if ($item->status === 'rejected') {
                $status = 'rejected';
            } elseif ($item->status === 'approved' && $item->returned_at) {
                $status = 'returned';
            } elseif ($item->status === 'approved' && !$item->returned_at) {
                $status = 'active';
            }
            $item->borrowing_status = $status;
            return $item;
        });

        // Statistics
        $statistics = [
            'total' => AssetRequest::count(),
            'active' => AssetRequest::where('status', 'approved')->where('returned_at', null)->count(),
            'pending' => AssetRequest::where('status', 'pending')->count(),
            'returned' => AssetRequest::where('returned_at', '!=', null)->count(),
        ];

        return view('borrowing.index', [
            'borrowings' => $borrowings,
            'statistics' => $statistics
        ]);
    }

    /**
     * Detail peminjaman
     */
    public function show($id)
    {
        $borrowing = AssetRequest::with(['user', 'asset'])->findOrFail($id);

        // ---[BAGIAN BARU: PERBAIKAN LOGIC DURASI]---
        Carbon::setLocale('id'); // Paksa bahasa Indonesia
        
        // Hitung Total Durasi (Rencana Pinjam)
        $start = Carbon::parse($borrowing->created_at);
        $end   = $borrowing->return_date ? Carbon::parse($borrowing->return_date) : null;
        
        $totalDurasi = '-';
        if($end) {
             // Syntax 3 bagian (Hari, Jam, Menit) agar detail
            $totalDurasi = $start->diffForHumans($end, [
                'parts' => 3, 
                'join' => true, 
                'syntax' => Carbon::DIFF_ABSOLUTE
            ]);
        }

        // Hitung Sisa Waktu / Keterlambatan
        $sisaWaktu = '';
        $isOverdue = false;
        $now = Carbon::now();

        if ($borrowing->status === 'approved' && !$borrowing->returned_at && $end) {
            if ($now->greaterThan($end)) {
                // Telat
                $isOverdue = true;
                $sisaWaktu = 'Terlambat ' . $end->diffForHumans($now, [
                    'parts' => 2,
                    'join' => true, 
                    'syntax' => Carbon::DIFF_ABSOLUTE
                ]);
            } else {
                // Masih jalan (Countdown)
                $sisaWaktu = $now->diffForHumans($end, [
                    'parts' => 3, 
                    'join' => true, 
                    'syntax' => Carbon::DIFF_ABSOLUTE
                ]);
            }
        } elseif ($borrowing->returned_at) {
            $sisaWaktu = 'Selesai (Dikembalikan)';
        }

        // Ambil history
        $history = AssetHistory::where('asset_id', $borrowing->asset_id)
            ->latest()
            ->take(10)
            ->get();

        // Status Logic (Biarkan sama, cuma dirapikan)
        $status = 'pending';
        if ($borrowing->status === 'rejected') $status = 'rejected';
        elseif ($borrowing->status === 'approved' && $borrowing->returned_at) $status = 'returned';
        elseif ($borrowing->status === 'approved' && !$borrowing->returned_at) $status = 'active';

        return view('borrowing.show', [
            'borrowing' => $borrowing,
            'borrowing_status' => $status,
            'history' => $history,
            'totalDurasi' => $totalDurasi, // Kirim ke view
            'sisaWaktu' => $sisaWaktu,     // Kirim ke view
            'isOverdue' => $isOverdue      // Kirim ke view
        ]);
    }

    // ---[FITUR BARU: QUICK APPROVE]---
    public function quickApprove($id)
    {
        $request = AssetRequest::findOrFail($id);
        
        if($request->status == 'pending') {
            $request->status = 'approved';
            $request->admin_note = 'Disetujui Cepat via Timeline';
            $request->approved_at = now(); // Catat waktu setuju
            $request->save();
            
            // Catat History
            AssetHistory::create([
                'asset_id' => $request->asset_id,
                'user_id' => auth()->id(),
                'action' => 'check_out', 
                'notes' => 'Peminjaman disetujui admin (Quick Action)',
                'date' => now()
            ]);
            
            return back()->with('success', 'Peminjaman berhasil disetujui!');
        }
        return back()->with('error', 'Status tidak valid');
    }

    /**
     * Riwayat peminjaman user tertentu
     */
    public function userHistory($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        $borrowings = AssetRequest::where('user_id', $userId)
            ->with('asset')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_borrowings' => AssetRequest::where('user_id', $userId)->count(),
            'active_borrowings' => AssetRequest::where('user_id', $userId)
                ->where('status', 'approved')
                ->where('returned_at', null)
                ->count(),
            'returned_borrowings' => AssetRequest::where('user_id', $userId)
                ->where('returned_at', '!=', null)
                ->count(),
        ];

        return view('borrowing.user-history', [
            'user' => $user,
            'borrowings' => $borrowings,
            'stats' => $stats
        ]);
    }

    /**
     * Kembalikan aset (mark as returned)
     */
    public function return(Request $request, $id)
    {
        $borrowing = AssetRequest::findOrFail($id);

        $request->validate([
            'condition' => 'required|in:good,minor_damage,major_damage',
            'notes' => 'nullable|string|max:500'
        ]);

        // Update peminjaman
        $borrowing->update([
            'returned_at' => Carbon::now(),
            'condition' => $request->condition,
            'return_notes' => $request->notes,
            'status' => 'approved' // Mark as completed
        ]);

        // Update asset status
        $borrowing->asset->update(['status' => 'available']);

        // Create history record
        AssetHistory::create([
            'asset_id' => $borrowing->asset_id,
            'user_id' => auth()->id(),
            'action' => 'returned',
            'notes' => 'Aset dikembalikan. Kondisi: ' . $request->condition . '. Catatan: ' . ($request->notes ?? '-')
        ]);

        return redirect()->route('borrowing.show', $id)->with('success', 'Aset berhasil dikembalikan');
    }

    /**
     * Laporan peminjaman dengan filter
     */
    public function report(Request $request)
    {
        $query = AssetRequest::with(['user', 'asset']);

        // Date filter
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->status && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('status', 'approved')->where('returned_at', null);
            } elseif ($request->status === 'returned') {
                $query->where('returned_at', '!=', null);
            }
        }

        // User filter
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $borrowings = $query->latest()->get();

        // Summary
        $summary = [
            'total' => $borrowings->count(),
            'active' => $borrowings->where('status', 'approved')->where('returned_at', null)->count(),
            'returned' => $borrowings->where('returned_at', '!=', null)->count(),
        ];

        // Users for filter dropdown
        $users = \App\Models\User::orderBy('name')->get();

        return view('borrowing.report', [
            'borrowings' => $borrowings,
            'summary' => $summary,
            'users' => $users,
            'filters' => $request->all()
        ]);
    }

    /**
     * Export laporan ke Excel/CSV
     */
    public function exportExcel(Request $request)
    {
        $query = AssetRequest::with(['user', 'asset']);

        // Apply same filters as report
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->status && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('status', 'approved')->where('returned_at', null);
            } elseif ($request->status === 'returned') {
                $query->where('returned_at', '!=', null);
            }
        }
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $borrowings = $query->latest()->get();

        // Create CSV
        $filename = 'laporan-peminjaman-' . date('Y-m-d-H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        
        // BOM untuk UTF-8 di Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($output, [
            'No',
            'Peminjam',
            'Email',
            'Aset',
            'Tanggal Peminjaman',
            'Tanggal Kembali',
            'Status',
            'Kondisi',
            'Durasi (Hari)',
            'Catatan'
        ], ';');

        // Data rows
        foreach ($borrowings as $index => $borrowing) {
            $duration = '-';
            if ($borrowing->returned_at) {
                $duration = Carbon::parse($borrowing->returned_at)
                    ->diffInDays(Carbon::parse($borrowing->created_at));
            }

            fputcsv($output, [
                $index + 1,
                $borrowing->user->name ?? '-',
                $borrowing->user->email ?? '-',
                $borrowing->asset->name ?? '-',
                Carbon::parse($borrowing->created_at)->format('d-m-Y H:i'),
                $borrowing->returned_at ? Carbon::parse($borrowing->returned_at)->format('d-m-Y H:i') : '-',
                ucfirst($borrowing->status),
                $borrowing->condition ? str_replace('_', ' ', ucfirst($borrowing->condition)) : '-',
                $duration,
                $borrowing->return_notes ?? '-'
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * API endpoint untuk statistik
     */
    public function stats()
    {
        // Top borrowed items
        $topItems = AssetRequest::with('asset')
            ->selectRaw('asset_id, count(*) as count')
            ->where('status', 'approved')
            ->groupBy('asset_id')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'asset_name' => $item->asset->name ?? 'Unknown',
                    'borrow_count' => $item->count
                ];
            });

        // Top borrowers
        $topBorrowers = AssetRequest::with('user')
            ->selectRaw('user_id, count(*) as count')
            ->where('status', 'approved')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'user_name' => $item->user->name ?? 'Unknown',
                    'borrow_count' => $item->count
                ];
            });

        return response()->json([
            'total_borrowings' => AssetRequest::where('status', 'approved')->count(),
            'active_borrowings' => AssetRequest::where('status', 'approved')
                ->where('returned_at', null)
                ->count(),
            'returned_borrowings' => AssetRequest::where('returned_at', '!=', null)->count(),
            'top_items' => $topItems,
            'top_borrowers' => $topBorrowers
        ]);
    }
}
