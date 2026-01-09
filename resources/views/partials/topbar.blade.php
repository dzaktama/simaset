<header class="sticky top-0 z-30 flex h-16 flex-shrink-0 bg-white shadow-sm border-b border-gray-200">
    
    {{-- Tombol Hamburger (Mobile) --}}
    <button type="button" 
            class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none md:hidden hover:bg-gray-50 transition" 
            onclick="toggleSidebar()">
        <span class="sr-only">Buka Sidebar</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
        </svg>
    </button>
    
    <div class="flex flex-1 justify-between px-4 sm:px-6 lg:px-8">
        
        {{-- Search Bar --}}
        <div class="flex flex-1 items-center">
            <form class="w-full max-w-lg" action="/assets" method="GET">
                <div class="relative w-full text-gray-400 focus-within:text-indigo-600">
                    <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-3">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input name="search" 
                           class="block w-full rounded-full border-0 bg-gray-100 py-2 pl-10 pr-3 text-gray-900 placeholder-gray-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm transition-all" 
                           placeholder="Cari aset (Nama, SN, dll)..." 
                           type="search" 
                           value="{{ request('search') }}">
                </div>
            </form>
        </div>
        
        {{-- Menu Kanan --}}
        <div class="ml-4 flex items-center gap-3">
            
            {{-- Notifikasi --}}
            <button class="relative rounded-full bg-white p-1 text-gray-400 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span class="sr-only">Lihat notifikasi</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
            </button>
            
            <div class="h-6 w-px bg-gray-300 hidden sm:block"></div>

            {{-- Tombol Logout --}}
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-red-50 hover:text-red-600 rounded-md border border-gray-200 shadow-sm transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</header>