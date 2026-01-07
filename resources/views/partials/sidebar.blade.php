<div class="md:flex md:w-64 md:flex-col fixed md:inset-y-0 z-40 bg-slate-900 transition-transform transform md:translate-x-0 h-full"
     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    
    <div class="flex items-center justify-center h-16 bg-slate-900 shadow-sm border-b border-slate-700">
        <a href="/" class="flex items-center gap-3 px-4">
            <img src="{{ asset('img/logoVitechAsia.png') }}" alt="Vitech Logo" class="h-8 w-auto object-contain">
            
            <span class="font-bold text-lg text-white tracking-wider hover:text-indigo-400 transition-colors">
                SIMASET Vitech Asia
            </span>
        </a>
    </div>

    <div class="flex flex-col flex-1 overflow-y-auto">
        <nav class="flex-1 px-2 py-4 space-y-2">
            
            <a href="/" class="{{ request()->is('/') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-colors">
                <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->is('/') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
            </a>

            <div class="pt-4 pb-1">
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Manajemen Aset
                </p>
            </div>

            <a href="/assets" class="{{ request()->is('assets') || request()->is('assets/*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-colors">
                <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->is('assets*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Semua Aset
            </a>

            <a href="/assets/create" class="{{ request()->is('assets/create') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-colors">
                <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->is('assets/create') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Input Barang Baru
            </a>

            <div class="pt-4 pb-1">
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Administrasi
                </p>
            </div>

            <a href="#" class="text-slate-300 hover:bg-slate-800 hover:text-white group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-colors opacity-75">
                <svg class="mr-3 flex-shrink-0 h-6 w-6 text-slate-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Data Karyawan
            </a>
            
            <a href="#" class="text-slate-300 hover:bg-slate-800 hover:text-white group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-colors opacity-75">
                <svg class="mr-3 flex-shrink-0 h-6 w-6 text-slate-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Laporan & Audit
            </a>

        </nav>
    </div>

    <div class="flex-shrink-0 border-t border-slate-700 p-4 bg-slate-900">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="h-9 w-9 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold shadow-lg">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-white group-hover:text-gray-200">
                    {{ auth()->user()->name ?? 'Guest' }}
                </p>
                <form action="/logout" method="POST" class="mt-1">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-slate-400 hover:text-red-400 transition-colors flex items-center gap-1 group">
                        <svg class="w-3 h-3 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>