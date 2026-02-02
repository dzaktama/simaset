{{-- Overlay untuk Mobile --}}
<div id="mobile-overlay" class="hidden fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm md:hidden transition-opacity duration-300" onclick="toggleMobileSidebar()"></div>

{{-- Sidebar Container --}}
<aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 transition-transform duration-300 transform -translate-x-full md:translate-x-0 flex flex-col shadow-xl w-64">
    
    {{-- 1. Header: Logo --}}
    <div class="h-24 flex flex-col items-center justify-center border-b border-gray-100 shrink-0 relative">
        <div class="flex flex-col items-center justify-center gap-2">
            {{-- Logo --}}
            <img class="h-10 w-auto object-contain" src="{{ asset('img/logoVitechAsia.png') }}" alt="Logo">
            {{-- Teks --}}
            <span class="font-bold text-gray-800 text-sm tracking-wide whitespace-nowrap">
                SIMASET
            </span>
        </div>

        {{-- Close Button (Mobile) --}}
        <button onclick="toggleMobileSidebar()" class="absolute top-4 right-4 md:hidden text-gray-500 hover:text-red-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    {{-- 2. Menu Items --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar py-4 px-3 space-y-1">
        
        {{-- SECTION: MENU UTAMA --}}
        <div class="px-3 mb-2 mt-2">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Menu Utama</p>
        </div>
        <nav class="space-y-1">
            @can('dashboard.view')
            <a href="{{ route('dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                <span>Dashboard</span>
            </a>
            @endcan

            @can('chat.access')
            <a href="{{ route('chat.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('chat.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('chat.*') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>Pesan & Diskusi</span>
            </a>
            @endcan

            {{-- Analytics (Only Admin/Super) --}}
            @if(in_array(optional(auth()->user()->role)->slug, ['admin', 'super_admin']))
            <a href="{{ route('analytics.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('analytics.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('analytics.*') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                <span>Pusat Data</span>
            </a>
            @endif
        </nav>

        {{-- SECTION: MANAJEMEN ASET --}}
        @canany(['asset.view', 'maintenance.view'])
        <div class="mt-6 mb-2 px-3"><div class="h-px bg-gray-200 mb-2"></div><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Aset & Inventaris</p></div>
        <nav class="space-y-1">
            @can('asset.view')
            <a href="{{ route('assets.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->is('assets*') && !request()->routeIs('assets.create') && !request()->routeIs('assets.map') && !request()->routeIs('assets.my') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->is('assets*') && !request()->routeIs('assets.create') && !request()->routeIs('assets.map') && !request()->routeIs('assets.my') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                <span>Katalog Aset</span>
            </a>
            {{-- Submenu: Input Aset (Hidden if no permission) --}}
            @can('asset.create')
                <a href="{{ route('assets.create') }}" class="group flex items-center pl-11 pr-3 py-2 text-xs font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('assets.create') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:text-indigo-600 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4 mr-2 transition-colors duration-200 {{ request()->routeIs('assets.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Input Aset Baru
                </a>
            @endcan

            <a href="{{ route('assets.map') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('assets.map') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('assets.map') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Lokasi Barang</span>
            </a>
            @endcan

            @can('maintenance.view')
            <a href="{{ route('maintenances.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('maintenances.index') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('maintenances.index') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Perbaikan Barang</span>
            </a>
            {{-- Submenu: Input Maintenance --}}
            @can('maintenance.create')
                <a href="{{ route('maintenances.create') }}" class="group flex items-center pl-11 pr-3 py-2 text-xs font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('maintenances.create') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:text-indigo-600 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4 mr-2 transition-colors duration-200 {{ request()->routeIs('maintenances.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Lapor Kerusakan
                </a>
            @endcan
            @endcan
        </nav>
        @endcanany

        {{-- SECTION: SIRKULASI --}}
        @canany(['borrow.view', 'return.verify'])
        <div class="mt-6 mb-2 px-3"><div class="h-px bg-gray-200 mb-2"></div><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Sirkulasi</p></div>
        <nav class="space-y-1">
            {{-- Admin/Approver View --}}
            @can('borrow.action')
            <a href="{{ route('borrowing.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('borrowing.index') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('borrowing.index') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                <span>Approval Peminjaman</span>
            </a>
            @endcan

            {{-- Teknisi/Admin Return Check --}}
            @can('return.verify')
            <a href="{{ route('returns.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('returns.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('returns.*') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Verifikasi Pengembalian</span>
            </a>
            @endcan

            {{-- Personal History (Karyawan) --}}
            @can('borrow.view')
            <a href="{{ route('borrowing.history') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('borrowing.history') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('borrowing.history') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Riwayat Peminjaman</span>
            </a>
            @endcan
        </nav>
        @endcanany

        {{-- SECTION: ADMINISTRASI --}}
        @canany(['report.view', 'user.view'])
        <div class="mt-6 mb-2 px-3"><div class="h-px bg-gray-200 mb-2"></div><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Administrasi</p></div>
        <nav class="space-y-1">
            @can('report.view')
            <a href="{{ route('reports.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span>Laporan & Audit</span>
            </a>
            @endcan

            @can('user.view')
            <a href="{{ route('users.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('users.index') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('users.index') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <span>Manajemen User</span>
            </a>
            {{-- Submenu: Tambah User --}}
            @can('user.create')
                <a href="{{ route('users.create') }}" class="group flex items-center pl-11 pr-3 py-2 text-xs font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('users.create') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:text-indigo-600 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4 mr-2 transition-colors duration-200 {{ request()->routeIs('users.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Tambah User
                </a>
            @endcan
            @endcan
        </nav>
        @endcanany

        {{-- SECTION: MANAJEMEN GUDANG (BARU) --}}
        @can('asset.view')
        <div class="mt-6 mb-2 px-3"><div class="h-px bg-gray-200 mb-2"></div><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Manajemen Gudang</p></div>
        <nav class="space-y-1">
            <a href="{{ route('warehouse.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('warehouse.index') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('warehouse.index') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                <span>Dashboard Gudang</span>
            </a>
            
            <a href="{{ route('warehouse.createMove') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('warehouse.createMove') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('warehouse.createMove') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                <span>Mutasi Aset</span>
            </a>

            <a href="{{ route('warehouse.history') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('warehouse.history') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('warehouse.history') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Riwayat Pindah</span>
            </a>

            <a href="{{ route('assets.map') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('assets.map') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('assets.map') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span>Peta Lokasi</span>
            </a>
        </nav>
        @endcan
        <div class="mt-6 mb-2 px-3"><div class="h-px bg-gray-200 mb-2"></div><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Area Pribadi</p></div>
        <nav class="space-y-1">
            <a href="{{ route('assets.my') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('assets.my') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('assets.my') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                <span>Aset Saya</span>
            </a>
        </nav>
    </div>

    {{-- Footer --}}
    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center w-full group text-gray-600 hover:text-red-600 transition-colors">
                <svg class="shrink-0 h-5 w-5 mr-3 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="sidebar-text font-medium text-sm whitespace-nowrap">Logout</span>
            </button>
        </form>
    </div>
</aside>

<script>
    let sidebarEl, mainContentEl, overlayEl;

    document.addEventListener('DOMContentLoaded', () => {
        sidebarEl = document.getElementById('sidebar-menu');
        mainContentEl = document.getElementById('main-content');
        overlayEl = document.getElementById('mobile-overlay');

        const isClosed = localStorage.getItem('sidebarClosed') === 'true';
        if (isClosed && window.innerWidth >= 768) {
            closeSidebarDesktop(); 
        }
    });

    function toggleMobileSidebar() {
        if (!sidebarEl) return;
        const isHidden = sidebarEl.classList.contains('-translate-x-full');
        
        if (isHidden) {
            sidebarEl.classList.remove('-translate-x-full');
            overlayEl.classList.remove('hidden');
        } else {
            sidebarEl.classList.add('-translate-x-full');
            overlayEl.classList.add('hidden');
        }
    }

    function toggleMinimize() {
        const isClosed = sidebarEl.classList.contains('-translate-x-full');
        if (isClosed) {
            openSidebarDesktop();
            localStorage.setItem('sidebarClosed', 'false');
        } else {
            closeSidebarDesktop();
            localStorage.setItem('sidebarClosed', 'true');
        }
    }

    function closeSidebarDesktop() {
        if (!sidebarEl || !mainContentEl) return;
        sidebarEl.classList.add('-translate-x-full');
        sidebarEl.classList.remove('md:translate-x-0');
        mainContentEl.classList.remove('md:pl-64');
        mainContentEl.classList.add('md:pl-0');
    }

    function openSidebarDesktop() {
        if (!sidebarEl || !mainContentEl) return;
        sidebarEl.classList.remove('-translate-x-full');
        sidebarEl.classList.add('md:translate-x-0');
        mainContentEl.classList.remove('md:pl-0');
        mainContentEl.classList.add('md:pl-64');
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 20px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #d1d5db; }
</style>