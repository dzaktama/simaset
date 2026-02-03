<div class="mt-4 pt-4 border-t border-gray-100">
    <div class="px-3 mb-1 flex items-center justify-between group cursor-pointer hover:bg-gray-50 rounded-lg py-1.5 transition-colors">
        <div class="flex items-center gap-2">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Bantuan</p>
        </div>
        <div class="p-1 rounded-full hover:bg-indigo-100 text-gray-300 hover:text-indigo-600 transition-colors" 
            onmouseenter="startTooltip(event, 'bantuan')" onmouseleave="stopTooltip(event)">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
    </div>
    
    <nav class="space-y-0.5 ml-1">
        <a href="{{ route('guides.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('guides.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
            <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('guides.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span>Panduan Sistem</span>
        </a>
    </nav>
</div>
