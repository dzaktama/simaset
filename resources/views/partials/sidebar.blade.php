<div id="mobile-overlay" class="hidden fixed inset-0 z-40 bg-gray-900 bg-opacity-50 md:hidden transition-opacity" onclick="toggleSidebar()"></div>

{{-- PERBAIKAN PENTING: Gunakan 'md:fixed' bukan 'md:static' agar tidak memakan tempat double --}}
<div id="sidebar-menu" class="hidden fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 border-r border-gray-800 transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:fixed md:flex md:flex-col shadow-2xl">
    
    <div class="flex flex-col flex-shrink-0">
        {{-- 1. Area Foto (Background Putih Full) --}}
        <div class="h-20 w-full bg-white flex items-center justify-center p-2">
            <img class="h-14 w-auto object-contain" src="{{ asset('img/logoVitechAsia.png') }}" alt="Logo">
        </div>

        {{-- 2. Area Tulisan (Background Gelap Sidebar) --}}
        <div class="py-4 bg-gray-900 text-center border-b border-gray-800">
            <span class="text-white font-extrabold text-xl tracking-[0.2em] uppercase">SIMASET</span>
        </div>
    </div>

    <div class="flex-1 flex flex-col overflow-y-auto custom-scrollbar bg-gray-900 pt-4 px-3">
        <div class="mb-3 px-2">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Menu Utama</p>
        </div>

        <nav class="space-y-1.5">
            <a href="/dashboard" class="{{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z" />
                </svg>
                Dashboard
            </a>

            @if(auth()->user()->role === 'admin')
                <div class="mt-8 mb-2 px-2 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Administrator</div>

                <a href="/assets" class="{{ request()->is('assets*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                    <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->is('assets*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Data Aset IT
                </a>
                {{-- Cari bagian menu Data Aset / Manajemen Aset --}}
                <a href="{{ route('assets.map') }}" class="flex items-center px-6 py-2 mt-4 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 {{ request()->routeIs('assets.map') ? 'bg-gray-100 text-gray-700 border-r-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="mx-3">Peta Lokasi</span>
                </a>
                <a href="/users" class="{{ request()->is('users*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                    <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->is('users*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Manajemen User
                </a>

                <a href="{{ route('report.index') }}" class="{{ request()->routeIs('report.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                    <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('report.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Laporan & Audit
                </a>

                <a href="{{ route('borrowing.index') }}" class="{{ request()->routeIs('borrowing.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                    <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('borrowing.*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Manajemen Peminjaman
                </a>
                
            @endif
            @if(auth()->user()->role !== 'admin')
                <div class="mt-8 mb-2 px-2 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Karyawan</div>

                <a href="/my-assets" class="{{ request()->is('my-assets*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                    <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->is('my-assets*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    Aset Saya
                </a>

                <a href="/assets" class="{{ request()->is('assets*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                    <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->is('assets*') ? 'text-white' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Pinjam Aset Baru
                </a>
            @endif
        </nav>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('mobile-overlay');
        
        // Logika Toggle Mobile
        if (sidebar.classList.contains('hidden')) {
            sidebar.classList.remove('hidden');
            setTimeout(() => {
                sidebar.classList.remove('-translate-x-full');
            }, 10);
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            setTimeout(() => {
                sidebar.classList.add('hidden');
            }, 300);
        }
    }
</script>