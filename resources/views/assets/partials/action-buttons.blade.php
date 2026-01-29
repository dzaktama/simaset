<div class="flex items-center justify-center gap-2">
    {{-- Generate QR Code Base64 --}}
    @php
        $scanUrl = route('assets.scan', $asset->id);
        $qrCodeBase64 = '';
        try {
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode(
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->margin(1)->generate($scanUrl)
            );
        } catch (\Exception $e) {}
    @endphp

    {{-- BUTTON: DETAIL --}}
    <button onclick="openDetailModal({{ json_encode($asset) }}, {{ json_encode($asset->holder) }}, '{{ $qrCodeBase64 }}')" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 hover:text-indigo-800 transition border border-indigo-200" title="Detail">
        Detail
    </button>

    @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
        {{-- BUTTON: EDIT --}}
        <a href="{{ route('assets.edit', $asset->id) }}" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 border border-yellow-200 transition" title="Edit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        </a>

        {{-- BUTTON: DELETE --}}
        <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus aset ini?');" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-200 transition" title="Hapus">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </form>

        {{-- BUTTON: SERVICE (Hanya untuk status broken atau lihat tiket untuk maintenance) --}}
        @if($asset->status === 'broken')
            {{-- Aset RUSAK - tampilkan tombol "Buat Tiket Perbaikan" --}}
            <a href="{{ route('maintenances.create', ['asset_id' => $asset->id]) }}" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-200 transition" title="Perbaiki Aset Rusak">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </a>
        @elseif($asset->status === 'maintenance')
            {{-- Aset dalam MAINTENANCE - tampilkan tombol "Lihat Tiket Aktif" --}}
            @php
                $activeTicket = \App\Models\Maintenance::where('asset_id', $asset->id)->where('status', 'on_process')->first();
            @endphp
            @if($activeTicket)
                <a href="{{ route('maintenances.show', $activeTicket) }}" class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 border border-orange-200 transition" title="Lihat Tiket Aktif">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </a>
            @endif
        @endif
        {{-- Status available/deployed: Tidak ada tombol service karena aset tidak butuh perbaikan --}}

    @elseif(auth()->user()->role == 'service_center')
        {{-- BUTTON: SERVICE (SC) - Hanya untuk status broken atau lihat tiket untuk maintenance --}}
        @if($asset->status === 'broken')
            {{-- Aset RUSAK --}}
            <a href="{{ route('maintenances.create', ['asset_id' => $asset->id]) }}" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-200 transition" title="Perbaiki Aset Rusak">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </a>
        @elseif($asset->status === 'maintenance')
            {{-- Aset dalam MAINTENANCE --}}
            @php
                $activeTicketSC = \App\Models\Maintenance::where('asset_id', $asset->id)->where('status', 'on_process')->first();
            @endphp
            @if($activeTicketSC)
                <a href="{{ route('maintenances.show', $activeTicketSC) }}" class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 border border-orange-200 transition" title="Lihat Tiket Aktif">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </a>
            @endif
        @endif

    @else
        {{-- USER BUTTONS --}}
        @if($asset->quantity > 0 && $asset->status == 'available')
            <button onclick="openLoanModal({{ json_encode($asset) }})" class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs font-bold">Pinjam</button>
        @elseif($asset->status == 'deployed')
            <button onclick="openDetailModal({{ json_encode($asset) }}, {{ json_encode($asset->holder) }}, '{{ $qrCodeBase64 }}')" class="p-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition text-xs font-bold">Booking</button>
        @else
            <button disabled class="p-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed border border-gray-200 text-xs">Pinjam</button>
        @endif
    @endif
</div>
