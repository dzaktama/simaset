<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BorrowingController extends Controller
{
    // nampilin daftar peminjaman buat admin
    public function index(Request $request)
    {
        // cek dulu role user yang login, harus admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $query = AssetRequest::with(['user', 'asset']);

        // fitur pencarian berdasarkan nama user atau nama aset
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })->orWhereHas('asset', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            });
        }

        // filter berdasarkan status peminjaman
        if ($request->borrowing_status === 'active') {
            $query->where('status', 'approved')
                  ->whereNotNull('borrowed_at')
                  ->whereNull('returned_at');
        } elseif ($request->borrowing_status === 'returned') {
            $query->whereNotNull('returned_at');
        } elseif ($request->borrowing_status === 'rejected') {
            $query->where('status', 'rejected');
        }

        $borrowings = $query->latest('created_at')
                            ->paginate(15)
                            ->appends($request->query());
        
        // modifikasi data collection buat nentuin status visual di frontend
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

        // itung statistik ringkas buat di atas tabel
        $statistics = [
            'total' => AssetRequest::count(),
            'active' => AssetRequest::where('status', 'approved')->whereNull('returned_at')->count(),
            'pending' => AssetRequest::where('status', 'pending')->count(),
            'returned' => AssetRequest::whereNotNull('returned_at')->count(),
        ];

        return view('borrowing.index', [
            'borrowings' => $borrowings,
            'statistics' => $statistics
        ]);
    }

    // simpan pengajuan peminjaman dari user
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'quantity' => 'required|integer|min:1',
            'return_date' => 'nullable|date|after_or_equal:today',
            'reason' => 'required|string|max:255',
        ], [
            'return_date.after_or_equal' => 'Tanggal kembali tidak boleh kurang dari hari ini.',
        ]);

        try {
            DB::beginTransaction();

            // kunci baris aset biar gak balapan sama user lain pas update stok
            $asset = Asset::lockForUpdate()->findOrFail($request->asset_id);

            // pastiin stok cukup sebelum lanjut
            if ($asset->quantity < $request->quantity) {
                return back()->with('error', 'Stok aset tidak mencukupi!')->withInput();
            }

            AssetRequest::create([
                'user_id' => auth()->id(),
                'asset_id' => $request->asset_id,
                'quantity' => $request->quantity,
                'request_date' => now(), 
                'return_date' => $request->return_date,
                'reason' => $request->reason,
                'status' => 'pending', 
            ]);

            AssetHistory::create([
                'asset_id' => $request->asset_id,
                'user_id' => auth()->id(),
                'action' => 'created', 
                'notes' => 'User mengajukan peminjaman aset.'
            ]);

            DB::commit();

            return redirect()->route('borrowing.history')->with('success', 'Pengajuan berhasil dikirim.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Store Borrowing Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.')->withInput();
        }
    }

    // liat detail peminjaman spesifik
    public function show($id)
    {
        $borrowing = AssetRequest::with(['user', 'asset'])->findOrFail($id);

        Carbon::setLocale('id'); 
        
        $totalDurasi = '-';
        $sisaWaktu = '-';
        $isOverdue = false;

        // tentukan waktu mulai hitungan durasi
        $start = $borrowing->borrowed_at ? Carbon::parse($borrowing->borrowed_at) : Carbon::parse($borrowing->created_at);
        
        // set batas akhir pengembalian ke jam terakhir hari itu
        $end = $borrowing->return_date ? Carbon::parse($borrowing->return_date)->endOfDay() : null;

        // hitung total durasi peminjaman kalau tanggal kembalinya ada
        if ($end) {
            $totalDurasi = $this->formatInterval($start->diff($end));
        }

        // logika hitung sisa waktu atau keterlambatan
        if ($borrowing->status === 'approved' && !$borrowing->returned_at && $end) {
            $now = Carbon::now();
            
            if ($now->greaterThan($end)) {
                $isOverdue = true;
                $sisaWaktu = "Terlambat " . $this->formatInterval($end->diff($now));
            } else {
                $sisaWaktu = $this->formatInterval($now->diff($end)) . " lagi";
            }
        } elseif ($borrowing->returned_at) {
            $sisaWaktu = 'Selesai (Dikembalikan)';
        } elseif ($borrowing->status == 'pending') {
            $sisaWaktu = 'Menunggu Persetujuan';
        }

        // ambil history aset terkait buat tracking
        $history = AssetHistory::where('asset_id', $borrowing->asset_id)
                               ->latest()
                               ->take(10)
                               ->get();

        return view('borrowing.show', [
            'borrowing' => $borrowing,
            'borrowing_status' => $borrowing->status,
            'history' => $history,
            'totalDurasi' => $totalDurasi,
            'sisaWaktu' => $sisaWaktu,
            'isOverdue' => $isOverdue
        ]);
    }

    // helper buat format tampilan waktu biar enak dibaca
    private function formatInterval($diff)
    {
        $parts = [];
        if ($diff->d > 0) $parts[] = $diff->d . ' Hari';
        if ($diff->h > 0) $parts[] = $diff->h . ' Jam';
        if ($diff->i > 0) $parts[] = $diff->i . ' Menit';
        
        if ($diff->s > 0) $parts[] = $diff->s . ' Detik';

        if (empty($parts)) return 'Kurang dari 1 Menit';
        
        // ambil 3 unit terbesar aja biar gak kepanjangan
        return implode(' ', array_slice($parts, 0, 3)); 
    }

    // menyetujui peminjaman
    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $assetRequest = AssetRequest::with('asset')->lockForUpdate()->findOrFail($id);
            
            if ($assetRequest->status !== 'pending') {
                return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
            }

            // cek stok lagi sebelum beneran approve
            if ($assetRequest->asset->quantity < $assetRequest->quantity) {
                return back()->with('error', 'Gagal! Stok aset tidak mencukupi.');
            }

            // kurangi stok aset
            $assetRequest->asset->decrement('quantity', $assetRequest->quantity ?? 1);

            $assetRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'borrowed_at' => now()
            ]);

            AssetHistory::create([
                'asset_id' => $assetRequest->asset_id,
                'user_id' => auth()->id(),
                'action' => 'approved', 
                'notes' => 'Peminjaman disetujui Admin. Stok berkurang.'
            ]);

            DB::commit();
            return back()->with('success', 'Peminjaman berhasil disetujui!');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error Approve: ' . $e->getMessage());
        }
    }

    // nolak peminjaman
    public function reject(Request $request, $id)
    {
        $request->validate(['admin_note' => 'required|string']);

        try {
            DB::beginTransaction();
            
            $assetRequest = AssetRequest::findOrFail($id);
            
            if ($assetRequest->status !== 'pending') {
                return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
            }

            $assetRequest->update([
                'status' => 'rejected',
                'admin_note' => $request->admin_note
            ]);

            AssetHistory::create([
                'asset_id' => $assetRequest->asset_id,
                'user_id' => auth()->id(),
                'action' => 'rejected',
                'notes' => 'Ditolak: ' . $request->admin_note
            ]);

            DB::commit();
            return back()->with('success', 'Permintaan ditolak.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error Reject: ' . $e->getMessage());
        }
    }

    // jalan pintas buat approve cepat
    public function quickApprove($id)
    {
        return $this->approve($id);
    }

    // riwayat peminjaman per user
    public function userHistory($userId = null)
    {
        $targetUserId = $userId ?: auth()->id();

        // proteksi biar user biasa gak bisa intip history orang lain
        if (auth()->user()->role !== 'admin' && auth()->id() != $targetUserId) {
             abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($targetUserId);
        
        $borrowings = AssetRequest::where('user_id', $targetUserId)
            ->with('asset')
            ->latest()
            ->paginate(20);

        return view('borrowing.user-history', [
            'user' => $user,
            'borrowings' => $borrowings,
            'stats' => [
                'total_borrowings' => AssetRequest::where('user_id', $targetUserId)->count(),
                'active_borrowings' => AssetRequest::where('user_id', $targetUserId)->where('status', 'approved')->whereNull('returned_at')->count(),
                'returned_borrowings' => AssetRequest::where('user_id', $targetUserId)->whereNotNull('returned_at')->count(),
            ]
        ]);
    }

    // proses pengembalian aset
    public function returnAsset(Request $request, $id) 
    {
        $request->validate([
            'condition' => 'required|in:good,minor_damage,major_damage',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $borrowing = AssetRequest::with('asset')->findOrFail($id);

            // update data pengembalian
            $borrowing->update([
                'returned_at' => now(),
                'condition' => $request->condition,
                'return_notes' => $request->notes,
            ]);

            // balikin stok asetnya
            $borrowing->asset->increment('quantity', $borrowing->quantity ?? 1);
            
            // set status aset jadi tersedia lagi
            $borrowing->asset->update(['status' => 'available']);

            AssetHistory::create([
                'asset_id' => $borrowing->asset_id,
                'user_id' => auth()->id(),
                'action' => 'returned',
                'notes' => 'Dikembalikan. Kondisi: ' . $request->condition
            ]);

            DB::commit();
            return redirect()->route('borrowing.show', $id)->with('success', 'Aset berhasil dikembalikan');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal proses pengembalian: ' . $e->getMessage());
        }
    }

    // buat fitur reporting atau laporan
    public function report(Request $request)
    {
        $query = AssetRequest::with(['user', 'asset']);
        
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('created_at', '<=', $request->date_to);
        
        if ($request->status === 'active') {
            $query->where('status', 'approved')->whereNull('returned_at');
        } elseif ($request->status === 'returned') {
            $query->whereNotNull('returned_at');
        }
        
        if ($request->user_id) $query->where('user_id', $request->user_id);

        $borrowings = $query->latest()->get();
        $users = User::orderBy('name')->get();

        return view('borrowing.report', [
            'borrowings' => $borrowings,
            'summary' => [
                'total' => $borrowings->count(),
                'active' => $borrowings->where('status', 'approved')->whereNull('returned_at')->count(),
                'returned' => $borrowings->whereNotNull('returned_at')->count(),
            ],
            'users' => $users,
            'filters' => $request->all()
        ]);
    }

    // export excel pake logic report yang sama
    public function exportExcel(Request $request) { 
        return $this->report($request); 
    }

    // endpoint buat statistik json kalo butuh ajax
    public function stats() { 
        return response()->json([]); 
    }
}