{{-- Overlay Gelap untuk Mobile --}}
<div id="mobile-overlay" 
     class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm hidden transition-opacity md:hidden" 
     onclick="toggleSidebar()">
</div>

{{-- Sidebar Container --}}
{{-- FIX: Gunakan 'fixed' di semua ukuran layar, 'translate' untuk animasi --}}
<aside id="sidebar-menu" 
       class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 border-r border-gray-800 shadow-xl transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 md:fixed">
    
    {{-- Logo --}}
    <div class="flex items-center justify-center h-16 bg-gray-800 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <img class="h-8 w-auto bg-white rounded p-0.5" src="{{ asset('img/logoVitechAsia.png') }}" alt="Logo" onerror="this.style.display='none'">
            <span class="text-white font-bold text-xl tracking-wider">SIMASET</span>
        </div>
    </div>

    {{-- Menu Items --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar py-4 px-3 space-y-1">
        
        {{-- Dashboard --}}
        <a href="/dashboard" class="{{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
            Dashboard
        </a>

        @if(auth()->user()->role === 'admin')
            <div class="mt-6 mb-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Administrator</div>

            <a href="/assets" class="{{ request()->is('assets*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                Data Aset IT
            </a>

            <a href="/users" class="{{ request()->is('users*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                Manajemen User
            </a>

            <a href="{{ route('report.index') }}" class="{{ request()->routeIs('report.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Laporan & Audit
            </a>
        @endif

        @if(auth()->user()->role !== 'admin')
            <div class="mt-6 mb-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Karyawan</div>

            <a href="/my-assets" class="{{ request()->is('my-assets*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                Aset Saya
            </a>

            <a href="/assets" class="{{ request()->is('assets*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                Pinjam Aset Baru
            </a>
        @endif
    </div>

    {{-- User Profile Footer --}}
    <div class="bg-gray-800 border-t border-gray-700 p-4">
        <div class="flex items-center w-full">
            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="ml-3 truncate">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 truncate">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
        </div>
    </div>
</aside>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-menu');
        const overlay = document.getElementById('mobile-overlay');
        
        // Cek apakah sidebar sedang tersembunyi (keluar layar)
        if (sidebar.classList.contains('-translate-x-full')) {
            // Buka Sidebar
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            // Tutup Sidebar
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }
</script>