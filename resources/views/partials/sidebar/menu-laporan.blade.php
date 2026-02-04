{{-- SECTION 3: LAPORAN --}}
@canany(['report.view', 'borrow.view'])

<div x-data="{ 
    open: localStorage.getItem('sidebar_laporan') === 'true', 
    showModal: false
}" 
}"
x-init="$watch('open', val => localStorage.setItem('sidebar_laporan', val))">
    <div class="px-3 mb-1 flex items-center justify-between group cursor-pointer hover:bg-gray-50 rounded-lg py-1.5 transition-colors" @click="open = !open">
        <div class="flex items-center gap-2">
                <svg class="w-3 h-3 text-gray-400 transition-transform duration-200 transform" :class="open ? 'rotate-90' : 'rotate-0'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-indigo-600 transition-colors">Laporan</p>
        </div>
        {{-- Info Icon & Modal Trigger --}}
        <button type="button" @click.stop="showModal = true"
                class="p-1 rounded-full hover:bg-indigo-100 text-gray-300 hover:text-indigo-600 transition-colors focus:outline-none">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </button>

        {{-- MODAL WITH TELEPORT --}}
        <template x-teleport="body">
            <div x-show="showModal" class="fixed inset-0 z-[99] flex items-center justify-center px-4" style="display: none;">
                {{-- Backdrop --}}
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showModal = false"
                     class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

                {{-- Modal Content --}}
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     class="bg-white rounded-2xl shadow-xl w-full max-w-md relative overflow-hidden transform transition-all">

                    {{-- Header --}}
                    <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-white font-bold text-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Laporan & Audit
                        </h3>
                        <button @click="showModal = false" class="text-indigo-200 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-6">
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            Fitur ini untuk memantau kinerja dan riwayat penggunaan aset. Digunakan untuk keperluan <strong>Audit</strong> rutin atau pelacakan insiden.
                        </p>
                        <div class="grid grid-cols-1 gap-3">
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <div>
                                    <h5 class="text-sm font-bold text-gray-800">Laporan Lengkap</h5>
                                    <p class="text-xs text-gray-500">Ekspor data aset, peminjaman, dan maintenance ke Excel/PDF untuk laporan bulanan.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="p-2 bg-orange-100 text-orange-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div>
                                    <h5 class="text-sm font-bold text-gray-800">Riwayat Aktivitas</h5>
                                    <p class="text-xs text-gray-500">Log detail ("Track Record") siapa yang meminjam kapan dan dikembalikan jam berapa.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-3 flex justify-end">
                        <button @click="showModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </template>

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

            @can('asset.edit')
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
