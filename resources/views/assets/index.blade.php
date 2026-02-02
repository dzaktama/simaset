@extends('layouts.main')

@section('container')
<div class="w-full mx-auto px-4 py-8">
    
    {{-- Header & Stats --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Daftar Aset IT</h1>
            <p class="text-gray-600 mt-1">Kelola inventaris, stok, dan lokasi aset.</p>
        </div>
        
        <div class="flex gap-3">
            @if(in_array(auth()->user()->role?->slug, ['admin', 'super_admin']))
            <a href="{{ route('assets.create') }}" class="group relative inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-500/30 hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Aset
            </a>
            @endif
            @if(in_array(auth()->user()->role?->slug, ['admin', 'super_admin']))
                <a href="{{ route('assets.map') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-gray-700 transition-all duration-200 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 text-gray-700">
                    <svg class="w-5 h-5 mr-2 -ml-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    Peta Lokasi
                </a>
            @endif
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <form action="{{ route('assets.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            {{-- Search Input --}}
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="pl-10 block w-full rounded-lg border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5" placeholder="Cari nama aset, serial number, atau kategori...">
            </div>

            {{-- Status Filter --}}
            <div>
                <select name="status" class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5">
                    <option value="all">Semua Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="deployed" {{ request('status') == 'deployed' ? 'selected' : '' }}>Deployed</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="broken" {{ request('status') == 'broken' ? 'selected' : '' }}>Broken</option>
                </select>
            </div>

            {{-- Filter Button --}}
            <div>
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-lg text-sm px-5 py-2.5 transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Table Container --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th scope="col" class="px-6 py-4">Info Aset</th>
                        <th scope="col" class="px-6 py-4">Kategori & Lokasi</th>
                        <th scope="col" class="px-6 py-4">Status & Stok</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        // Group assets by name
                        $groupedAssets = $assets->groupBy('name');
                    @endphp

                    @forelse ($groupedAssets as $name => $group)
                        @if ($group->count() > 1)
                            {{-- PARENT ROW (GROUP) --}}
                            <tbody x-data="{ expanded: false }" class="border-b border-gray-100 last:border-0">
                                <tr class="bg-white hover:bg-gray-50 transition cursor-pointer" @click="expanded = !expanded">
                                    {{-- Kolom 1: Info Grup --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 relative">
                                                @if($group->first()->image && $group->first()->image !== 'null')
                                                    <img class="h-full w-full object-cover" src="/storage/{{ $group->first()->image }}" alt="" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'h-full w-full flex items-center justify-center text-gray-400\'><svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';">
                                                @else
                                                    <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                                <div class="absolute bottom-0 right-0 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded-tl font-bold">{{ $group->count() }} Unit</div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $name }}</div>
                                                <div class="text-xs text-gray-500 font-mono mt-1">
                                                    {{ $group->count() > 1 ? $group->count() . ' Unit (Berbagai SN)' : $group->first()->serial_number }}
                                                </div>
                                                <div class="text-xs text-indigo-600 font-medium flex items-center gap-1 mt-1">
                                                    <span x-text="expanded ? 'Tutup Daftar' : 'Lihat ' + {{ $group->count() }} + ' Unit'"></span>
                                                    <svg x-show="!expanded" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                                    <svg x-show="expanded" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- Kolom 2: Kategori (Parent) --}}
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">{{ $group->first()->category ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 mt-1">Berbagai Lokasi</div>
                                    </td>

                                    {{-- Kolom 3: Summary Status --}}
                                    <td class="px-6 py-4">
                                        <div class="flex gap-1 flex-wrap">
                                            @php
                                                $statusCounts = $group->countBy('status');
                                            @endphp
                                            @foreach($statusCounts as $status => $count)
                                                @php
                                                    $colors = [
                                                        'available' => 'bg-green-100 text-green-800 border-green-200',
                                                        'deployed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                        'maintenance' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                        'broken' => 'bg-red-100 text-red-800 border-red-200',
                                                        'lost' => 'bg-gray-100 text-gray-800 border-gray-200',
                                                    ];
                                                    $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $colorClass }}">
                                                    {{ $count }} {{ ucfirst($status) }}
                                                </span>
                                            @endforeach
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1 group/stock">
                                                Total {{ $group->sum('quantity') }}
                                                @if(in_array(auth()->user()->role?->slug, ['admin', 'super_admin']))
                                                <button type="button" 
                                                        onclick="event.stopPropagation(); openAddStockModal({{ $group->first()->makeHidden(['image', 'image2', 'image3']) }})" 
                                                        class="ml-1 p-0.5 rounded hover:bg-indigo-200 text-indigo-500 hover:text-indigo-800 transition" 
                                                        title="Tambah Stok Aset Ini">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                                </button>
                                                @endif
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Kolom 4: Aksi Parent (Expand Toggle) --}}
                                    <td class="px-6 py-4 text-center">
                                        <button class="text-gray-400 hover:text-indigo-600 transition">
                                            <svg class="w-6 h-6 transform transition-transform duration-200" :class="{'rotate-180': expanded}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                {{-- CHILD ROWS (Items) --}}
                                @foreach ($group as $asset)
                                    <tr x-show="expanded" 
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="bg-gray-50/80 border-l-4 border-l-indigo-500 hover:bg-indigo-50/30 transition duration-150">
                                        
                                        {{-- Child Kolom 1: Image + Name + SN --}}
                                        <td class="px-6 py-3 pl-12">
                                            <div class="flex items-center gap-4">
                                                {{-- Garis Hierarki Visual --}}
                                                <div class="absolute left-6 w-4 h-8 border-b-2 border-l-2 border-indigo-200 rounded-bl-xl -translate-y-4"></div>
                                                
                                                <div class="flex-shrink-0 h-10 w-10 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 relative group z-10">
                                                    @if($asset->image && $asset->image !== 'null')
                                                        <img class="h-full w-full object-cover" src="/storage/{{ $asset->image }}" alt="" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'h-full w-full flex items-center justify-center text-gray-400\'><svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';">
                                                    @else
                                                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-700">{{ $asset->name }}</div>
                                                    <div class="text-[10px] text-gray-400 font-mono">{{ $asset->serial_number }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Child Kolom 2: Category + Lokasi --}}
                                        <td class="px-6 py-3">
                                            <div class="text-xs font-medium text-gray-900 mb-0.5">{{ $asset->category ?? '-' }}</div>
                                            <div class="text-[10px] text-gray-500 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                {{ $asset->lorong ?? '-' }} - Rak {{ $asset->rak ?? '-' }}
                                            </div>
                                        </td>

                                        {{-- Child Kolom 3: Status Style --}}
                                        <td class="px-6 py-3">
                                            @if ($asset->status === 'available')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">Available</span>
                                            @elseif ($asset->status === 'deployed')
                                                <div class="flex flex-col items-start gap-1">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">Deployed</span>
                                                    <span class="text-[10px] text-gray-500 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                        {{ $asset->holder->name ?? '-' }}
                                                    </span>
                                                </div>
                                            @elseif ($asset->status === 'maintenance')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">Maintenance</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">Broken</span>
                                            @endif
                                        </td>

                                        {{-- Child Kolom 4: Actions --}}
                                        <td class="px-6 py-3 text-center">
                                            @include('assets.partials.action-buttons', ['asset' => $asset])
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        @else
                            {{-- SINGLE ITEM ROW (No Grouping Needed) --}}
                            @php $asset = $group->first(); @endphp
                            <tr class="bg-white hover:bg-gray-50 transition duration-150">
                                {{-- Kolom 1 --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 relative group">
                                            @if($asset->image && $asset->image !== 'null')
                                                <img class="h-full w-full object-cover" src="/storage/{{ $asset->image }}" alt="" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'h-full w-full flex items-center justify-center text-gray-400\'><svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $asset->name }}</div>
                                            <div class="text-xs text-gray-500 font-mono mt-0.5 bg-gray-100 px-1 rounded w-fit">{{ $asset->serial_number }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom 2 --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium">{{ $asset->category ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ $asset->lorong ?? '-' }} - Rak {{ $asset->rak ?? '-' }}
                                    </div>
                                </td>

                                {{-- Kolom 3 --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 mb-1">
                                        @if ($asset->status === 'available')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Available</span>
                                        @elseif ($asset->status === 'deployed')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">Deployed</span>
                                        @elseif ($asset->status === 'maintenance')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Maintenance</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Broken</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">Stok: <b>{{ $asset->quantity }}</b> Unit</div>
                                </td>

                                {{-- Kolom 4 --}}
                                <td class="px-6 py-4 text-center">
                                    @include('assets.partials.action-buttons', ['asset' => $asset])
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center bg-gray-50 rounded-b-xl">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-gray-500 font-medium">Belum ada data aset.</p>
                                    <p class="text-xs text-gray-400 mt-1">Silakan tambahkan aset baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-xs text-gray-500 text-right">
            Menampilkan semua data (Auto-scroll) • Total {{ $assets->count() }} Aset
        </div>
    </div>
</div>

{{-- INCLUDE COMPONENT MODAL (Sama seperti sebelumnya, tidak diubah) --}}
@include('assets.partials.modals')

{{-- SCRIPT: Real-time Search (AJAX) untuk Katalog Aset --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen input search & dropdown status
        const searchInput = document.querySelector('input[name="search"]');
        const statusSelect = document.querySelector('select[name="status"]');
        const listContainer = document.querySelector('.overflow-x-auto table'); // Target tabel untuk diganti isinya
        let debounceTimer;

        function fetchResults() {
            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(() => {
                // Konsep: Ambil value dari form search & filter
                const query = searchInput.value;
                const status = statusSelect.value;

                // Update URL Browser (agar kalau direfresh filternya tetap ada)
                const url = new URL(window.location.href);
                if(query) url.searchParams.set('search', query); else url.searchParams.delete('search');
                if(status !== 'all') url.searchParams.set('status', status); else url.searchParams.delete('status');
                
                window.history.pushState({}, '', url);

                // Fetch Data Baru via AJAX
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    // Parse HTML baru
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Ambil isi tabel baru dan gantikan isi tabel lama
                    const newTableContent = doc.querySelector('.overflow-x-auto table').innerHTML;
                    if(listContainer && newTableContent) {
                        listContainer.innerHTML = newTableContent;
                        
                        // Re-initalize AlpineJS variables if needed (though x-data is usually on parents)
                        // Karena kita pakai AlpineJS untuk expand row, pastikan strukturnya aman.
                        // Di sini kita replace innerHTML tabel, jadi x-data di tbody mungkin perlu diperhatikan.
                        // Namun karena fetch mengambil full HTML table termasuk thead/tbody, x-data akan terevaluasi ulang oleh Alpine jika me-render komponen baru.
                        // (Tips: Alpine mungkin butuh manual init kalau DOM berubah drastis, tapi untuk page refresh parsial biasanya oke)
                    }
                    
                    // Update juga angka total di footer
                    const newFooter = doc.querySelector('.text-xs.text-gray-500.text-right').innerHTML;
                    const footerElement = document.querySelector('.text-xs.text-gray-500.text-right');
                    if(footerElement && newFooter) footerElement.innerHTML = newFooter;
                })
                .catch(error => console.error('Error fetching assets:', error));

            }, 300); // Debounce 300ms
        }

        if(searchInput) {
            searchInput.addEventListener('input', fetchResults);
        }
        
        if(statusSelect) {
            statusSelect.addEventListener('change', fetchResults);
        }
    });
</script>

@endsection