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
    /**
     * ADMIN: Daftar semua peminjaman
     */
    public function index(Request $request)
    {
        // Security check: Hanya admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini khusus Admin.');
        }

        $query = AssetRequest::with(['user', 'asset']);

        // Filter Search
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

        // Filter Status
        if ($request->borrowing_status === 'active') {
            $query->where('status', 'approved')->whereNull('returned_at');
        } elseif ($request->borrowing_status === 'returned') {
            $query->whereNotNull('returned_at');
        } elseif ($request->borrowing_status === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($request->borrowing_status === 'pending') {
            $query->where('status', 'pending');
        }

        // Urutan
        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $query->oldest('created_at');
        } else {
            $query->latest('created_at');
        }

        $borrowings = $query->paginate(15)->appends($request->query());
        
        // Transform data untuk view (Helper Status)
        $borrowings->getCollection()->transform(function($item) {
            $status = 'pending';
            if ($item->status === 'rejected') $status = 'rejected';
            elseif ($item->status === 'approved' && $item->returned_at) $status = 'returned';
            elseif ($item->status === 'approved' && !$item->returned_at) $status = 'active';
            $item->borrowing_status = $status;
            return $item;
        });

        // Statistik
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

    /**
     * USER: Simpan Pengajuan
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'quantity' => 'required|integer|min:1',
            'return_date' => 'nullable|date|after_or_equal:today',
            'return_time' => 'nullable', // Tambahan untuk jam
            'reason' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $asset = Asset::lockForUpdate()->findOrFail($request->asset_id);

            // Cek Stok
            if ($asset->quantity < $request->quantity) {
                return back()->with('error', 'Stok aset tidak mencukupi!')->withInput();
            }

            // Gabungkan Tanggal & Waktu Kembali jika ada
            $returnDateTime = $request->return_date;
            if ($request->return_date && $request->return_time) {
                $returnDateTime = $request->return_date . ' ' . $request->return_time;
            }

            AssetRequest::create([
                'user_id' => auth()->id(),
                'asset_id' => $request->asset_id,
                'quantity' => $request->quantity,
                'request_date' => now(), 
                'return_date' => $returnDateTime,
                'reason' => $request->reason,
                'status' => 'pending', 
            ]);

            // Log History
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
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * SHARED: Detail Peminjaman (Admin & User Pemilik)
     */
    public function show($id)
    {
        $borrowing = AssetRequest::with(['user', 'asset'])->findOrFail($id);

        // Security Check
        if (auth()->user()->role !== 'admin' && $borrowing->user_id !== auth()->id()) {
            abort(403, 'Akses Ditolak: Anda tidak berhak melihat data peminjaman ini.');
        }

        // Set Locale Carbon
        Carbon::setLocale('id'); 
        
        $totalDurasi = '-';
        $sisaWaktu = '-';
        $isOverdue = false;

        // Tentukan Waktu Mulai (Borrowed At atau Created At)
        $start = $borrowing->borrowed_at ? Carbon::parse($borrowing->borrowed_at) : Carbon::parse($borrowing->created_at);
        $end = $borrowing->return_date ? Carbon::parse($borrowing->return_date) : null;

        // Hitung Total Durasi Rencana
        if ($end) {
            // diff() mengembalikan interval positif (absolute)
            $totalDurasi = $this->formatInterval($start->diff($end));
        }

        // Hitung Sisa Waktu / Status Keterlambatan
        if ($borrowing->status === 'approved' && !$borrowing->returned_at && $end) {
            $now = Carbon::now();
            
            if ($now->greaterThan($end)) {
                // Jika sekarang > rencana kembali = Terlambat
                $isOverdue = true;
                $sisaWaktu = "Terlambat " . $this->formatInterval($end->diff($now));
            } else {
                // Jika sekarang < rencana kembali = Sisa Waktu
                $sisaWaktu = $this->formatInterval($now->diff($end)) . " lagi";
            }
        } elseif ($borrowing->returned_at) {
            $sisaWaktu = 'Selesai (Dikembalikan)';
        } elseif ($borrowing->status == 'pending') {
            $sisaWaktu = 'Menunggu Persetujuan';
        } elseif ($borrowing->status == 'rejected') {
            $sisaWaktu = 'Permintaan Ditolak';
        }

        $history = AssetHistory::where('asset_id', $borrowing->asset_id)->latest()->take(10)->get();

        return view('borrowing.show', [
            'borrowing' => $borrowing,
            'borrowing_status' => $borrowing->status,
            'history' => $history,
            'totalDurasi' => $totalDurasi,
            'sisaWaktu' => $sisaWaktu,
            'isOverdue' => $isOverdue
        ]);
    }

    /**
     * ADMIN: Approve Peminjaman
     */
    public function approve($id)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        try {
            DB::beginTransaction();

            $assetRequest = AssetRequest::with('asset')->lockForUpdate()->findOrFail($id);
            
            if ($assetRequest->status !== 'pending') {
                return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
            }

            if ($assetRequest->asset->quantity < $assetRequest->quantity) {
                return back()->with('error', 'Gagal! Stok aset tidak mencukupi.');
            }

            // Kurangi Stok
            $assetRequest->asset->decrement('quantity', $assetRequest->quantity ?? 1);
            
            // Update Status Aset jika stok habis
            if($assetRequest->asset->quantity == 0) {
                $assetRequest->asset->update(['status' => 'deployed']);
            }

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

    /**
     * ADMIN: Reject Peminjaman
     */
    public function reject(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate(['admin_note' => 'required|string|max:500']);

        try {
            DB::beginTransaction();
            
            $assetRequest = AssetRequest::lockForUpdate()->findOrFail($id);
            
            if ($assetRequest->status !== 'pending') {
                return back()->with('error', 'Gagal: Status permintaan bukan pending.');
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
            return back()->with('success', 'Permintaan berhasil ditolak.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Reject Error ID $id: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menolak: ' . $e->getMessage());
        }
    }

    /**
     * USER: Lihat Riwayat Sendiri
     */
    public function userHistory()
    {
        $borrowings = AssetRequest::where('user_id', auth()->id())
            ->with('asset')
            ->latest()
            ->paginate(20);

        return view('borrowing.user-history', [
            'user' => auth()->user(),
            'borrowings' => $borrowings,
            'stats' => [
                'total_borrowings' => AssetRequest::where('user_id', auth()->id())->count(),
                'active_borrowings' => AssetRequest::where('user_id', auth()->id())->where('status', 'approved')->whereNull('returned_at')->count(),
                'returned_borrowings' => AssetRequest::where('user_id', auth()->id())->whereNotNull('returned_at')->count(),
            ]
        ]);
    }

    /**
     * ADMIN/USER: Kembalikan Aset
     */
    public function returnAsset(Request $request, $id) 
    {
        $request->validate([
            'condition' => 'required|in:good,minor_damage,major_damage',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $borrowing = AssetRequest::with('asset')->findOrFail($id);

            // Security: Cek kepemilikan
            if (auth()->user()->role !== 'admin' && $borrowing->user_id !== auth()->id()) {
                abort(403);
            }

            $borrowing->update([
                'returned_at' => now(),
                'condition' => $request->condition,
                'return_notes' => $request->notes,
            ]);

            // Kembalikan Stok
            $borrowing->asset->increment('quantity', $borrowing->quantity ?? 1);
            
            // Jika status sebelumnya deployed (habis), kembalikan jadi available
            if($borrowing->asset->status == 'deployed' && $borrowing->asset->quantity > 0) {
                $borrowing->asset->update(['status' => 'available']);
            }

            AssetHistory::create([
                'asset_id' => $borrowing->asset_id,
                'user_id' => auth()->id(),
                'action' => 'returned',
                'notes' => 'Dikembalikan. Kondisi: ' . $request->condition
            ]);

            DB::commit();
            return back()->with('success', 'Aset berhasil dikembalikan');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal proses pengembalian: ' . $e->getMessage());
        }
    }

    /**
     * HELPER: Format Durasi (X Hari Y Jam Z Menit)
     */
    private function formatInterval($diff)
    {
        $parts = [];
        if ($diff->d > 0) $parts[] = $diff->d . ' Hari';
        if ($diff->h > 0) $parts[] = $diff->h . ' Jam';
        if ($diff->i > 0) $parts[] = $diff->i . ' Menit';
        
        if (empty($parts)) return 'Kurang dari 1 Menit';
        return implode(' ', array_slice($parts, 0, 3)); 
    }
}