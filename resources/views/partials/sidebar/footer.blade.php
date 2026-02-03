{{-- Footer: User Profile & Clock --}}
<div class="border-t border-gray-100 bg-gray-50/50 mt-auto" x-data="{ open: false }">

    {{-- TOMBOL KELUAR OVERRIDE - SELALU MUNCUL JIKA SEDANG IMPERSONATE --}}
    @if(session('impersonator_id'))
    <div class="px-3 pt-3">
        <a href="{{ route('impersonate.leave') }}" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-bold rounded-lg bg-red-500 text-white hover:bg-red-600 transition-all shadow-lg shadow-red-500/30 animate-pulse hover:animate-none" title="Kembali ke Super Admin">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            <span>KELUAR OVERRIDE MODE</span>
        </a>
        <p class="text-[10px] text-center text-red-500 mt-1 font-medium">Anda login sebagai: {{ auth()->user()->name }}</p>
    </div>
    @endif

    <div class="relative">
        <button @click="open = !open" class="w-full text-left p-3 flex items-center gap-3 hover:bg-indigo-50 transition-colors focus:outline-none group">
            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200 shrink-0 group-hover:bg-white group-hover:border-indigo-300 transition-colors">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex flex-col min-w-0 flex-1">
                <p class="text-[12px] font-bold text-gray-800 truncate group-hover:text-indigo-700">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-gray-500 truncate">{{ auth()->user()->role->name ?? 'User' }}</p>
            </div>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>
        
        {{-- Dropdown Menu (Upwards) --}}
        <div x-show="open" @click.away="open = false" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
             class="absolute bottom-full left-2 right-2 mb-2 bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden z-50">
            
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600">
                Profile Saya
            </a>
            @if(in_array(optional(auth()->user()->role)->slug, ['admin', 'super_admin']))
                 <a href="{{ route('users.index') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600">
                    Kelola User
                </a>
            @endif
            <div class="border-t border-gray-100 my-1"></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50">
                    Keluar (Logout)
                </button>
            </form>
        </div>
    </div>
    
    <div class="px-3 pb-2 flex justify-between items-center text-[9px] text-gray-400">
         <span>v1.0.0</span>
         <span>&copy; {{ date('Y') }}</span>
    </div>
</div>
