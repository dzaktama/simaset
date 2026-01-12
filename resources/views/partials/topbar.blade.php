<div class="sticky top-0 z-20 flex-shrink-0 flex h-16 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-200 w-full">
    
    {{-- Tombol Hamburger (Mobile Only) --}}
    <button type="button" 
            class="px-4 border-r border-gray-200 text-gray-500 hover:bg-gray-50 focus:outline-none md:hidden transition-colors" 
            onclick="toggleSidebar()">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
        </svg>
    </button>
    
    <div class="flex-1 px-4 flex justify-between w-full">
        
        {{-- Bagian Kiri: Search Bar --}}
        <div class="flex-1 flex items-center">
            <form class="w-full max-w-lg flex md:ml-0" action="/assets" method="GET">
                <div class="relative w-full text-gray-400 focus-within:text-indigo-600 group">
                    <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-3">
                        <svg class="h-5 w-5 transition-colors group-focus-within:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input name="search" 
                           class="block w-full h-full pl-10 pr-3 py-2 border-0 bg-gray-100/50 rounded-full text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white sm:text-sm transition-all shadow-inner" 
                           placeholder="Cari aset (Nama, SN, dll)..." 
                           type="search" 
                           value="{{ request('search') }}">
                </div>
            </form>
        </div>
        
        {{-- Bagian Kanan: Notifikasi & Dropdown Profil --}}
        <div class="ml-4 flex items-center gap-4">
            
            {{-- Tombol Notifikasi --}}
            <button class="p-2 text-gray-400 hover:text-indigo-600 transition rounded-full hover:bg-indigo-50 relative group">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                {{-- Dot Indikator (Opsional) --}}
                <span class="absolute top-2 right-2 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
            </button>

            {{-- [REVISI POIN 2 & 3] PROFIL DROPDOWN --}}
            <div class="relative" id="profileDropdownContainer">
                {{-- Trigger Button --}}
                <button onclick="toggleProfileDropdown()" class="flex items-center gap-3 focus:outline-none group">
                    <div class="text-right hidden md:block">
                        <div class="text-sm font-bold text-gray-700 leading-tight group-hover:text-indigo-600 transition">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                    <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-md ring-2 ring-white group-hover:ring-indigo-200 transition">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    {{-- Chevron Icon --}}
                    <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Dropdown Menu (Hidden by default) --}}
                <div id="profileDropdownMenu" class="hidden absolute right-0 mt-3 w-48 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-50 transform origin-top-right transition-all">
                    
                    {{-- Header di Dropdown (Mobile View Helper) --}}
                    <div class="px-4 py-3 border-b border-gray-100 md:hidden">
                        <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>

                    {{-- Menu Items --}}
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition">Profil Saya</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition">Pengaturan</a>
                    
                    <div class="border-t border-gray-100 my-1"></div>

                    {{-- Logout Button --}}
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar Aplikasi
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Logic Dropdown Profil
    function toggleProfileDropdown() {
        const menu = document.getElementById('profileDropdownMenu');
        menu.classList.toggle('hidden');
    }

    // Tutup dropdown jika klik di luar
    window.addEventListener('click', function(e) {
        const container = document.getElementById('profileDropdownContainer');
        const menu = document.getElementById('profileDropdownMenu');
        
        // Jika klik terjadi DILUAR container, tutup menu
        if (!container.contains(e.target)) {
            if (!menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        }
    });
</script>