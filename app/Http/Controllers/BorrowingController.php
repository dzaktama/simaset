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

        // Ambil data
        $borrowings = $query->paginate(15)->appends($request->query());
        
        // Map status logic
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
     * Simpan Pengajuan Peminjaman (Store)
     * [PERBAIKAN] Menambahkan 'request_date'
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'quantity' => 'required|integer|min:1',
            'return_date' => 'nullable|date|after_or_equal:today',
            'reason' => 'required|string|max:255',
        ]);

        $asset = Asset::findOrFail($request->asset_id);

        // Cek ketersediaan stok
        if ($asset->quantity < $request->quantity) {
            return back()->with('error', 'Maaf, stok aset tidak mencukupi untuk jumlah yang diminta.')->withInput();
        }

        // Simpan Permintaan ke Database
        AssetRequest::create([
            'user_id' => auth()->id(),
            'asset_id' => $request->asset_id,
            'quantity' => $request->quantity,
            'request_date' => now(), // [TAMBAHAN] Wajib diisi sesuai error log
            'return_date' => $request->return_date,
            'reason' => $request->reason,
            'status' => 'pending', 
        ]);

        // Redirect ke halaman Riwayat Peminjaman User
        return redirect()->route('borrowing.history')->with('success', 'Pengajuan peminjaman berhasil dikirim. Menunggu persetujuan admin.');
    }

    /**
     * Detail peminjaman
     */
    public function show($id)
    {
        $borrowing = AssetRequest::with(['user', 'asset'])->findOrFail($id);

        Carbon::setLocale('id'); 
        
        // Hitung Total Durasi
        $start = Carbon::parse($borrowing->created_at);
        $end   = $borrowing->return_date ? Carbon::parse($borrowing->return_date) : null;
        
        $totalDurasi = '-';
        if($end) {
            $days = $start->diffInDays($end);
            $hours = $start->copy()->addDays($days)->diffInHours($end);
            $totalDurasi = $days . ' Hari';
            if ($hours > 0) $totalDurasi .= ' ' . $hours . ' Jam';
        }

        // Hitung Sisa Waktu
        $sisaWaktu = '';
        $isOverdue = false;
        $now = Carbon::now();

        if ($borrowing->status === 'approved' && !$borrowing->returned_at && $end) {
            if ($now->greaterThan($end)) {
                $isOverdue = true;
                $lateDays = $end->diffInDays($now);
                $lateHours = $end->copy()->addDays($lateDays)->diffInHours($now);
                $sisaWaktu = "Terlambat {$lateDays} Hari {$lateHours} Jam";
            } else {
                $remainingDays = $now->diffInDays($end);
                $sisaWaktu = "{$remainingDays} Hari lagi";
            }
        } elseif ($borrowing->returned_at) {
            $sisaWaktu = 'Selesai (Dikembalikan)';
        }

        $history = AssetHistory::where('asset_id', $borrowing->asset_id)
            ->latest()
            ->take(10)
            ->get();

        $status = 'pending';
        if ($borrowing->status === 'rejected') $status = 'rejected';
        elseif ($borrowing->status === 'approved' && $borrowing->returned_at) $status = 'returned';
        elseif ($borrowing->status === 'approved' && !$borrowing->returned_at) $status = 'active';

        return view('borrowing.show', [
            'borrowing' => $borrowing,
            'borrowing_status' => $status,
            'history' => $history,
            'totalDurasi' => $totalDurasi,
            'sisaWaktu' => $sisaWaktu,
            'isOverdue' => $isOverdue
        ]);
    }

    /**
     * Approve Peminjaman
     */
    public function approve($id)
    {
        $request = AssetRequest::with('asset')->findOrFail($id);
        
        if ($request->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        // Cek stok
        if ($request->asset->quantity < $request->quantity) {
            return back()->with('error', 'Gagal! Stok aset tidak mencukupi.');
        }

        // Kurangi Stok
        $request->asset->decrement('quantity', $request->quantity ?? 1);

        // Update Request
        $request->status = 'approved';
        $request->approved_at = now(); // Catat waktu approve
        $request->save();

        // Catat History
        AssetHistory::create([
            'asset_id' => $request->asset_id,
            'user_id' => auth()->id(),
            'action' => 'approved', 
            'notes' => 'Peminjaman disetujui Admin. Stok berkurang.',
            'date' => now()
        ]);

        return back()->with('success', 'Peminjaman berhasil disetujui!');
    }

    /**
     * Reject Peminjaman
     */
    public function reject(Request $request, $id)
    {
        $assetRequest = AssetRequest::findOrFail($id);
        
        if ($assetRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $request->validate([
            'admin_note' => 'required|string'
        ]);

        $assetRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note
        ]);

        AssetHistory::create([
            'asset_id' => $assetRequest->asset_id,
            'user_id' => auth()->id(),
            'action' => 'rejected',
            'notes' => 'Permintaan ditolak: ' . $request->admin_note
        ]);

        return back()->with('success', 'Permintaan berhasil ditolak.');
    }

    public function quickApprove($id)
    {
        return $this->approve($id);
    }

    /**
     * Riwayat peminjaman user tertentu
     */
    public function userHistory($userId = null)
    {
        // Jika userId null (dipanggil via route tanpa param), gunakan auth user
        $targetUserId = $userId ?: auth()->id();

        // Security check: cegah user biasa melihat history orang lain
        if (auth()->user()->role !== 'admin' && auth()->id() != $targetUserId) {
             abort(403, 'Unauthorized action.');
        }

        $user = \App\Models\User::findOrFail($targetUserId);
        
        $borrowings = AssetRequest::where('user_id', $targetUserId)
            ->with('asset')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_borrowings' => AssetRequest::where('user_id', $targetUserId)->count(),
            'active_borrowings' => AssetRequest::where('user_id', $targetUserId)
                ->where('status', 'approved')
                ->where('returned_at', null)
                ->count(),
            'returned_borrowings' => AssetRequest::where('user_id', $targetUserId)
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
    public function returnAsset(Request $request, $id) 
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
        ]);

        // Kembalikan Stok Aset
        $borrowing->asset->increment('quantity', $borrowing->quantity ?? 1);
        
        // Update status aset jadi available jika sebelumnya deployed dan stok > 0
        // (Opsional, tergantung logika bisnis: apakah aset otomatis available)
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

        $summary = [
            'total' => $borrowings->count(),
            'active' => $borrowings->where('status', 'approved')->where('returned_at', null)->count(),
            'returned' => $borrowings->where('returned_at', '!=', null)->count(),
        ];

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

        $filename = 'laporan-peminjaman-' . date('Y-m-d-H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'No', 'Peminjam', 'Email', 'Aset', 'Tanggal Peminjaman', 'Tanggal Kembali', 'Status', 'Kondisi', 'Durasi (Hari)', 'Catatan'
        ], ';');

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

    public function stats()
    {
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
            'active_borrowings' => AssetRequest::where('status', 'approved')->where('returned_at', null)->count(),
            'returned_borrowings' => AssetRequest::where('returned_at', '!=', null)->count(),
            'top_items' => $topItems,
            'top_borrowers' => $topBorrowers
        ]);
    }
}