{{-- Header: Logo --}}
<div class="h-20 flex flex-col items-center justify-center border-b border-gray-100 shrink-0 relative">
    <div class="flex flex-col items-center justify-center gap-1.5">
        {{-- Logo --}}
        <img class="h-8 w-auto object-contain" src="{{ asset('img/logoVitechAsia.png') }}" alt="Logo">
        {{-- Teks --}}
        <span class="font-bold text-gray-800 text-xs tracking-wide whitespace-nowrap">
            SIMASET
        </span>
    </div>

    {{-- Close Button (Mobile) --}}
    <button onclick="toggleMobileSidebar()" class="absolute top-4 right-4 md:hidden text-gray-500 hover:text-red-500">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>
