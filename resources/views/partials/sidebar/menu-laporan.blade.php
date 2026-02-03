{{-- SECTION 3: LAPORAN --}}
@canany(['report.view', 'borrow.view'])

<div x-data="{ open: localStorage.getItem('sidebar_laporan') === 'true' }" x-init="$watch('open', val => localStorage.setItem('sidebar_laporan', val))">
    <div class="px-3 mb-1 flex items-center justify-between group cursor-pointer hover:bg-gray-50 rounded-lg py-1.5 transition-colors" @click="open = !open">
        <div class="flex items-center gap-2">
                <svg class="w-3 h-3 text-gray-400 transition-transform duration-200 transform" :class="open ? 'rotate-90' : 'rotate-0'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-indigo-600 transition-colors">Laporan</p>
        </div>
            <div class="p-1 rounded-full hover:bg-indigo-100 text-gray-300 hover:text-indigo-600 transition-colors" 
                onmouseenter="startTooltip(event, 'laporan')" onmouseleave="stopTooltip(event)">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
    </div>
    
    <div x-ref="content" class="overflow-hidden transition-all duration-300"
         :style="open ? 'max-height: ' + $refs.content.scrollHeight + 'px; opacity: 1' : 'max-height: 0px; opacity: 0'">
        <nav class="space-y-0.5 pl-2 border-l-2 border-gray-100 ml-2">
            {{-- Analytics (Only Admin/Super) --}}
            @if(in_array(optional(auth()->user()->role)->slug, ['admin', 'super_admin']))
            <a href="{{ route('analytics.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('analytics.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('analytics.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                <span>Pusat Data</span>
            </a>
            @endif

            @can('report.view')
            <a href="{{ route('reports.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('reports.index') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('reports.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span>Laporan & Audit</span>
            </a>
            @endcan

            @can('asset.view')
            <a href="{{ route('warehouse.history') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('warehouse.history') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('warehouse.history') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Riwayat Pindah</span>
            </a>
            @endcan

            @can('borrow.view')
            <a href="{{ route('borrowing.history') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('borrowing.history') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('borrowing.history') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Riwayat Peminjaman</span>
            </a>
            @endcan
        </nav>
    </div>
</div>
@endcanany
