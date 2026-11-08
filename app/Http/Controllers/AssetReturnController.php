<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\AssetRequest;
use App\Models\AssetReturn;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Alur pengembalian aset + denda:
 *
 *  User     : store            -> AssetReturn(pending), request = pending_return
 *  Admin    : verify / reject  -> request = returned (stok bertambah) / kembali ke approved
 *  Denda    : (admin isi nominal saat verify) fine_status = unpaid
 *  User     : submitFineProof  -> proof_submitted (unggah bukti bayar)
 *  Admin    : confirmFine      -> paid   | rejectFineProof -> unpaid | waiveFine -> waived
 */
class AssetReturnController extends Controller
{
    public function __construct()
    {
        // Semua aksi admin/teknisi butuh izin 'return.verify'.
        // store & submitFineProof dibuka untuk user, tetapi dicek kepemilikannya di dalam method.
        // [PRESENTASI FR-05 #2] Verifikasi hanya untuk admin/teknisi. Method store (pengajuan oleh
        //     staf) sengaja dibiarkan terbuka untuk user yang login (kepemilikan dicek di dalam method).
        $this->middleware('can:return.verify')->only([
            'index', 'verify', 'reject', 'confirmFine', 'rejectFineProof', 'waiveFine',
        ]);
    }

    /**
     * [ADMIN/TEKNISI] Daftar pengembalian + denda.
     * Tab: verify (perlu diverifikasi), fine (denda berjalan), all (riwayat).
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'verify');
        if (!in_array($tab, ['verify', 'fine', 'all'], true)) {
            $tab = 'verify';
        }

        $query = AssetReturn::with(['asset:id,name,serial_number', 'user:id,name,email', 'assetRequest:id,quantity,return_date,borrowed_at', 'admin:id,name']);

        if ($tab === 'verify') {
            $query->where('status', 'pending')->orderBy('created_at');
        } elseif ($tab === 'fine') {
            // Bukti yang menunggu konfirmasi ditampilkan paling atas
            $query->whereIn('fine_status', [AssetReturn::FINE_UNPAID, AssetReturn::FINE_PROOF])
                ->orderByRaw("CASE WHEN fine_status = 'proof_submitted' THEN 0 ELSE 1 END")
                ->orderBy('created_at');
        } else {
            $query->orderByDesc('created_at');
        }

        $returns = $query->paginate(10)->withQueryString();

        // Hitung dari seluruh data, bukan hanya halaman yang tampil
        $counts = [
            'verify' => AssetReturn::where('status', 'pending')->count(),
            'fine' => AssetReturn::whereIn('fine_status', [AssetReturn::FINE_UNPAID, AssetReturn::FINE_PROOF])->count(),
            'proof' => AssetReturn::where('fine_status', AssetReturn::FINE_PROOF)->count(),
            'outstanding' => (float) AssetReturn::whereIn('fine_status', [AssetReturn::FINE_UNPAID, AssetReturn::FINE_PROOF])->sum('fine'),
        ];

        return view('returns.index', compact('returns', 'tab', 'counts'));
    }

    /**
     * [USER] Ajukan pengembalian (dengan foto bukti kondisi).
     */
    public function store(Request $request)
    {
        // [PRESENTASI FR-05 #3] Staf wajib memilih kondisi aset (good/broken/maintenance) dan
        //     mengunggah minimal 1 foto bukti (maksimal 3, tiap file 2 MB).
        $validated = $request->validate([
            'asset_request_id' => 'required|integer|exists:asset_requests,id',
            'condition' => 'required|in:good,broken,maintenance',
            'notes' => 'nullable|string|max:255',
            'photo_proof_1' => 'required|image|max:2048',
            'photo_proof_2' => 'nullable|image|max:2048',
            'photo_proof_3' => 'nullable|image|max:2048',
        ], [
            'photo_proof_1.required' => 'Wajib menyertakan minimal 1 foto bukti kondisi aset.',
            'photo_proof_1.image' => 'File harus berupa gambar.',
            'photo_proof_1.max' => 'Ukuran foto maksimal 2MB per file.',
            'condition.required' => 'Pilih kondisi barang terlebih dahulu.',
        ]);

        $paths = [];

        try {
            DB::transaction(function () use ($request, $validated, &$paths) {
                $assetRequest = AssetRequest::lockForUpdate()->findOrFail($validated['asset_request_id']);

                abort_unless($assetRequest->user_id === auth()->id(), 403, 'Anda tidak berhak mengembalikan aset ini.');

                // [PRESENTASI FR-05 #4] Mencegah pengembalian ganda: aset yang sudah dikembalikan atau sedang
                //     menunggu verifikasi ditolak.
                if ($assetRequest->status === 'pending_return') {
                    throw new \DomainException('Pengembalian aset ini sedang menunggu verifikasi admin.');
                }
                if ($assetRequest->status !== 'approved' || $assetRequest->returned_at) {
                    throw new \DomainException('Aset ini tidak sedang dipinjam atau sudah dikembalikan.');
                }

                $photos = [];
                for ($i = 1; $i <= 3; $i++) {
                    $key = "photo_proof_$i";
                    if ($request->hasFile($key)) {
                        $paths[] = $photos[$key] = $request->file($key)->store('returns', 'public');
                    } else {
                        $photos[$key] = null;
                    }
                }

                // [PRESENTASI FR-05 #5] Menyimpan data pengembalian beserta foto bukti. Status peminjaman
                //     menjadi 'pending_return'. Stok BELUM bertambah.
                AssetReturn::create([
                    'asset_request_id' => $assetRequest->id,
                    'user_id' => auth()->id(),
                    'asset_id' => $assetRequest->asset_id,
                    'return_date' => now(),
                    'condition' => $validated['condition'],
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'pending',
                ] + $photos);

                $assetRequest->update(['status' => 'pending_return']);
            });
        } catch (\DomainException $e) {
            $this->deleteFiles($paths);
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->deleteFiles($paths);
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface || $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                throw $e;
            }
            Log::error('Gagal mengajukan pengembalian', ['error' => $e->getMessage()]);
            return back()->with('error', 'Pengembalian gagal diproses. Silakan coba lagi.');
        }

        return back()->with('success', 'Pengembalian diajukan! Serahkan barang ke Admin untuk diverifikasi.');
    }

    /**
     * [ADMIN] Verifikasi fisik: stok bertambah, tiket peminjaman ditutup, denda dicatat.
     */
    public function verify(Request $request, $id)
    {
        // [PRESENTASI FR-05 #6] Kondisi akhir ditentukan admin setelah memeriksa fisik barang
        //     (bisa berbeda dari kondisi yang dilaporkan staf).
        $data = $request->validate([
            'final_condition' => 'required|in:available,maintenance,broken',
            'fine' => 'nullable|numeric|min:0|max:999999999',
            'admin_note' => 'nullable|string|max:500',
        ], [
            'final_condition.required' => 'Pilih kondisi fisik aset terlebih dahulu.',
            'fine.numeric' => 'Denda harus berupa angka.',
            'fine.min' => 'Denda tidak boleh negatif.',
        ]);

        $fine = (float) ($data['fine'] ?? 0);

        try {
            $message = DB::transaction(function () use ($id, $data, $fine) {
                $return = AssetReturn::with('assetRequest', 'user')->lockForUpdate()->findOrFail($id);

                if ($return->status !== 'pending') {
                    throw new \DomainException('Data ini sudah diproses sebelumnya.');
                }

                $asset = Asset::lockForUpdate()->findOrFail($return->asset_id);
                $assetRequest = $return->assetRequest;
                $qty = $assetRequest?->quantity ?: 1;
                $final = $data['final_condition'];

                // [PRESENTASI FR-05 #7] Stok bertambah HANYA di sini (saat verifikasi). Status aset mengikuti
                //     kondisi akhir: layak menjadi available, selain itu maintenance/broken.
                // Unit fisik selalu kembali ke gudang; status aset menentukan bisa dipinjam lagi atau tidak.
                $asset->increment('quantity', $qty);
                $asset->update(['status' => $final]);

                // [PRESENTASI FR-06 #1] Jalur OTOMATIS laporan kerusakan: jika kondisi akhir rusak/perlu
                //     perbaikan, sistem langsung membuat tiket perbaikan di tabel maintenances.
                if (in_array($final, ['maintenance', 'broken'], true)) {
                    Maintenance::create([
                        'asset_id' => $asset->id,
                        'user_id' => auth()->id(),
                        'vendor_name' => 'Internal Review',
                        'start_date' => now(),
                        'problem_description' => 'Kondisi aset: ' . ucfirst($final) . '. Catatan User: ' . ($return->notes ?? '-'),
                        'status' => 'pending',
                    ]);
                }

                $return->update([
                    'status' => 'approved',
                    'admin_id' => auth()->id(),
                    'condition' => $final,
                    'fine' => $fine,
                    'fine_status' => $fine > 0 ? AssetReturn::FINE_UNPAID : AssetReturn::FINE_NONE,
                    'admin_note' => $data['admin_note'] ?? null,
                ]);

                // return_date (jatuh tempo) dibiarkan; waktu kembali sebenarnya = returned_at
                $assetRequest?->update([
                    'status' => 'returned',
                    'returned_at' => now(),
                    'condition' => $final === 'available' ? 'good' : ($final === 'maintenance' ? 'minor_damage' : 'major_damage'),
                    'return_notes' => $return->notes,
                ]);

                // [PRESENTASI FR-05 #8] Menutup tiket peminjaman (status 'returned') lalu mencatat kondisi
                //     akhir dan denda ke riwayat aset.
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'user_id' => auth()->id(),
                    'action' => 'returned',
                    'notes' => "Aset dikembalikan oleh {$return->user?->name}. Kondisi akhir: " . ucfirst($final)
                        . ($fine > 0 ? '. Denda: Rp ' . number_format($fine, 0, ',', '.') . ' (belum dibayar)' : ''),
                ]);

                return $fine > 0
                    ? 'Pengembalian diverifikasi. Denda Rp ' . number_format($fine, 0, ',', '.') . ' dicatat dan menunggu pembayaran peminjam.'
                    : 'Pengembalian diverifikasi. Stok aset diperbarui.';
            });
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data pengembalian tidak ditemukan.');
        } catch (\Throwable $e) {
            Log::error('Gagal verifikasi pengembalian', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Verifikasi gagal diproses. Tidak ada data yang berubah, silakan coba lagi.');
        }

        return back()->with('success', $message);
    }

    /**
     * [ADMIN] Tolak pengajuan pengembalian (mis. barang belum diserahkan / foto tidak sesuai).
     * Peminjaman kembali aktif sehingga user dapat mengajukan ulang.
     */
    public function reject(Request $request, $id)
    {
        $data = $request->validate([
            'admin_note' => 'required|string|min:5|max:500',
        ], [
            'admin_note.required' => 'Alasan penolakan wajib diisi agar peminjam tahu apa yang harus diperbaiki.',
            'admin_note.min' => 'Alasan penolakan terlalu singkat.',
        ]);

        try {
            DB::transaction(function () use ($id, $data) {
                $return = AssetReturn::with('assetRequest')->lockForUpdate()->findOrFail($id);

                if ($return->status !== 'pending') {
                    throw new \DomainException('Data ini sudah diproses sebelumnya.');
                }

                $return->update([
                    'status' => 'rejected',
                    'admin_id' => auth()->id(),
                    'admin_note' => $data['admin_note'],
                ]);

                $return->assetRequest?->update(['status' => 'approved']);

                AssetHistory::create([
                    'asset_id' => $return->asset_id,
                    'user_id' => auth()->id(),
                    'action' => 'updated',
                    'notes' => 'Pengajuan pengembalian ditolak: ' . $data['admin_note'],
                ]);
            });
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data pengembalian tidak ditemukan.');
        } catch (\Throwable $e) {
            Log::error('Gagal menolak pengembalian', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Penolakan gagal diproses, silakan coba lagi.');
        }

        return back()->with('success', 'Pengajuan pengembalian ditolak. Peminjam dapat mengajukan ulang.');
    }

    /**
     * [USER] Unggah bukti pembayaran denda.
     */
    public function submitFineProof(Request $request, $id)
    {
        $data = $request->validate([
            'fine_payment_method' => 'required|in:transfer,cash',
            'fine_payment_proof' => 'required|image|max:2048',
            'fine_note' => 'nullable|string|max:255',
        ], [
            'fine_payment_method.required' => 'Pilih metode pembayaran.',
            'fine_payment_proof.required' => 'Unggah foto bukti pembayaran (struk / bukti transfer).',
            'fine_payment_proof.image' => 'Bukti pembayaran harus berupa gambar.',
            'fine_payment_proof.max' => 'Ukuran bukti pembayaran maksimal 2MB.',
        ]);

        $return = AssetReturn::findOrFail($id);
        abort_unless($return->user_id === auth()->id(), 403, 'Anda tidak berhak mengubah denda ini.');

        $path = $request->file('fine_payment_proof')->store('fines', 'public');
        $oldProof = null;

        try {
            DB::transaction(function () use ($id, $data, $path, &$oldProof) {
                $return = AssetReturn::lockForUpdate()->findOrFail($id);

                if ($return->status !== 'approved' || !in_array($return->fine_status, [AssetReturn::FINE_UNPAID, AssetReturn::FINE_PROOF], true)) {
                    throw new \DomainException('Tidak ada denda yang perlu dibayar pada pengembalian ini.');
                }

                $oldProof = $return->fine_payment_proof;
                $return->update([
                    'fine_status' => AssetReturn::FINE_PROOF,
                    'fine_payment_method' => $data['fine_payment_method'],
                    'fine_payment_proof' => $path,
                    'fine_proof_submitted_at' => now(),
                    'fine_note' => $data['fine_note'] ?? null,
                ]);
            });
        } catch (\DomainException $e) {
            $this->deleteFiles([$path]);
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->deleteFiles([$path]);
            Log::error('Gagal menyimpan bukti denda', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Bukti pembayaran gagal disimpan, silakan coba lagi.');
        }

        $this->deleteFiles([$oldProof]);

        return back()->with('success', 'Bukti pembayaran terkirim. Menunggu konfirmasi admin.');
    }

    /**
     * [ADMIN] Konfirmasi denda sudah dibayar (lunas).
     * Admin bisa langsung konfirmasi tanpa bukti jika dibayar tunai di tempat.
     */
    public function confirmFine(Request $request, $id)
    {
        $data = $request->validate([
            'fine_payment_method' => 'nullable|in:transfer,cash',
            'fine_note' => 'nullable|string|max:255',
        ]);

        return $this->transitionFine($id, function (AssetReturn $return) use ($data) {
            $return->update([
                'fine_status' => AssetReturn::FINE_PAID,
                'fine_payment_method' => $data['fine_payment_method'] ?? $return->fine_payment_method ?? 'cash',
                'fine_paid_at' => now(),
                'fine_confirmed_by' => auth()->id(),
                'fine_note' => $data['fine_note'] ?? $return->fine_note,
            ]);

            $this->logFine($return, 'Denda Rp ' . number_format($return->fine, 0, ',', '.') . " dari {$return->user?->name} dikonfirmasi LUNAS.");
        }, 'Pembayaran denda dikonfirmasi lunas.');
    }

    /**
     * [ADMIN] Tolak bukti bayar (tidak valid / nominal salah) -> kembali ke "belum dibayar".
     */
    public function rejectFineProof(Request $request, $id)
    {
        $data = $request->validate([
            'fine_note' => 'required|string|min:5|max:255',
        ], [
            'fine_note.required' => 'Alasan penolakan bukti wajib diisi.',
            'fine_note.min' => 'Alasan penolakan terlalu singkat.',
        ]);

        return $this->transitionFine($id, function (AssetReturn $return) use ($data) {
            if ($return->fine_status !== AssetReturn::FINE_PROOF) {
                throw new \DomainException('Belum ada bukti pembayaran yang perlu ditolak.');
            }

            $return->update([
                'fine_status' => AssetReturn::FINE_UNPAID,
                'fine_note' => 'Bukti ditolak: ' . $data['fine_note'],
            ]);

            $this->logFine($return, "Bukti bayar denda dari {$return->user?->name} ditolak: {$data['fine_note']}");
        }, 'Bukti pembayaran ditolak. Peminjam diminta mengunggah ulang.');
    }

    /**
     * [ADMIN] Bebaskan denda (kebijakan / kerusakan wajar). Alasan wajib untuk audit.
     */
    public function waiveFine(Request $request, $id)
    {
        $data = $request->validate([
            'fine_note' => 'required|string|min:5|max:255',
        ], [
            'fine_note.required' => 'Alasan pembebasan denda wajib diisi untuk keperluan audit.',
            'fine_note.min' => 'Alasan pembebasan terlalu singkat.',
        ]);

        return $this->transitionFine($id, function (AssetReturn $return) use ($data) {
            $return->update([
                'fine_status' => AssetReturn::FINE_WAIVED,
                'fine_paid_at' => now(),
                'fine_confirmed_by' => auth()->id(),
                'fine_note' => $data['fine_note'],
            ]);

            $this->logFine($return, "Denda Rp " . number_format($return->fine, 0, ',', '.') . " dari {$return->user?->name} dibebaskan: {$data['fine_note']}");
        }, 'Denda dibebaskan.');
    }

    /** Jalankan perubahan status denda secara atomik, hanya jika denda masih berjalan. */
    private function transitionFine($id, \Closure $apply, string $successMessage)
    {
        try {
            DB::transaction(function () use ($id, $apply) {
                $return = AssetReturn::with('user')->lockForUpdate()->findOrFail($id);

                if ($return->status !== 'approved' || !$return->hasOutstandingFine()) {
                    throw new \DomainException('Denda ini sudah diselesaikan atau tidak ada denda yang perlu diproses.');
                }

                $apply($return);
            });
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Data pengembalian tidak ditemukan.');
        } catch (\Throwable $e) {
            Log::error('Gagal memproses denda', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Proses denda gagal, tidak ada data yang berubah. Silakan coba lagi.');
        }

        return back()->with('success', $successMessage);
    }

    private function logFine(AssetReturn $return, string $note): void
    {
        AssetHistory::create([
            'asset_id' => $return->asset_id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'notes' => $note,
        ]);
    }

    private function deleteFiles(array $paths): void
    {
        foreach (array_filter($paths) as $path) {
            try {
                Storage::disk('public')->delete($path);
            } catch (\Throwable $e) {
                // file sampah tidak boleh menggagalkan request
            }
        }
    }
}
