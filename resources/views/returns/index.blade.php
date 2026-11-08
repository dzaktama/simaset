@extends('layouts.main')

@section('container')
<div class="px-6 py-6 w-full font-sans text-slate-800">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengembalian & Denda</h1>
            <p class="text-slate-500 mt-1 text-sm">Verifikasi kondisi aset yang dikembalikan, lalu konfirmasi pembayaran denda peminjam.</p>
        </div>

        <div class="flex gap-3">
            <div class="bg-amber-50 border border-amber-200 px-4 py-2 rounded-xl">
                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">Perlu Verifikasi</p>
                <p class="text-lg font-bold text-amber-700 leading-none">{{ $counts['verify'] }}</p>
            </div>
            <div class="bg-rose-50 border border-rose-200 px-4 py-2 rounded-xl">
                <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider">Denda Belum Lunas</p>
                <p class="text-lg font-bold text-rose-700 leading-none">Rp {{ number_format($counts['outstanding'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    @php
        $tabs = [
            'verify' => ['Perlu Verifikasi', $counts['verify']],
            'fine' => ['Denda Berjalan', $counts['fine']],
            'all' => ['Semua Riwayat', null],
        ];
    @endphp
    <div class="flex gap-2 mb-4 border-b border-slate-200">
        @foreach($tabs as $key => [$label, $count])
            <a href="{{ route('returns.index', ['tab' => $key]) }}"
               class="px-4 py-2 text-sm font-bold -mb-px border-b-2 {{ $tab === $key ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                {{ $label }}
                @if($count)
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === $key ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">{{ $count }}</span>
                @endif
                @if($key === 'fine' && $counts['proof'] > 0)
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-700">{{ $counts['proof'] }} bukti baru</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Pesan sukses/error ditampilkan oleh layout utama --}}

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider w-12">No</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Peminjam</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Aset</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Kembali</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Kondisi</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Denda</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($returns as $index => $return)
                    @php
                        $userName = $return->user->name ?? 'Pengguna dihapus';
                        $assetName = $return->asset->name ?? 'Aset dihapus';
                        [$fineLabel, $fineClass] = \App\Models\AssetReturn::fineLabel($return->fine_status);
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
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium text-slate-500">{{ $returns->firstItem() + $index }}</td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $userName }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $return->user->email ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-bold text-indigo-900">{{ $assetName }}</p>
                            <p class="text-xs font-mono text-slate-500">{{ $return->asset->serial_number ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-medium text-slate-700">{{ $return->return_date?->format('d M Y') }}</span>
                            <span class="block text-xs text-slate-400">{{ $return->return_date?->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-4 py-3">
                            @if(in_array($return->condition, ['good', 'available']))
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">Baik</span>
                            @elseif($return->condition === 'maintenance')
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">Perlu Servis</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-rose-100 text-rose-800 border border-rose-200">Rusak</span>
                            @endif
                            @if($return->notes)
                                <div class="mt-1 text-xs text-slate-500 italic max-w-[150px] truncate" title="{{ $return->notes }}">"{{ Str::limit($return->notes, 20) }}"</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($return->status === 'pending')
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">Perlu Diverifikasi</span>
                            @elseif($return->status === 'rejected')
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200" title="{{ $return->admin_note }}">Ditolak</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">Selesai</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($return->status === 'approved' && $return->fine > 0)
                                <p class="text-sm font-mono font-bold text-slate-800">Rp {{ number_format($return->fine, 0, ',', '.') }}</p>
                                <span class="inline-flex mt-0.5 px-2 py-0.5 rounded text-[11px] font-bold border {{ $fineClass }}">{{ $fineLabel }}</span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if($return->status === 'pending')
                                <button type="button" data-return='@json($payload)' onclick="openVerifyModal(this)"
                                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm">Verifikasi</button>
                            @elseif($return->hasOutstandingFine())
                                <button type="button" data-return='@json($payload)' onclick="openFineModal(this)"
                                    class="px-3 py-1.5 {{ $return->fine_status === 'proof_submitted' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-rose-600 hover:bg-rose-700' }} text-white text-xs font-bold rounded-lg shadow-sm">
                                    {{ $return->fine_status === 'proof_submitted' ? 'Cek Bukti Bayar' : 'Proses Denda' }}
                                </button>
                            @else
                                <a href="{{ route('borrowing.show', $return->asset_request_id) }}" class="text-xs font-bold text-indigo-600 hover:underline">Detail</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                            <p class="font-medium text-lg text-slate-500">
                                {{ ['verify' => 'Tidak ada pengembalian yang perlu diverifikasi', 'fine' => 'Tidak ada denda yang berjalan', 'all' => 'Belum ada pengembalian'][$tab] }}
                            </p>
                            <p class="text-sm">Data akan muncul di sini saat ada pengajuan baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">{{ $returns->links() }}</div>
        @endif
    </div>
</div>

{{-- ================= MODAL VERIFIKASI ================= --}}
<div id="verifyModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm" onclick="closeModals()"></div>
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl w-full max-w-lg">
            <form id="verifyForm" method="POST" action="">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Verifikasi Pengembalian</h3>
                        <p class="mt-1 text-sm text-slate-500">Periksa fisik <strong id="vAsset" class="text-slate-800"></strong> dari <strong id="vUser" class="text-slate-800"></strong>.</p>
                        <p id="vNotes" class="mt-1 text-xs italic text-slate-500"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Bukti Foto Peminjam</label>
                        <div id="vPhotos" class="grid grid-cols-3 gap-2"></div>
                        <p id="vNoPhoto" class="text-xs text-slate-400 hidden">Tidak ada foto disertakan</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kondisi Fisik Sebenarnya</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="flex cursor-pointer"><input type="radio" name="final_condition" value="available" class="peer sr-only" required>
                                <span class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 text-slate-600 text-center text-xs font-bold peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700">Layak (Stok +)</span></label>
                            <label class="flex cursor-pointer"><input type="radio" name="final_condition" value="maintenance" class="peer sr-only">
                                <span class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 text-slate-600 text-center text-xs font-bold peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700">Perlu Servis</span></label>
                            <label class="flex cursor-pointer"><input type="radio" name="final_condition" value="broken" class="peer sr-only">
                                <span class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 text-slate-600 text-center text-xs font-bold peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700">Rusak Total</span></label>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Perlu Servis / Rusak otomatis membuat tiket maintenance.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Denda (Opsional)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-sm">Rp</span>
                            <input type="number" name="fine" min="0" max="999999999" step="1000" placeholder="0"
                                   class="block w-full rounded-lg border-slate-300 pl-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Jika diisi, peminjam wajib membayar. Konfirmasi lunas dilakukan di tab "Denda Berjalan".</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Catatan Admin (Opsional)</label>
                        <input type="text" name="admin_note" maxlength="500" class="block w-full rounded-lg border-slate-300 text-sm">
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-3 flex flex-wrap justify-between gap-2">
                    <button type="button" onclick="showReject()" class="px-4 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50 rounded-lg">Tolak Pengajuan</button>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeModals()" class="px-4 py-2 text-sm font-medium bg-white border border-slate-300 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Verifikasi & Terima</button>
                    </div>
                </div>
            </form>

            {{-- Form tolak (terpisah, alasan wajib) --}}
            <form id="rejectForm" method="POST" action="" class="hidden p-6 border-t border-slate-200 space-y-3 bg-rose-50">
                @csrf
                <label class="block text-sm font-bold text-rose-700">Alasan penolakan (wajib, dilihat peminjam)</label>
                <textarea name="admin_note" rows="2" required minlength="5" maxlength="500" class="w-full rounded-lg border-rose-300 text-sm" placeholder="Contoh: Barang belum diserahkan / foto tidak sesuai"></textarea>
                <button type="submit" class="px-4 py-2 text-sm font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-lg">Tolak Pengembalian</button>
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL DENDA ================= --}}
<div id="fineModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm" onclick="closeModals()"></div>
        <div class="relative bg-white rounded-2xl overflow-hidden shadow-xl w-full max-w-md p-6 space-y-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Konfirmasi Pembayaran Denda</h3>
                <p class="mt-1 text-sm text-slate-500"><strong id="fUser" class="text-slate-800"></strong> &middot; <span id="fAsset"></span></p>
                <p class="mt-2 text-2xl font-mono font-bold text-rose-700" id="fAmount"></p>
            </div>

            <div id="fProofBox" class="hidden space-y-1">
                <p class="text-xs font-bold text-slate-700">Bukti dari peminjam (<span id="fMethod"></span>)</p>
                <a id="fProofLink" href="#" target="_blank"><img id="fProofImg" src="" alt="Bukti pembayaran" class="max-h-56 rounded-lg border border-slate-200"></a>
            </div>
            <p id="fNoProof" class="text-xs text-slate-500 hidden">Belum ada bukti diunggah. Jika peminjam membayar tunai langsung kepada Anda, Anda dapat langsung mengonfirmasi.</p>
            <p id="fNote" class="text-xs italic text-slate-500"></p>

            <form id="fConfirm" method="POST" action="" class="space-y-2" onsubmit="return confirm('Pastikan uang sudah diterima. Tandai denda ini LUNAS?')">
                @csrf
                <select name="fine_payment_method" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="cash">Dibayar tunai</option>
                    <option value="transfer">Dibayar via transfer</option>
                </select>
                <button type="submit" class="w-full px-4 py-2 text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">Konfirmasi Sudah Lunas</button>
            </form>

            <form id="fReject" method="POST" action="" class="space-y-2">
                @csrf
                <input type="text" name="fine_note" required minlength="5" maxlength="255" placeholder="Alasan bukti ditolak (wajib)" class="w-full rounded-lg border-slate-300 text-sm">
                <button type="submit" class="w-full px-4 py-2 text-sm font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-lg">Tolak Bukti Bayar</button>
            </form>

            <form id="fWaive" method="POST" action="" class="space-y-2 pt-3 border-t border-slate-200" onsubmit="return confirm('Bebaskan denda ini? Tindakan tercatat di riwayat aset.')">
                @csrf
                <input type="text" name="fine_note" required minlength="5" maxlength="255" placeholder="Alasan pembebasan denda (wajib)" class="w-full rounded-lg border-slate-300 text-sm">
                <button type="submit" class="w-full px-4 py-2 text-sm font-bold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg">Bebaskan Denda</button>
            </form>

            <button type="button" onclick="closeModals()" class="w-full text-sm text-slate-500 hover:text-slate-700">Tutup</button>
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
        document.getElementById('fReject').classList.toggle('hidden', !hasProof);
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

    function closeModals() {
        document.getElementById('verifyModal').classList.add('hidden');
        document.getElementById('fineModal').classList.add('hidden');
    }
</script>
@endsection
