@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-7xl px-4 py-8">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Peta Lokasi Aset
            </h1>
            <p class="text-gray-600 mt-1">Denah posisi aset berdasarkan Lorong dan Rak.</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-white shadow-sm px-4 py-2 rounded-lg border border-gray-200">
                <span class="block text-xs text-gray-500 font-bold uppercase">Total Lorong</span>
                <span class="text-xl font-bold text-blue-600">{{ $totalLorong ?? 0 }}</span>
            </div>
            <div class="bg-white shadow-sm px-4 py-2 rounded-lg border border-gray-200">
                <span class="block text-xs text-gray-500 font-bold uppercase">Total Rak</span>
                <span class="text-xl font-bold text-indigo-600">{{ $totalRak ?? 0 }}</span>
            </div>
            <a href="{{ route('assets.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center h-full">
                &larr; List Aset
            </a>
        </div>
    </div>

    @if($mapData->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl shadow-sm border-2 border-dashed border-gray-300">
            <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0121 18.382V7.618a1 1 0 01-1.447-.894L15 7m0 13V7m0 0L9 4"></path></svg>
            <h3 class="text-lg font-medium text-gray-900">Peta Masih Kosong</h3>
            <p class="text-gray-500 mt-2 max-w-md mx-auto">Belum ada aset yang memiliki data lokasi lengkap. Silakan edit aset dan isi kolom <strong>Lorong</strong> & <strong>Rak</strong>.</p>
            <a href="{{ route('assets.create') }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tambah Aset</a>
        </div>
    @else
        
        {{-- LOOPING LORONG --}}
        <div class="space-y-8">
            @foreach($mapData as $lorongName => $raks)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Header Lorong --}}
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold">
                                {{ strtoupper(substr($lorongName, 0, 1)) }}
                            </span>
                            LORONG: {{ strtoupper($lorongName) }}
                        </h2>
                        <span class="text-xs font-semibold text-gray-500 bg-white px-3 py-1 rounded-full border">
                            {{ $raks->count() }} Rak Terisi
                        </span>
                    </div>

                    {{-- Grid Rak (Visualisasi Kotak) --}}
                    <div class="p-6 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($raks as $rakName => $items)
                            <button onclick="showRakDetails('{{ $lorongName }}', '{{ $rakName }}', {{ json_encode($items) }})" 
                                class="group relative flex flex-col items-center justify-center p-4 rounded-xl border-2 border-blue-50 bg-blue-50/50 hover:bg-blue-100 hover:border-blue-400 transition cursor-pointer text-center h-full">
                                
                                {{-- Icon Rak --}}
                                <div class="mb-3 text-blue-300 group-hover:text-blue-600 transition transform group-hover:scale-110 duration-200">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                                </div>
                                
                                <span class="font-bold text-gray-800 text-base mb-1">{{ strtoupper($rakName) }}</span>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">
                                    {{ $items->count() }} Item
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    @endif
</div>

{{-- MODAL DETAIL RAK (Popup) --}}
<div id="rakModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeRakModal()"></div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all w-full max-w-lg relative z-10">
            
            {{-- Modal Header --}}
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center gap-2" id="modalTitle">
                    <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m8-2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Detail Rak
                </h3>
                <button onclick="closeRakModal()" class="text-blue-100 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Modal Body (List Item) --}}
            <div class="p-0 max-h-[60vh] overflow-y-auto bg-gray-50">
                <ul id="modalItemList" class="divide-y divide-gray-200">
                    {{-- Item akan diisi oleh Javascript --}}
                </ul>
            </div>

            {{-- Modal Footer --}}
            <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="closeRakModal()" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showRakDetails(lorong, rak, items) {
        document.getElementById('modalTitle').innerHTML = `Isi Rak <span class="text-blue-200 mx-1">|</span> ${rak} <span class="text-sm font-normal text-blue-100 ml-1">(Lorong ${lorong})</span>`;
        const list = document.getElementById('modalItemList');
        list.innerHTML = '';

        if(items.length === 0) {
            list.innerHTML = '<li class="p-6 text-center text-gray-500">Rak kosong</li>';
        } else {
            items.forEach(item => {
                // Tentukan warna status badge
                let statusClass = 'bg-gray-100 text-gray-800';
                if(item.status === 'available') statusClass = 'bg-green-100 text-green-800 border-green-200';
                else if(item.status === 'deployed') statusClass = 'bg-blue-100 text-blue-800 border-blue-200';
                else if(item.status === 'maintenance') statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                else if(item.status === 'broken') statusClass = 'bg-red-100 text-red-800 border-red-200';

                // Image placeholder logic
                let imgHtml = item.image 
                    ? `<img src="/storage/${item.image}" class="h-12 w-12 rounded-lg object-cover border border-gray-200">`
                    : `<div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center text-gray-400 border border-gray-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>`;

                list.innerHTML += `
                    <li class="p-4 hover:bg-white transition flex items-center gap-4 group">
                        <div class="shrink-0">
                            ${imgHtml}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate group-hover:text-blue-600 transition">${item.name}</p>
                            <p class="text-xs text-gray-500 font-mono mt-0.5">${item.serial_number}</p>
                            <p class="text-xs text-gray-400 mt-0.5">${item.category ?? 'Tanpa Kategori'}</p>
                        </div>
                        <div class="shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${statusClass}">
                                ${item.status}
                            </span>
                            <a href="/assets/${item.id}" class="block text-right text-xs text-blue-500 hover:text-blue-700 mt-1 font-medium">Detail &rarr;</a>
                        </div>
                    </li>
                `;
            });
        }

        const modal = document.getElementById('rakModal');
        modal.classList.remove('hidden');
        // Simple animation
        setTimeout(() => {
            modal.firstElementChild.classList.add('opacity-100');
        }, 10);
    }

    function closeRakModal() {
        const modal = document.getElementById('rakModal');
        modal.classList.add('hidden');
    }
</script>
@endsection