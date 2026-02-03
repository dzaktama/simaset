{{-- SECTION 1: MASTER --}}
@canany(['dashboard.view', 'asset.view', 'asset.map'])

<div x-data="{ open: localStorage.getItem('sidebar_master') === 'true' }" x-init="$watch('open', val => localStorage.setItem('sidebar_master', val))">
    <div class="px-3 mb-1 flex items-center justify-between group cursor-pointer hover:bg-gray-50 rounded-lg py-1.5 transition-colors" @click="open = !open">
        <div class="flex items-center gap-2">
            <svg class="w-3 h-3 text-gray-400 transition-transform duration-200 transform" :class="open ? 'rotate-90' : 'rotate-0'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-indigo-600 transition-colors">Master</p>
        </div>
        {{-- Tooltip Trigger Icon --}}
        <div class="p-1 rounded-full hover:bg-indigo-100 text-gray-300 hover:text-indigo-600 transition-colors" 
                onmouseenter="startTooltip(event, 'master')" onmouseleave="stopTooltip(event)">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
    </div>
    
    <div x-ref="content" class="overflow-hidden transition-all duration-300"
         :style="open ? 'max-height: ' + $refs.content.scrollHeight + 'px; opacity: 1' : 'max-height: 0px; opacity: 0'">
        <nav class="space-y-0.5 pl-2 border-l-2 border-gray-100 ml-2">
            @can('dashboard.view')
            <a href="{{ route('dashboard') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                <span>Dashboard</span>
            </a>
            @endcan

            @can('asset.view')
            {{-- FIXED ROUTE: warehouse.dashboard -> warehouse.index --}}
            <a href="{{ route('warehouse.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('warehouse.index') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                    <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('warehouse.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                <span>Dashboard Gudang</span>
            </a>
            <a href="{{ route('assets.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('assets.index') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('assets.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                <span>Katalog Aset</span>
            </a>
            @endcan

            @can('asset.map')
            {{-- Placeholder Lokasi --}}
            <a href="{{ route('assets.map') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('assets.map') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('assets.map') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span>Lokasi Barang</span>
            </a>
            @endcan
        </nav>
    </div>
</div>
@endcanany
