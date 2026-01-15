{{-- Overlay untuk Mobile --}}
<div id="mobile-overlay" class="hidden fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm md:hidden transition-opacity duration-300" onclick="toggleMobileSidebar()"></div>

{{-- Sidebar Container --}}
<aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 transition-all duration-300 transform -translate-x-full md:translate-x-0 md:flex md:flex-col shadow-xl w-64">
    
    {{-- 1. Header: Logo & Toggle Button --}}
    <div class="h-20 flex items-center justify-between px-4 border-b border-gray-100 shrink-0">
        {{-- Logo Area --}}
        <div class="flex items-center justify-center overflow-hidden transition-all duration-300 logo-container">
            <img class="h-10 w-auto object-contain" src="{{ asset('img/logoVitechAsia.png') }}" alt="Logo">
            <span class="ml-2 font-bold text-gray-800 text-lg tracking-wide sidebar-text whitespace-nowrap">SIMASET</span>
        </div>

        {{-- Toggle Button (Desktop Only) --}}
        <button onclick="toggleMinimize()" class="hidden md:flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-indigo-600 transition-colors">
            <svg id="toggle-icon" class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
        </button>
        
        {{-- Close Button (Mobile Only) --}}
        <button onclick="toggleMobileSidebar()" class="md:hidden text-gray-500 hover:text-red-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    {{-- 2. Menu Items --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar py-4 px-3 space-y-1">
        
        {{-- Menu Utama --}}
        <div class="mb-2 px-3 section-header transition-opacity duration-300">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Menu Utama</p>
        </div>

        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="sidebar-text opacity-100 transition-opacity duration-300">Dashboard</span>
            </a>

            @if(auth()->user()->role === 'admin')
                <div class="mt-6 mb-2 px-3 section-header transition-opacity duration-300">
                    <div class="h-px bg-gray-200 mb-2"></div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Administrator</p>
                </div>

                <a href="{{ route('assets.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 whitespace-nowrap {{ request()->is('assets*') && !request()->routeIs('assets.map') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->is('assets*') && !request()->routeIs('assets.map') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="sidebar-text">Data Aset IT</span>
                </a>

                <a href="{{ route('assets.map') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 whitespace-nowrap {{ request()->routeIs('assets.map') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('assets.map') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="sidebar-text">Peta Lokasi</span>
                </a>

                <a href="{{ route('users.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 whitespace-nowrap {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="sidebar-text">Manajemen User</span>
                </a>

                <a href="{{ route('reports.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 whitespace-nowrap {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="sidebar-text">Laporan & Audit</span>
                </a>

                <a href="{{ route('borrowing.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 whitespace-nowrap {{ request()->routeIs('borrowing.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('borrowing.*') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="sidebar-text">Manajemen Peminjaman</span>
                </a>
            @endif

            @if(auth()->user()->role !== 'admin')
                <div class="mt-6 mb-2 px-3 section-header transition-opacity duration-300">
                    <div class="h-px bg-gray-200 mb-2"></div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Karyawan</p>
                </div>

                {{-- [PERBAIKAN] Route ini sekarang sudah benar: assets.my --}}
                <a href="{{ route('assets.my') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 whitespace-nowrap {{ request()->routeIs('assets.my') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->routeIs('assets.my') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span class="sidebar-text">Aset Saya</span>
                </a>

                <a href="{{ route('assets.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 whitespace-nowrap {{ request()->is('assets*') && !request()->routeIs('assets.my') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="shrink-0 h-5 w-5 mr-3 {{ request()->is('assets*') && !request()->routeIs('assets.my') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span class="sidebar-text">Pinjam Aset Baru</span>
                </a>
            @endif
        </nav>
    </div>

    {{-- 3. Footer: Profile / Logout (Optional Area) --}}
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
    // State Management untuk Minimize Sidebar
    const sidebar = document.getElementById('sidebar-menu');
    const overlay = document.getElementById('mobile-overlay');
    const toggleIcon = document.getElementById('toggle-icon');
    const texts = document.querySelectorAll('.sidebar-text');
    const headers = document.querySelectorAll('.section-header');

    // Cek LocalStorage saat load
    document.addEventListener('DOMContentLoaded', () => {
        const isMinimized = localStorage.getItem('sidebarMinimized') === 'true';
        if (isMinimized && window.innerWidth >= 768) {
            applyMinimize(true);
        }
    });

    // Fungsi Toggle Mobile (Layar Kecil)
    function toggleMobileSidebar() {
        const isHidden = sidebar.classList.contains('-translate-x-full');
        
        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    // Fungsi Toggle Minimize (Desktop)
    function toggleMinimize() {
        const isCurrentlyMinimized = sidebar.classList.contains('w-20');
        const newState = !isCurrentlyMinimized;
        
        applyMinimize(newState);
        localStorage.setItem('sidebarMinimized', newState);
    }

    function applyMinimize(minimize) {
        if (minimize) {
            // Kecilkan Sidebar
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');
            toggleIcon.classList.add('rotate-180'); // Putar icon panah
            
            // Sembunyikan Teks & Header
            texts.forEach(el => el.classList.add('hidden'));
            headers.forEach(el => el.classList.add('hidden'));
            
        } else {
            // Besarkan Sidebar
            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-64');
            toggleIcon.classList.remove('rotate-180');
            
            // Tampilkan Teks & Header
            texts.forEach(el => el.classList.remove('hidden'));
            headers.forEach(el => el.classList.remove('hidden'));
        }
    }
</script>

<style>
    /* Styling tambahan untuk Scrollbar tipis */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 20px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
    }
</style>