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
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden" id="assetTableContainer">
        @include('assets.partials.table')
    </div>
</div>

{{-- INCLUDE COMPONENT MODAL (Sama seperti sebelumnya, tidak diubah) --}}
@include('assets.partials.modals')

{{-- SCRIPT: Real-time Search (AJAX) untuk Katalog Aset --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const statusSelect = document.querySelector('select[name="status"]');
        const listContainer = document.getElementById('assetTableContainer');
        let debounceTimer;

        // Create Loading Indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'absolute inset-0 bg-white/50 z-10 flex items-center justify-center backdrop-blur-sm transition-opacity duration-200';
        loadingDiv.innerHTML = '<div class="flex flex-col items-center"><svg class="animate-spin h-8 w-8 text-indigo-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="text-sm font-bold text-indigo-700">Memuat data...</span></div>';
        loadingDiv.style.opacity = '0';
        loadingDiv.style.pointerEvents = 'none'; 
        
        // Helper to append loading overlay
        function attachLoading() {
            if(listContainer) {
                listContainer.style.position = 'relative';
                listContainer.style.minHeight = '200px';
                if(!listContainer.contains(loadingDiv)) {
                    listContainer.appendChild(loadingDiv);
                }
            }
        }
        
        // Initial attach
        attachLoading();

        function showLoading() {
            attachLoading();
            loadingDiv.style.opacity = '1';
            loadingDiv.style.pointerEvents = 'auto';
        }

        function hideLoading() {
            loadingDiv.style.opacity = '0';
            loadingDiv.style.pointerEvents = 'none';
        }

        function fetchResults() {
            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(() => {
                const query = searchInput.value;
                const status = statusSelect.value;
                const url = new URL(window.location.href);
                
                if(query) url.searchParams.set('search', query); else url.searchParams.delete('search');
                if(status !== 'all') url.searchParams.set('status', status); else url.searchParams.delete('status');
                
                window.history.pushState({}, '', url);

                showLoading();

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    if(listContainer) {
                        // Replace content but keep loadingDiv (it will be re-appended if lost)
                        listContainer.innerHTML = html;
                        attachLoading(); // Re-attach loading div to new content
                    }
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    hideLoading(); // Hide spinner
                });

            }, 400); // Debounce 400ms
        }

        if(searchInput) searchInput.addEventListener('input', fetchResults);
        if(statusSelect) statusSelect.addEventListener('change', fetchResults);
    });
</script>

@endsection