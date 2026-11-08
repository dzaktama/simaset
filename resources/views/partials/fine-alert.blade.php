@if(($fines ?? collect())->isNotEmpty())
    <div class="mb-6 bg-rose-50 border border-rose-200 rounded-xl p-4">
        <p class="text-sm font-bold text-rose-700">Anda memiliki denda pengembalian yang belum diselesaikan</p>
        <ul class="mt-2 space-y-1.5">
            @foreach($fines as $fine)
                <li class="flex flex-wrap items-center justify-between gap-2 text-sm">
                    <span class="text-rose-800">
                        {{ $fine->asset->name ?? 'Aset' }} &middot;
                        <span class="font-mono font-bold">Rp {{ number_format($fine->fine, 0, ',', '.') }}</span>
                        @if($fine->fine_status === 'proof_submitted')
                            <span class="text-amber-700">(bukti bayar menunggu konfirmasi admin)</span>
                        @elseif($fine->fine_note)
                            <span class="text-rose-600">({{ $fine->fine_note }})</span>
                        @endif
                    </span>
                    <a href="{{ route('borrowing.show', $fine->asset_request_id) }}" class="text-xs font-bold text-rose-700 underline hover:text-rose-900">
                        {{ $fine->fine_status === 'proof_submitted' ? 'Lihat status' : 'Bayar / unggah bukti' }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
