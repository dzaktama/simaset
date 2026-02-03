{{-- SECTION 4: UTILITAS --}}
@canany(['user.view', 'user.create'])

<div x-data="{ open: localStorage.getItem('sidebar_utilitas') === 'true' }" x-init="$watch('open', val => localStorage.setItem('sidebar_utilitas', val))">
    <div class="px-3 mb-1 flex items-center justify-between group cursor-pointer hover:bg-gray-50 rounded-lg py-1.5 transition-colors" @click="open = !open">
        <div class="flex items-center gap-2">
                <svg class="w-3 h-3 text-gray-400 transition-transform duration-200 transform" :class="open ? 'rotate-90' : 'rotate-0'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-indigo-600 transition-colors">Utilitas</p>
        </div>
            <div class="p-1 rounded-full hover:bg-indigo-100 text-gray-300 hover:text-indigo-600 transition-colors" 
                onmouseenter="startTooltip(event, 'utilitas')" onmouseleave="stopTooltip(event)">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
    </div>
    
    <div x-ref="content" class="overflow-hidden transition-all duration-300"
         :style="open ? 'max-height: ' + $refs.content.scrollHeight + 'px; opacity: 1' : 'max-height: 0px; opacity: 0'">
        <nav class="space-y-0.5 pl-2 border-l-2 border-gray-100 ml-2">
            <a href="{{ route('assets.my') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('assets.my') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('assets.my') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                <span>Aset Saya</span>
            </a>

            @can('user.view')
            <a href="{{ route('users.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('users.index') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('users.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <span>Manajemen User</span>
            </a>
            @can('user.create')
                <a href="{{ route('users.create') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 {{ request()->routeIs('users.create') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 -ml-[2px]' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                    <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('users.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Tambah User</span>
                </a>
            @endcan
            @endcan

            {{-- Manajemen Grup (Roles) - Placeholder --}}
            <a href="#" onclick="alert('Fitur Manajemen Grup Pengguna dalam pengembangan.')" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-indigo-600">
                <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                <span>Grup Pengguna</span>
            </a>

            {{-- Pengaturan - Placeholder --}}
            <a href="#" onclick="alert('Fitur Pengaturan dalam pengembangan.')" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-r-lg transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-indigo-600">
                <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span>Pengaturan</span>
            </a>

            @if(session('impersonator_id'))
            <div class="mt-2 pt-2 border-t border-gray-100">
                <a href="{{ route('impersonate.leave') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-bold rounded-r-lg transition-all duration-200 bg-red-50 text-red-600 border-l-4 border-red-500 hover:bg-red-100 -ml-[2px]" title="Kembali ke Super Admin">
                    <svg class="shrink-0 h-4 w-4 mr-2.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span>KELUAR OVERRIDE</span>
                </a>
            </div>
            @endif
        </nav>
    </div>
</div>
@endcanany
