@extends('layouts.main')

@section('container')
<style>
    /* Gaya tombol & badge halaman ini ditulis langsung (tidak bergantung pada hasil build Tailwind),
       warnanya mengikuti tema utama SIMASET: indigo sebagai aksen, slate/gray sebagai netral, font Inter. */
    .rt-btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;padding:.45rem .9rem;border-radius:.6rem;font-size:.75rem;font-weight:700;line-height:1.25;border:1px solid transparent;cursor:pointer;transition:background-color .15s,box-shadow .15s,transform .05s;white-space:nowrap;text-decoration:none}
    .rt-btn:active{transform:translateY(1px)}
    .rt-btn svg{width:.95rem;height:.95rem}
    .rt-btn-primary{background:#4f46e5;color:#fff;box-shadow:0 1px 2px rgba(79,70,229,.35)}.rt-btn-primary:hover{background:#4338ca}
    .rt-btn-warn{background:#f59e0b;color:#fff;box-shadow:0 1px 2px rgba(245,158,11,.35)}.rt-btn-warn:hover{background:#d97706}
    .rt-btn-danger{background:#e11d48;color:#fff;box-shadow:0 1px 2px rgba(225,29,72,.35)}.rt-btn-danger:hover{background:#be123c}
    .rt-btn-success{background:#059669;color:#fff;box-shadow:0 1px 2px rgba(5,150,105,.35)}.rt-btn-success:hover{background:#047857}
    .rt-btn-ghost{background:#fff;color:#374151;border-color:#d1d5db}.rt-btn-ghost:hover{background:#f9fafb}
    .rt-btn-soft{background:#eef2ff;color:#4338ca}.rt-btn-soft:hover{background:#e0e7ff}
    .rt-btn-block{display:flex;width:100%;padding:.65rem 1rem;font-size:.8rem}
    .rt-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.15rem .6rem;border-radius:9999px;font-size:.7rem;font-weight:700;line-height:1.1rem;border:1px solid transparent}
    .rt-pill i{width:.4rem;height:.4rem;border-radius:9999px;background:currentColor;display:inline-block}
    .rt-amber{background:#fffbeb;color:#b45309;border-color:#fde68a}
    .rt-rose{background:#fff1f2;color:#be123c;border-color:#fecdd3}
    .rt-green{background:#ecfdf5;color:#047857;border-color:#a7f3d0}
    .rt-slate{background:#f8fafc;color:#475569;border-color:#e2e8f0}
    .rt-indigo{background:#eef2ff;color:#4338ca;border-color:#c7d2fe}
    .rt-stat{background:#fff;border:1px solid #f1f5f9;border-radius:.9rem;padding:1rem 1.1rem;display:flex;align-items:center;gap:.9rem;box-shadow:0 1px 2px rgba(15,23,42,.04)}
    .rt-stat-icon{width:2.6rem;height:2.6rem;border-radius:.7rem;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .rt-stat-icon svg{width:1.3rem;height:1.3rem}
    .rt-tabs{display:inline-flex;gap:.25rem;padding:.25rem;background:#f1f5f9;border-radius:.8rem;flex-wrap:wrap}
    .rt-tab{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .95rem;border-radius:.6rem;font-size:.8rem;font-weight:600;color:#64748b;text-decoration:none;transition:all .15s}
    .rt-tab:hover{color:#334155}
    .rt-tab.active{background:#fff;color:#4338ca;box-shadow:0 1px 3px rgba(15,23,42,.1)}
    .rt-count{padding:.05rem .45rem;border-radius:9999px;font-size:.65rem;font-weight:700;background:#e2e8f0;color:#475569}
    .rt-tab.active .rt-count{background:#eef2ff;color:#4338ca}
    .rt-modal-box{background:#fff;border-radius:1rem;box-shadow:0 25px 50px -12px rgba(15,23,42,.35);overflow:hidden;width:100%}
    .rt-modal-head{display:flex;gap:.8rem;align-items:flex-start;padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9}
    .rt-modal-icon{width:2.5rem;height:2.5rem;border-radius:.7rem;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .rt-modal-icon svg{width:1.3rem;height:1.3rem}
    .rt-radio span{display:block;width:100%;padding:.6rem .5rem;border-radius:.7rem;border:2px solid #e2e8f0;color:#475569;text-align:center;font-size:.75rem;font-weight:700;cursor:pointer;transition:all .15s}
    .rt-radio input:checked + span.r-ok{border-color:#10b981;background:#ecfdf5;color:#047857}
    .rt-radio input:checked + span.r-svc{border-color:#f59e0b;background:#fffbeb;color:#b45309}
    .rt-radio input:checked + span.r-bad{border-color:#f43f5e;background:#fff1f2;color:#be123c}
    .rt-input{display:block;width:100%;padding:.55rem .75rem;border:1px solid #d1d5db;border-radius:.6rem;font-size:.85rem;font-family:inherit;background:#fff}
    .rt-input:focus{outline:none;border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.2)}
    .rt-label{display:block;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.35rem}
    .rt-hint{font-size:.7rem;color:#9ca3af;margin-top:.25rem}
</style>

<div class="p-6 w-full">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengembalian & Denda</h1>
            <p class="text-gray-500 mt-1 text-sm">Verifikasi kondisi aset yang dikembalikan, lalu konfirmasi pembayaran denda peminjam.</p>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid gap-4 mb-6 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rt-stat">
            <div class="rt-stat-icon" style="background:#fffbeb;color:#d97706"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Perlu Verifikasi</p><p class="text-xl font-bold text-gray-800">{{ $counts['verify'] }} <span class="text-sm font-medium text-gray-400">pengajuan</span></p></div>
        </div>
        <div class="rt-stat">
            <div class="rt-stat-icon" style="background:#eef2ff;color:#4f46e5"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Denda Berjalan</p><p class="text-xl font-bold text-gray-800">{{ $counts['fine'] }} <span class="text-sm font-medium text-gray-400">kasus</span>@if($counts['proof'] > 0)<span class="rt-pill rt-amber ml-1">{{ $counts['proof'] }} bukti baru</span>@endif</p></div>
        </div>
        <div class="rt-stat">
            <div class="rt-stat-icon" style="background:#fff1f2;color:#e11d48"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Denda Belum Lunas</p><p class="text-xl font-bold text-rose-600">Rp {{ number_format($counts['outstanding'], 0, ',', '.') }}</p></div>
        </div>
        <div class="rt-stat">
            <div class="rt-stat-icon" style="background:#ecfdf5;color:#059669"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div><p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Denda Terkumpul</p><p class="text-xl font-bold text-emerald-600">Rp {{ number_format($counts['collected'], 0, ',', '.') }}</p></div>
        </div>
    </div>

    {{-- (kartu denda terkumpul ditambahkan di atas) --}}
    {{-- Tab --}}
    @php
        $tabs = [
            'verify' => ['Perlu Verifikasi', $counts['verify']],
            'fine' => ['Denda Berjalan', $counts['fine']],
            'all' => ['Semua Riwayat', null],
        ];
    @endphp
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
        <div class="rt-tabs">
            @foreach($tabs as $key => [$label, $count])
                <a href="{{ route('returns.index', ['tab' => $key]) }}" class="rt-tab {{ $tab === $key ? 'active' : '' }}">
                    {{ $label }}
                    @if($count)<span class="rt-count">{{ $count }}</span>@endif
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('returns.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari peminjam / aset / SN" class="rt-input" style="padding-left:2.1rem;width:15rem">
            </div>
            @if($tab === 'all')
                <select name="fine" class="rt-input" style="width:auto" onchange="this.form.submit()">
                    <option value="">Semua status denda</option>
                    @foreach(['unpaid' => 'Belum dibayar', 'proof_submitted' => 'Menunggu konfirmasi', 'paid' => 'Lunas', 'waived' => 'Dibebaskan', 'none' => 'Tanpa denda'] as $v => $l)
                        <option value="{{ $v }}" @selected(request('fine') === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            @endif
            <button type="submit" class="rt-btn rt-btn-primary" style="padding:.6rem 1rem">Cari</button>
            @if(request('q') || request('fine'))
                <a href="{{ route('returns.index', ['tab' => $tab]) }}" class="rt-btn rt-btn-ghost" style="padding:.6rem 1rem">Reset</a>
            @endif
        </form>
    </div>

    {{-- Pesan sukses/error ditampilkan oleh layout utama --}}

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                        <th class="px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                        <th class="px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Aset</th>
                        <th class="px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Kembali</th>
                        <th class="px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                        <th class="px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                        <th class="px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($returns as $index => $return)
                    @php
                        $userName = $return->user->name ?? 'Pengguna dihapus';
                        $assetName = $return->asset->name ?? 'Aset dihapus';
                        [$fineLabel] = \App\Models\AssetReturn::fineLabel($return->fine_status);
                        $fineCss = ['unpaid' => 'rt-rose', 'proof_submitted' => 'rt-amber', 'paid' => 'rt-green', 'waived' => 'rt-slate'][$return->fine_status] ?? 'rt-slate';
                        $photos = array_values(array_filter([$return->photo_proof_1, $return->photo_proof_2, $return->photo_proof_3]));
                        $payload = [
                            'id' => $return->id,
                            'asset' => $assetName,
                            'user' => $userName,
                            'condition' => $return->condition,
                            'notes' => $return->notes,
                            'photos' => array_map(fn ($p) => asset('storage/' . $p), $photos),
                            'fine' => (float) $return->fine,
                            'fineMethod' => $return->fine_payment_method,
                            'fineProof' => $return->fine_payment_proof ? asset('storage/' . $return->fine_payment_proof) : null,
                            'fineNote' => $return->fine_note,
                            'fineStatus' => $return->fine_status,
                            'status' => $return->status,
                            'adminNote' => $return->admin_note,
                            'verifier' => $return->admin->name ?? null,
                            'returnDate' => $return->return_date?->translatedFormat('d F Y, H:i'),
                            'proofAt' => $return->fine_proof_submitted_at?->translatedFormat('d F Y, H:i'),
                            'paidAt' => $return->fine_paid_at?->translatedFormat('d F Y, H:i'),
                            'confirmer' => $return->fineConfirmer->name ?? null,
                            'requestUrl' => route('borrowing.show', $return->asset_request_id),
                            'receiptUrl' => in_array($return->fine_status, ['paid', 'waived']) ? route('returns.receipt', $return->id) : null,
                            'daysOpen' => in_array($return->fine_status, ['unpaid', 'proof_submitted']) ? (int) $return->updated_at->diffInDays(now()) : null,
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 text-sm font-medium text-slate-500">{{ $returns->firstItem() + $index }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">{{ strtoupper(substr($userName, 0, 2)) }}</div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $userName }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $return->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-bold text-gray-900">{{ $assetName }}</p>
                            <p class="text-xs font-mono text-slate-500">{{ $return->asset->serial_number ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm font-medium text-slate-700">{{ $return->return_date?->format('d M Y') }}</span>
                            <span class="block text-xs text-slate-400">{{ $return->return_date?->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-5 py-4">
                            @if(in_array($return->condition, ['good', 'available']))
                                <span class="rt-pill rt-green"><i></i>Baik</span>
                            @elseif($return->condition === 'maintenance')
                                <span class="rt-pill rt-amber"><i></i>Perlu Servis</span>
                            @else
                                <span class="rt-pill rt-rose"><i></i>Rusak</span>
                            @endif
                            @if($return->notes)
                                <div class="mt-1 text-xs text-slate-500 italic max-w-[150px] truncate" title="{{ $return->notes }}">"{{ Str::limit($return->notes, 20) }}"</div>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($return->status === 'pending')
                                <span class="rt-pill rt-amber">Perlu Diverifikasi</span>
                            @elseif($return->status === 'rejected')
                                <span class="rt-pill rt-rose" title="{{ $return->admin_note }}">Ditolak</span>
                            @else
                                <span class="rt-pill rt-green">Selesai</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($return->status === 'approved' && $return->fine > 0)
                                <p class="text-sm font-bold text-gray-900">Rp {{ number_format($return->fine, 0, ',', '.') }}</p>
                                <span class="rt-pill {{ $fineCss }} mt-1">{{ $fineLabel }}</span>
                                @if($payload['daysOpen'] !== null && $payload['daysOpen'] > 0)
                                    <span class="block text-[11px] text-gray-400 mt-0.5">{{ $payload['daysOpen'] }} hari berjalan</span>
                                @endif
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            @if($return->status === 'pending')
                                <button type="button" data-return='@json($payload)' onclick="openVerifyModal(this)"
                                    class="rt-btn rt-btn-primary"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Verifikasi</button>
                            @elseif($return->hasOutstandingFine())
                                <button type="button" data-return='@json($payload)' onclick="openFineModal(this)"
                                    class="rt-btn {{ $return->fine_status === 'proof_submitted' ? 'rt-btn-warn' : 'rt-btn-danger' }}">
                                    {{ $return->fine_status === 'proof_submitted' ? 'Cek Bukti Bayar' : 'Proses Denda' }}
                                </button>
                            @else
                                <button type="button" data-return='@json($payload)' onclick="openDetailModal(this)" class="rt-btn rt-btn-soft">Detail</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                            <p class="font-medium text-lg text-slate-500">
                                {{ ['verify' => 'Tidak ada pengembalian yang perlu diverifikasi', 'fine' => 'Tidak ada denda yang berjalan', 'all' => 'Belum ada pengembalian'][$tab] }}
                            </p>
                            <p class="text-sm">{{ request('q') || request('fine') ? 'Coba ubah kata kunci atau reset filter.' : 'Data akan muncul di sini saat ada pengajuan baru.' }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">{{ $returns->links() }}</div>
        @endif
    </div>
</div>

{{-- ================= MODAL VERIFIKASI ================= --}}
<div id="verifyModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeModals()"></div>
        <div class="relative rt-modal-box" style="max-width:34rem">
            <form id="verifyForm" method="POST" action="">
                @csrf
                <div class="rt-modal-head">
                    <div class="rt-modal-icon" style="background:#eef2ff;color:#4f46e5"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div class="min-w-0">
                        <h3 class="text-lg font-bold text-gray-900">Verifikasi Pengembalian</h3>
                        <p class="mt-0.5 text-sm text-gray-500">Periksa fisik <strong id="vAsset" class="text-gray-800"></strong> dari <strong id="vUser" class="text-gray-800"></strong>.</p>
                        <p id="vNotes" class="mt-1 text-xs italic text-gray-500"></p>
                    </div>
                </div>

                <div class="px-6 py-5 space-y-5">
                    <div>
                        <span class="rt-label">Bukti Foto Peminjam</span>
                        <div id="vPhotos" class="grid grid-cols-3 gap-2"></div>
                        <p id="vNoPhoto" class="rt-hint hidden">Tidak ada foto disertakan</p>
                    </div>

                    <div>
                        <span class="rt-label">Kondisi Fisik Sebenarnya</span>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="rt-radio"><input type="radio" name="final_condition" value="available" class="sr-only" required><span class="r-ok">Layak<br><small style="font-weight:500">stok bertambah</small></span></label>
                            <label class="rt-radio"><input type="radio" name="final_condition" value="maintenance" class="sr-only"><span class="r-svc">Perlu Servis<br><small style="font-weight:500">tiket perbaikan</small></span></label>
                            <label class="rt-radio"><input type="radio" name="final_condition" value="broken" class="sr-only"><span class="r-bad">Rusak Total<br><small style="font-weight:500">tiket perbaikan</small></span></label>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="rt-label">Denda (Opsional)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">Rp</span>
                                <input type="number" name="fine" min="0" max="999999999" step="1000" placeholder="0" class="rt-input" style="padding-left:2.2rem">
                            </div>
                            <p class="rt-hint">Jika diisi, peminjam wajib membayar.</p>
                        </div>
                        <div>
                            <label class="rt-label">Catatan Admin (Opsional)</label>
                            <input type="text" name="admin_note" maxlength="500" class="rt-input" placeholder="Mis. layar retak">
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2">
                    <button type="button" onclick="showReject()" class="rt-btn rt-btn-ghost" style="color:#be123c;border-color:#fecdd3">Tolak Pengajuan</button>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeModals()" class="rt-btn rt-btn-ghost">Batal</button>
                        <button type="submit" class="rt-btn rt-btn-primary">Verifikasi & Terima</button>
                    </div>
                </div>
            </form>

            <form id="rejectForm" method="POST" action="" class="hidden px-6 py-4 border-t border-rose-100 space-y-3" style="background:#fff1f2">
                @csrf
                <label class="rt-label" style="color:#be123c">Alasan penolakan (wajib, dilihat peminjam)</label>
                <textarea name="admin_note" rows="2" required minlength="5" maxlength="500" class="rt-input" placeholder="Contoh: Barang belum diserahkan / foto tidak sesuai"></textarea>
                <button type="submit" class="rt-btn rt-btn-danger">Tolak Pengembalian</button>
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL DENDA ================= --}}
<div id="fineModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeModals()"></div>
        <div class="relative rt-modal-box" style="max-width:30rem">
            <div class="rt-modal-head">
                <div class="rt-modal-icon" style="background:#fff1f2;color:#e11d48"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-gray-900">Konfirmasi Pembayaran Denda</h3>
                    <p class="mt-0.5 text-sm text-gray-500"><strong id="fUser" class="text-gray-800"></strong> &middot; <span id="fAsset"></span></p>
                </div>
            </div>

            <div class="px-6 py-5 space-y-4">
                <div class="rounded-xl px-4 py-3 flex items-center justify-between" style="background:#fff1f2;border:1px solid #fecdd3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#be123c">Total Denda</span>
                    <span class="text-2xl font-bold" style="color:#be123c" id="fAmount"></span>
                </div>

                <div id="fProofBox" class="hidden">
                    <span class="rt-label">Bukti dari peminjam (<span id="fMethod"></span>)</span>
                    <a id="fProofLink" href="#" target="_blank"><img id="fProofImg" src="" alt="Bukti pembayaran" class="max-h-56 rounded-xl border border-gray-200"></a>
                </div>
                <p id="fNoProof" class="rt-hint hidden" style="font-size:.78rem">Belum ada bukti diunggah. Jika peminjam membayar tunai langsung kepada Anda, Anda dapat langsung mengonfirmasi.</p>
                <p id="fNote" class="text-xs italic text-gray-500"></p>

                <div class="rt-tabs" style="display:flex;width:100%">
                    <button type="button" class="rt-tab active" style="flex:1;justify-content:center;border:0;cursor:pointer;background:none" data-act="confirm" onclick="setFineAction('confirm')">Lunas</button>
                    <button type="button" class="rt-tab" id="fTabReject" style="flex:1;justify-content:center;border:0;cursor:pointer;background:none" data-act="reject" onclick="setFineAction('reject')">Tolak Bukti</button>
                    <button type="button" class="rt-tab" style="flex:1;justify-content:center;border:0;cursor:pointer;background:none" data-act="waive" onclick="setFineAction('waive')">Bebaskan</button>
                </div>

                <form id="fConfirm" method="POST" action="" class="space-y-3" onsubmit="return confirm('Pastikan uang sudah diterima. Tandai denda ini LUNAS?')">
                    @csrf
                    <div>
                        <label class="rt-label">Metode pembayaran</label>
                        <select name="fine_payment_method" class="rt-input">
                            <option value="cash">Tunai</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="rt-label">Catatan (opsional)</label>
                        <input type="text" name="fine_note" maxlength="255" class="rt-input" placeholder="Mis. diterima langsung di ruang IT">
                    </div>
                    <button type="submit" class="rt-btn rt-btn-success rt-btn-block">Konfirmasi Sudah Lunas</button>
                </form>

                <form id="fReject" method="POST" action="" class="space-y-3 hidden">
                    @csrf
                    <div>
                        <label class="rt-label">Alasan bukti ditolak <span style="color:#e11d48">*</span></label>
                        <input type="text" name="fine_note" required minlength="5" maxlength="255" class="rt-input" placeholder="Mis. nominal tidak sesuai / foto buram">
                        <p class="rt-hint">Peminjam akan diminta mengunggah ulang bukti.</p>
                    </div>
                    <button type="submit" class="rt-btn rt-btn-warn rt-btn-block">Tolak Bukti Bayar</button>
                </form>

                <form id="fWaive" method="POST" action="" class="space-y-3 hidden" onsubmit="return confirm('Bebaskan denda ini? Tindakan tercatat di riwayat aset.')">
                    @csrf
                    <div>
                        <label class="rt-label">Alasan pembebasan <span style="color:#e11d48">*</span></label>
                        <input type="text" name="fine_note" required minlength="5" maxlength="255" class="rt-input" placeholder="Mis. kerusakan wajar / kebijakan manajemen">
                        <p class="rt-hint">Dicatat di riwayat aset untuk keperluan audit.</p>
                    </div>
                    <button type="submit" class="rt-btn rt-btn-danger rt-btn-block">Bebaskan Denda</button>
                </form>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-right">
                <button type="button" onclick="closeModals()" class="rt-btn rt-btn-ghost">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL DETAIL ================= --}}
<div id="detailModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeModals()"></div>
        <div class="relative rt-modal-box" style="max-width:32rem">
            <div class="rt-modal-head">
                <div class="rt-modal-icon" style="background:#eef2ff;color:#4f46e5"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <div class="min-w-0"><h3 class="text-lg font-bold text-gray-900">Detail Pengembalian</h3><p class="mt-0.5 text-sm text-gray-500"><strong id="dUser" class="text-gray-800"></strong> &middot; <span id="dAsset"></span></p></div>
            </div>
            <div class="px-6 py-5 space-y-3 text-sm" id="dBody"></div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-between gap-2">
                <a id="dRequest" href="#" class="rt-btn rt-btn-ghost">Lihat Peminjaman</a>
                <div class="flex gap-2">
                    <a id="dReceipt" href="#" target="_blank" class="rt-btn rt-btn-soft hidden">Kwitansi</a>
                    <button type="button" onclick="closeModals()" class="rt-btn rt-btn-primary">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var rupiah = function (n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(n || 0); };
    var base = "{{ url('/returns') }}";

    function readPayload(btn) {
        try { return JSON.parse(btn.getAttribute('data-return')); } catch (e) { return null; }
    }

    function openVerifyModal(btn) {
        var d = readPayload(btn);
        if (!d) { alert('Data tidak dapat dibaca, muat ulang halaman.'); return; }

        document.getElementById('vAsset').textContent = d.asset;
        document.getElementById('vUser').textContent = d.user;
        document.getElementById('vNotes').textContent = d.notes ? 'Catatan peminjam: "' + d.notes + '"' : '';

        var grid = document.getElementById('vPhotos');
        grid.innerHTML = '';
        (d.photos || []).forEach(function (url) {
            var a = document.createElement('a');
            a.href = url; a.target = '_blank';
            a.className = 'block h-24 rounded-lg overflow-hidden border border-slate-200 bg-slate-100';
            var img = document.createElement('img');
            img.src = url; img.className = 'w-full h-full object-cover';
            a.appendChild(img); grid.appendChild(a);
        });
        document.getElementById('vNoPhoto').classList.toggle('hidden', (d.photos || []).length > 0);

        var map = { good: 'available', maintenance: 'maintenance', broken: 'broken' };
        document.querySelectorAll('input[name="final_condition"]').forEach(function (el) { el.checked = el.value === (map[d.condition] || 'available'); });
        document.querySelector('#verifyForm input[name="fine"]').value = '';
        document.querySelector('#verifyForm input[name="admin_note"]').value = '';

        document.getElementById('verifyForm').action = base + '/' + d.id + '/verify';
        var rf = document.getElementById('rejectForm');
        rf.action = base + '/' + d.id + '/reject';
        rf.classList.add('hidden');
        document.getElementById('verifyModal').classList.remove('hidden');
    }

    function showReject() {
        var rf = document.getElementById('rejectForm');
        rf.classList.remove('hidden');
        rf.querySelector('textarea').focus();
    }

    function openFineModal(btn) {
        var d = readPayload(btn);
        if (!d) { alert('Data tidak dapat dibaca, muat ulang halaman.'); return; }

        document.getElementById('fUser').textContent = d.user;
        document.getElementById('fAsset').textContent = d.asset;
        document.getElementById('fAmount').textContent = rupiah(d.fine);
        document.getElementById('fNote').textContent = d.fineNote ? 'Catatan: ' + d.fineNote : '';

        var hasProof = d.fineStatus === 'proof_submitted' && d.fineProof;
        document.getElementById('fProofBox').classList.toggle('hidden', !hasProof);
        document.getElementById('fNoProof').classList.toggle('hidden', !!hasProof);
        document.getElementById('fTabReject').style.display = hasProof ? '' : 'none';
        document.querySelectorAll('#fWaive input, #fReject input, #fConfirm input[name=fine_note]').forEach(function (i) { i.value = ''; });
        setFineAction('confirm');
        if (hasProof) {
            document.getElementById('fProofImg').src = d.fineProof;
            document.getElementById('fProofLink').href = d.fineProof;
            document.getElementById('fMethod').textContent = d.fineMethod === 'cash' ? 'tunai' : 'transfer';
        }
        document.querySelector('#fConfirm select').value = d.fineMethod === 'transfer' ? 'transfer' : 'cash';

        document.getElementById('fConfirm').action = base + '/' + d.id + '/fine/confirm';
        document.getElementById('fReject').action = base + '/' + d.id + '/fine/reject-proof';
        document.getElementById('fWaive').action = base + '/' + d.id + '/fine/waive';
        document.getElementById('fineModal').classList.remove('hidden');
    }

    function setFineAction(act) {
        ['confirm', 'reject', 'waive'].forEach(function (a) {
            var f = document.getElementById({ confirm: 'fConfirm', reject: 'fReject', waive: 'fWaive' }[a]);
            f.classList.toggle('hidden', a !== act);
        });
        document.querySelectorAll('#fineModal [data-act]').forEach(function (b) { b.classList.toggle('active', b.dataset.act === act); });
    }

    function row(label, value) {
        if (value === null || value === undefined || value === '') return '';
        var d = document.createElement('div');
        d.className = 'flex justify-between gap-4 py-1.5 border-b border-dashed border-gray-100';
        var l = document.createElement('span'); l.className = 'text-gray-500'; l.textContent = label;
        var v = document.createElement('span'); v.className = 'font-semibold text-gray-800 text-right'; v.textContent = value;
        d.appendChild(l); d.appendChild(v);
        return d;
    }

    function openDetailModal(btn) {
        var d = readPayload(btn);
        if (!d) { alert('Data tidak dapat dibaca, muat ulang halaman.'); return; }
        document.getElementById('dUser').textContent = d.user;
        document.getElementById('dAsset').textContent = d.asset;
        var labels = { available: 'Baik', good: 'Baik', maintenance: 'Perlu Servis', broken: 'Rusak' };
        var fineLabels = { none: 'Tanpa denda', unpaid: 'Belum dibayar', proof_submitted: 'Menunggu konfirmasi', paid: 'Lunas', waived: 'Dibebaskan' };
        var body = document.getElementById('dBody');
        body.innerHTML = '';
        [
            row('Tanggal kembali', d.returnDate),
            row('Kondisi akhir', labels[d.condition] || d.condition),
            row('Status pengajuan', { pending: 'Menunggu verifikasi', approved: 'Diterima', rejected: 'Ditolak' }[d.status]),
            row('Diverifikasi oleh', d.verifier),
            row('Catatan peminjam', d.notes),
            row('Catatan admin', d.adminNote),
            d.fine > 0 ? row('Denda', rupiah(d.fine)) : '',
            d.fine > 0 ? row('Status denda', fineLabels[d.fineStatus]) : '',
            row('Bukti bayar dikirim', d.proofAt),
            row('Dibayar / dibebaskan', d.paidAt),
            row('Dikonfirmasi oleh', d.confirmer),
            row('Catatan denda', d.fineNote)
        ].forEach(function (n) { if (n) body.appendChild(n); });

        (d.photos || []).length && (function () {
            var g = document.createElement('div'); g.className = 'grid grid-cols-3 gap-2 pt-2';
            d.photos.forEach(function (u) { var a = document.createElement('a'); a.href = u; a.target = '_blank'; var i = document.createElement('img'); i.src = u; i.className = 'h-20 w-full object-cover rounded-lg border border-gray-200'; a.appendChild(i); g.appendChild(a); });
            body.appendChild(g);
        })();
        if (d.fineProof) {
            var a = document.createElement('a'); a.href = d.fineProof; a.target = '_blank';
            var i = document.createElement('img'); i.src = d.fineProof; i.className = 'max-h-40 rounded-lg border border-gray-200 mt-2'; a.appendChild(i); body.appendChild(a);
        }

        document.getElementById('dRequest').href = d.requestUrl;
        var rc = document.getElementById('dReceipt');
        rc.classList.toggle('hidden', !d.receiptUrl);
        if (d.receiptUrl) rc.href = d.receiptUrl;
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeModals() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('verifyModal').classList.add('hidden');
        document.getElementById('fineModal').classList.add('hidden');
    }
</script>
@endsection
