{{-- SECTION 2: TRANSAKSI --}}
@canany(['chat.access', 'asset.create', 'maintenance.create', 'borrow.action', 'return.verify', 'asset.view'])

<div x-data="{ 
    open: localStorage.getItem('sidebar_transaksi') === 'true', 
    showModal: false
}" 
x-init="$watch('open', val => localStorage.setItem('sidebar_transaksi', val))">
    <div class="px-3 mb-1 flex items-center justify-between group cursor-pointer hover:bg-gray-50 rounded-lg py-1.5 transition-colors" @click="open = !open">
        <div class="flex items-center gap-2">
                <svg class="w-3 h-3 text-gray-400 transition-transform duration-200 transform" :class="open ? 'rotate-90' : 'rotate-0'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-indigo-600 transition-colors">Transaksi</p>
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
                            Transaksi Aset
                        </h3>
                        <button @click="showModal = false" class="text-indigo-200 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-6">
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            Menu <strong>Transaksi</strong> adalah tempat segala aktivitas sirkulasi aset terjadi. Di sini Anda mencatat perpindahan tangan atau kondisi aset.
                        </p>
                        <div class="space-y-4">
                            <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                                <h5 class="text-xs font-bold text-indigo-700 uppercase mb-1">Peminjaman & Pengembalian</h5>
                                <p class="text-xs text-gray-600">Proses karyawan meminjam aset kantor. Admin wajib memverifikasi persetujuan (approve) dan pengembalian (verify return).</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <h5 class="text-xs font-bold text-gray-700 mb-1">Mutasi</h5>
                                    <p class="text-[10px] text-gray-500">Memindahkan aset antar lokasi (Misal: Gudang A ke Gudang B).</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <h5 class="text-xs font-bold text-gray-700 mb-1">Maintenance</h5>
                                    <p class="text-[10px] text-gray-500">Pencatatan perbaikan aset yang rusak atau servis rutin.</p>
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
            @can('chat.access')
            <a href="{{ route('chat.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('chat.index') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('chat.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                <span>Pesan & Diskusi</span>
            </a>
            @endcan

            @can('asset.create')
            <a href="{{ route('assets.create') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('assets.create') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('assets.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Input Aset Baru</span>
            </a>
            @endcan

            @can('maintenance.create')
            <a href="{{ route('maintenances.create') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('maintenances.create') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('maintenances.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Lapor Kerusakan</span>
            </a>
            @endcan

            @can('borrow.action')
            <a href="{{ route('borrowing.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('borrowing.index') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('borrowing.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                <span>Approval Peminjaman</span>
            </a>
            @endcan

            @can('return.verify')
            <a href="{{ route('returns.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('returns.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('returns.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Verifikasi Pengembalian</span>
            </a>
            @endcan

            @can('asset.edit')
            <a href="{{ route('warehouse.createMove') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('warehouse.createMove') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('warehouse.createMove') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                <span>Mutasi Aset</span>
            </a>
            <a href="{{ route('maintenances.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('maintenances.index') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('maintenances.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Perbaikan Barang</span>
            </a>
            @endcan

        </nav>
    </div>
</div>
@endcanany
