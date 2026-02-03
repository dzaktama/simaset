<!-- Mobile Overlay -->
<div id="mobile-overlay" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden transition-opacity"></div>

<!-- Sidebar Container -->
<aside id="sidebar" class="relative w-full h-full bg-white flex flex-col">
    
    {{-- 1. Header --}}
    @include('partials.sidebar.header')

    {{-- Scrollable Menu Area --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4 space-y-4">
        
        {{-- 2. Menu Master --}}
        @include('partials.sidebar.menu-master')

        {{-- 3. Menu Transaksi --}}
        @include('partials.sidebar.menu-transaksi')

        {{-- 4. Menu Laporan --}}
        @include('partials.sidebar.menu-laporan')

        {{-- 5. Menu Utilitas --}}
        @include('partials.sidebar.menu-utilitas')

         {{-- 6. Menu Guide --}}
        @include('partials.sidebar.menu-guide')

    </div>

    {{-- 7. Footer --}}
    @include('partials.sidebar.footer')

</aside>

{{-- 8. Scripts --}}
@include('partials.sidebar.scripts')