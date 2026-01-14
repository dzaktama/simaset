@extends('layouts.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Peta Lokasi Aset
            </h1>
            <p class="text-gray-600 mt-1">Visualisasi posisi barang berdasarkan Lorong & Rak.</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-blue-50 px-4 py-2 rounded-lg border border-blue-100">
                <span class="block text-xs text-blue-600 font-bold uppercase">Total Lorong</span>
                <span class="text-xl font-bold text-gray-800">{{ $totalLorong ?? 0 }}</span>
            </div>
            <div class="bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-100">
                <span class="block text-xs text-indigo-600 font-bold uppercase">Total Rak Terisi</span>
                <span class="text-xl font-bold text-gray-800">{{ $totalRak ?? 0 }}</span>
            </div>
            <a href="{{ route('assets.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center h-full">
                Kembali ke List
            </a>
        </div>
    </div>

    @if($mapData->isEmpty())
        <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-dashed border-gray-300">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0121 18.382V7.618a1 1 0 01-1.447-.894L15 7m0 13V7m0 0L9 4"></path></svg>
            <h3 class="text-lg font-medium text-gray-900">Belum Ada Data Lokasi</h3>
            <p class="text-gray-500 mt-1">Edit data aset dan isi kolom "Lorong" & "Rak" untuk melihat peta.</p>
        </div>
    @else
        
        {{-- VISUALISASI PER LORONG --}}
        <div class="space-y-8">
            @foreach($mapData as $lorongName => $raks)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Header Lorong --}}
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
                            LORONG: {{ strtoupper($lorongName) }}
                        </h2>
                        <span class="text-sm text-gray-500 bg-white px-2 py-1 rounded border">{{ $raks->count() }} Rak Terisi</span>
                    </div>

                    {{-- Grid Rak (KAI Style) --}}
                    <div class="p-6 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($raks as $rakName => $items)
                            <button onclick="showRakDetails('{{ $lorongName }}', '{{ $rakName }}', {{ json_encode($items) }})" 
                                class="group relative flex flex-col items-center justify-center p-4 rounded-lg border-2 border-blue-100 bg-blue-50 hover:bg-blue-100 hover:border-blue-300 transition cursor-pointer text-center">
                                
                                {{-- Icon Rak --}}
                                <div class="mb-2 text-blue-400 group-hover:text-blue-600 transition">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                </div>
                                
                                <span class="font-bold text-gray-800 text-sm mb-1">{{ strtoupper($rakName) }}</span>
                                <span class="text-xs text-gray-500 bg-white px-2 py-0.5 rounded-full border border-blue-100">
                                    {{ $items->count() }} Item
                                </span>

                                {{-- Tooltip Hover --}}
                                <div class="absolute opacity-0 group-hover:opacity-100 bottom-full mb-2 bg-gray-900 text-white text-xs px-2 py-1 rounded whitespace-nowrap transition pointer-events-none z-10">
                                    Klik untuk lihat detail
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    @endif
</div>

{{-- MODAL DETAIL RAK (Hidden by default) --}}
<div id="rakModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRakModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Detail Rak</h3>
                        <div class="mt-4 max-h-60 overflow-y-auto">
                            <ul id="modalItemList" class="divide-y divide-gray-200">
                                {{-- Item diisi via JS --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeRakModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showRakDetails(lorong, rak, items) {
        document.getElementById('modalTitle').innerText = 'Isi Rak: ' + rak + ' (Lorong ' + lorong + ')';
        const list = document.getElementById('modalItemList');
        list.innerHTML = '';

        items.forEach(item => {
            // Tentukan warna status
            let statusColor = item.status === 'available' ? 'text-green-600 bg-green-50' : 'text-yellow-600 bg-yellow-50';
            
            list.innerHTML += `
                <li class="py-3 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-gray-100 rounded flex-shrink-0 overflow-hidden">
                             ${item.image ? `<img src="/storage/${item.image}" class="h-full w-full object-cover">` : '<span class="flex items-center justify-center h-full text-xs text-gray-400">No Pic</span>'}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">${item.name}</p>
                            <p class="text-xs text-gray-500">${item.serial_number}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusColor}">
                        ${item.status}
                    </span>
                </li>
            `;
        });

        document.getElementById('rakModal').classList.remove('hidden');
    }

    function closeRakModal() {
        document.getElementById('rakModal').classList.add('hidden');
    }
</script>
@endsection