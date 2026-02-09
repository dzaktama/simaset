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
    // [CONSTRUCTOR] Middleware Setup
    // Menentukan hak akses user terhadap controller ini
    public function __construct()
    {
        // Hanya user dengan permission 'borrow.action' (Admin) yang bisa index, approve, reject
        $this->middleware('can:borrow.action')->only(['index', 'approve', 'reject']);
        // Hanya user dengan permission 'borrow.request' (Semua User) yang bisa store/pinjam
        $this->middleware('can:borrow.request')->only(['store']);
        // Hanya user yang login yang bisa lihat history sendiri
        $this->middleware('can:borrow.view')->only(['userHistory']);
    }

    /**
     * [FUNCTION] Menampilkan daftar semua peminjaman (Untuk Admin)
     * File View: resources/views/borrowing/index.blade.php
     */
    public function index(Request $request)
    {
        // [DATABASE] Eager Loading ('with') untuk optimasi query, mengambil data user dan asset sekaligus
        $query = AssetRequest::with(['user', 'asset']);

        // [LOGIC] Filter Pencarian berdasarkan nama user atau nama aset
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Cari di tabel relasi 'user' kolom 'name'
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })->orWhereHas('asset', function ($q) use ($search) {
                    // Atau cari di tabel relasi 'asset' kolom 'name'
                    $q->where('name', 'like', "%$search%");
                });
            });
        }

        // [LOGIC] Filter Status Peminjaman
        if ($request->borrowing_status === 'active') {
            $query->where('status', 'approved')->whereNull('returned_at');
        } elseif ($request->borrowing_status === 'returned') {
            $query->whereNotNull('returned_at');
        } elseif ($request->borrowing_status === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($request->borrowing_status === 'pending') {
            $query->where('status', 'pending');
        }

        // [LOGIC] Sorting / Urutan Data
        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $query->oldest('created_at');
        } else {
            $query->latest('created_at');
        }

        // [PAGINATION] Menampilkan 15 data per halaman
        $borrowings = $query->paginate(15)->appends($request->query());
        
        // [HELPER] Menambahkan properti status custom untuk tampilan di view
        $borrowings->getCollection()->transform(function($item) {
            $status = 'pending';
            if ($item->status === 'rejected') $status = 'rejected';
            elseif ($item->status === 'approved' && $item->returned_at) $status = 'returned';
            elseif ($item->status === 'approved' && !$item->returned_at) $status = 'active';
            $item->borrowing_status = $status;
            return $item;
        });

        // [STATISTICS] Menghitung jumlah untuk kartu statistik di dashboard
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
     * [FUNCTION] Menyimpan pengajuan peminjaman baru (Untuk User)
     * Handle form dari modal pinjam.
     */
    public function store(Request $request)
    {
        // [VALIDATION] Memastikan data input valid
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'quantity' => 'required|integer|min:1',
            'return_date' => 'nullable|date|after_or_equal:today',
            'return_time' => 'nullable',
            'reason' => 'required|string|max:255',
        ]);

        // [ERROR HANDLING] Try-Catch Block untuk menangani error database transaction
        try {
            // Memulai transaksi database (agar data konsisten, rollback jika gagal)
            DB::beginTransaction();

            // [LOCKING] lockForUpdate mencegah double booking saat concurrent request
            $asset = Asset::lockForUpdate()->findOrFail($request->asset_id);

            // [LOGIC] Cek ketersediaan stok
            if ($asset->quantity < $request->quantity) {
                return back()->with('error', 'Stok aset tidak mencukupi!')->withInput();
            }

            // Gabungkan Tanggal & Waktu Kembali
            $returnDateTime = $request->return_date;
            if ($request->return_date && $request->return_time) {
                $returnDateTime = $request->return_date . ' ' . $request->return_time;
            }

            // [DATABASE] Simpan data peminjaman
            AssetRequest::create([
                'user_id' => auth()->id(),
                'asset_id' => $request->asset_id,
                'quantity' => $request->quantity,
                'request_date' => now(), 
                'return_date' => $returnDateTime,
                'reason' => $request->reason,
                'status' => 'pending', 
            ]);

            // [LOGGING] Catat di history aset
            $notes = 'User mengajukan peminjaman aset.';
            if (session('impersonator_id')) {
                $notes .= ' (Override by Super Admin)';
            }

            AssetHistory::create([
                'asset_id' => $request->asset_id,
                'user_id' => auth()->id(),
                'action' => 'created', 
                'notes' => $notes
            ]);

            // Simpan perubahan permanen ke database
            DB::commit();
            return redirect()->route('borrowing.history')->with('success', 'Pengajuan berhasil dikirim.');

        } catch (Exception $e) {
            // [ROLLBACK] Batalkan semua perubahan jika terjadi error
            DB::rollBack();
            Log::error('Store Borrowing Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * [FUNCTION] Membatalkan pengajuan peminjaman (Untuk User)
     * Hanya bisa dilakukan jika status masih 'pending'.
     */
    public function cancelRequest(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $borrowing = AssetRequest::findOrFail($id);

            // [SECURITY] Validasi kepemilikan data (Authorization)
            if ($borrowing->user_id !== auth()->id()) {
                abort(403, 'Anda tidak berhak membatalkan pengajuan ini.');
            }

            // [LOGIC] Validasi Status
            if ($borrowing->status !== 'pending') {
                return back()->with('error', 'Hanya pengajuan yang masih "Menunggu Persetujuan" yang bisa dibatalkan.');
            }
            
            // [DATABASE] Hard Delete (Hapus permanen)
            $borrowing->delete();

            DB::commit();
            return redirect()->route('borrowing.history')->with('success', 'Pengajuan peminjaman berhasil dibatalkan.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }


    /**
     * [FUNCTION] Menampilkan detail satu tiket peminjaman (Shared Admin & User)
     * File View: resources/views/borrowing/show.blade.php
     */
    public function show($id)
    {
        $borrowing = AssetRequest::with(['user', 'asset'])->findOrFail($id);

        // [SECURITY] Konfirmasi Hak Akses
        // Admin boleh lihat semua, User hanya boleh lihat punya sendiri
        $canViewAll = \Illuminate\Support\Facades\Gate::allows('borrow.action') || \Illuminate\Support\Facades\Gate::allows('borrow.view');
        
        if (!$canViewAll && $borrowing->user_id !== auth()->id()) {
            abort(403, 'Akses Ditolak: Anda tidak berhak melihat data peminjaman ini.');
        }

        Carbon::setLocale('id'); 
        
        $totalDurasi = '-';
        $sisaWaktu = '-';
        $isOverdue = false;

        // [LOGIC] Perhitungan Waktu
        $start = $borrowing->borrowed_at ? Carbon::parse($borrowing->borrowed_at) : Carbon::parse($borrowing->created_at);
        $end = $borrowing->return_date ? Carbon::parse($borrowing->return_date) : null;

        // Hitung Total Durasi Rencana
        if ($end) {
            // Jika jam 00:00:00, set ke akhir hari
            if ($end->format('H:i:s') === '00:00:00') {
                $end->endOfDay(); 
            }
            // Hitung selisih waktu
            $totalDurasi = $this->formatInterval($start->diff($end));
        }

        // [LOGIC] Status Keterlambatan Real-time
        if ($borrowing->status === 'approved' && !$borrowing->returned_at && $end) {
            $now = Carbon::now();
            
            if ($now->greaterThan($end)) {
                // Terlambat
                $isOverdue = true;
                $sisaWaktu = "Terlambat " . $this->formatInterval($end->diff($now));
            } else {
                // Masih ada waktu
                $sisaWaktu = $this->formatInterval($now->diff($end)) . " lagi";
            }
        } elseif ($borrowing->returned_at) {
            $sisaWaktu = 'Selesai (Dikembalikan)';
        } elseif ($borrowing->status == 'pending') {
            $sisaWaktu = 'Menunggu Persetujuan';
        } elseif ($borrowing->status == 'rejected') {
            $sisaWaktu = 'Permintaan Ditolak';
        }

        // Ambil 10 histori terakhir aset ini
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
     * [FUNCTION] Menyetujui Peminjaman (Untuk Admin)
     */
    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $assetRequest = AssetRequest::with('asset')->lockForUpdate()->findOrFail($id);
            
            if ($assetRequest->status !== 'pending') {
                return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
            }

            // [LOGIC] Cek Stok Lagi sebelum approve
            if ($assetRequest->asset->quantity < $assetRequest->quantity) {
                return back()->with('error', 'Gagal! Stok aset tidak mencukupi.');
            }

            // [DATABASE] Kurangi Stok Aset
            $assetRequest->asset->decrement('quantity', $assetRequest->quantity ?? 1);
            
            // Jika stok habis, tandai sebagai deployed
            if($assetRequest->asset->quantity == 0) {
                $assetRequest->asset->update(['status' => 'deployed']);
            }

            // Update Status Peminjaman
            $assetRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'borrowed_at' => now()
            ]);

            // Catat History
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
     * [FUNCTION] Menolak Peminjaman (Untuk Admin)
     */
    public function reject(Request $request, $id)
    {
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
     * [FUNCTION] Menampilkan riwayat peminjaman user yang sedang login
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
     * [FUNCTION] Mengembalikan Aset (Bisa Admin / User)
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

            // [SECURITY] Cek hak akses return
            $canReturnAny = \Illuminate\Support\Facades\Gate::allows('borrow.action') || \Illuminate\Support\Facades\Gate::allows('borrow.return');
            
            if (!$canReturnAny && $borrowing->user_id !== auth()->id()) {
                abort(403);
            }

            $borrowing->update([
                'returned_at' => now(),
                'condition' => $request->condition,
                'return_notes' => $request->notes,
            ]);

            // [DATABASE] Kembalikan Stok Aset
            $borrowing->asset->increment('quantity', $borrowing->quantity ?? 1);
            
            // [LOGIC] Update Status Aset berdasarkan Kondisi
            if (in_array($request->condition, ['minor_damage', 'major_damage'])) {
                $borrowing->asset->update(['status' => 'broken']);
            } elseif ($borrowing->asset->status == 'deployed' && $borrowing->asset->quantity > 0) {
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
     * [FUNCTION] Memperpanjang / Mengubah Durasi (Admin)
     */
    public function extendDuration(Request $request, $id)
    {
        $request->validate([
            'new_return_date' => 'required|date',
            'new_return_time' => 'required',
            'reason_extend' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $borrowing = AssetRequest::findOrFail($id);
            
            // Gabungkan Date & Time
            $newDateString = $request->new_return_date . ' ' . $request->new_return_time;
            $newDate = Carbon::parse($newDateString);

            // [VALIDASI] Logic Validasi Tanggal Baru
            // #1: Tidak boleh sebelum peminjaman dimulai
            $startDate = $borrowing->approved_at ?? $borrowing->borrowed_at ?? $borrowing->created_at;
            if ($newDate->lte($startDate)) {
                return back()->with('error', 'Tanggal pengembalian tidak boleh sebelum waktu mulai peminjaman.');
            }

            // #2: Tidak boleh di masa lalu
            if ($newDate->lte(now())) {
                return back()->with('error', 'Tanggal pengembalian baru harus lebih dari waktu saat ini.');
            }

            $oldDate = $borrowing->return_date ? Carbon::parse($borrowing->return_date)->format('d M Y H:i') : '-';

            $borrowing->update([
                'return_date' => $newDate
            ]);

            AssetHistory::create([
                'asset_id' => $borrowing->asset_id,
                'user_id' => auth()->id(),
                'action' => 'updated',
                'notes' => "Durasi diubah dari [$oldDate] ke [" . $newDate->format('d M Y H:i') . "]. Alasan: " . ($request->reason_extend ?? '-')
            ]);

            DB::commit();
            return back()->with('success', 'Durasi peminjaman berhasil diperbarui.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update durasi: ' . $e->getMessage());
        }
    }

    /**
     * [HELPER] Memformat durasi (DateInterval) menjadi string readable
     * Contoh: "43 Hari 5 Jam"
     * Menggunakan $diff->days untuk menghitung TOTAL selisih hari (termasuk tahun & bulan)
     */
    private function formatInterval($diff)
    {
        $parts = [];
        // [LOGIC] Gunakan 'days' untuk total hari absolut.
        // Jika pakai 'd', maka hanya menghitung sisa hari dalam bulan tsb (Misal 1 thn 5 hari -> 5 hari).
        // Dengan 'days', 1 thn 5 hari -> 370 Hari. Aman!
        if ($diff->days > 0) $parts[] = $diff->days . ' Hari';
        if ($diff->h > 0) $parts[] = $diff->h . ' Jam';
        if ($diff->i > 0) $parts[] = $diff->i . ' Menit';
        
        if (empty($parts)) return 'Kurang dari 1 Menit';
        return implode(' ', array_slice($parts, 0, 3)); 
    }
}
