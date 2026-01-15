
<div id="mobile-overlay" class="hidden fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm md:hidden transition-opacity duration-300" onclick="toggleMobileSidebar()"></div>


<aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 transition-transform duration-300 transform -translate-x-full md:translate-x-0 flex flex-col shadow-xl w-64">
    
    
    <div class="h-24 flex flex-col items-center justify-center border-b border-gray-100 shrink-0 relative">
        <div class="flex flex-col items-center justify-center gap-2">
            
            <img class="h-10 w-auto object-contain" src="<?php echo e(asset('img/logoVitechAsia.png')); ?>" alt="Logo">
            
            <span class="font-bold text-gray-800 text-sm tracking-wide whitespace-nowrap">
                SIMASET
            </span>
        </div>

        
        <button onclick="toggleMobileSidebar()" class="absolute top-4 right-4 md:hidden text-gray-500 hover:text-red-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    
    <div class="flex-1 overflow-y-auto custom-scrollbar py-4 px-3 space-y-1">
        
        <div class="mb-2 px-3">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Menu Utama</p>
        </div>

        <nav class="space-y-1">
            
            <a href="<?php echo e(route('dashboard')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50'); ?>">
                <svg class="shrink-0 h-5 w-5 mr-3 <?php echo e(request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                <span>Dashboard</span>
            </a>

            
            <?php if(auth()->user()->role === 'admin'): ?>
                <div class="mt-6 mb-2 px-3"><div class="h-px bg-gray-200 mb-2"></div><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Administrator</p></div>

                <a href="<?php echo e(route('assets.index')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->is('assets*') && !request()->routeIs('assets.map') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50'); ?>">
                    <svg class="shrink-0 h-5 w-5 mr-3 <?php echo e(request()->is('assets*') && !request()->routeIs('assets.map') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    <span>Data Aset IT</span>
                </a>

                
                <a href="<?php echo e(route('borrowing.index')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('borrowing.index') || request()->routeIs('borrowing.show') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50'); ?>">
                    <svg class="shrink-0 h-5 w-5 mr-3 <?php echo e(request()->routeIs('borrowing.index') || request()->routeIs('borrowing.show') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    <span>Manajemen Peminjaman</span>
                </a>

                <a href="<?php echo e(route('assets.map')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('assets.map') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50'); ?>">
                    <svg class="shrink-0 h-5 w-5 mr-3 <?php echo e(request()->routeIs('assets.map') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Peta Lokasi</span>
                </a>
                <a href="<?php echo e(route('users.index')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50'); ?>">
                    <svg class="shrink-0 h-5 w-5 mr-3 <?php echo e(request()->routeIs('users.*') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span>Manajemen User</span>
                </a>
                <a href="<?php echo e(route('reports.index')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('reports.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50'); ?>">
                    <svg class="shrink-0 h-5 w-5 mr-3 <?php echo e(request()->routeIs('reports.*') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span>Laporan & Audit</span>
                </a>
            <?php endif; ?>

            
            <?php if(auth()->user()->role !== 'admin'): ?>
                <div class="mt-6 mb-2 px-3"><div class="h-px bg-gray-200 mb-2"></div><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Karyawan</p></div>
                
                
                <a href="<?php echo e(route('assets.my')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('assets.my') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50'); ?>">
                    <svg class="shrink-0 h-5 w-5 mr-3 <?php echo e(request()->routeIs('assets.my') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                    <span>Aset Saya</span>
                </a>

                
                <a href="<?php echo e(route('assets.index')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('assets.index') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50'); ?>">
                    <svg class="shrink-0 h-5 w-5 mr-3 <?php echo e(request()->routeIs('assets.index') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    <span>Pinjam Aset Baru</span>
                </a>

                
                <a href="<?php echo e(route('borrowing.history')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('borrowing.history') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-indigo-50'); ?>">
                    <svg class="shrink-0 h-5 w-5 mr-3 <?php echo e(request()->routeIs('borrowing.history') ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Riwayat Peminjaman</span>
                </a>
            <?php endif; ?>
        </nav>
    </div>

    
    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
        <form action="<?php echo e(route('logout')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="flex items-center w-full group text-gray-600 hover:text-red-600 transition-colors">
                <svg class="shrink-0 h-5 w-5 mr-3 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="sidebar-text font-medium text-sm whitespace-nowrap">Logout</span>
            </button>
        </form>
    </div>
</aside>

<script>
    let sidebarEl, mainContentEl, overlayEl;

    document.addEventListener('DOMContentLoaded', () => {
        sidebarEl = document.getElementById('sidebar-menu');
        mainContentEl = document.getElementById('main-content');
        overlayEl = document.getElementById('mobile-overlay');

        const isClosed = localStorage.getItem('sidebarClosed') === 'true';
        if (isClosed && window.innerWidth >= 768) {
            closeSidebarDesktop(); 
        }
    });

    function toggleMobileSidebar() {
        if (!sidebarEl) return;
        const isHidden = sidebarEl.classList.contains('-translate-x-full');
        
        if (isHidden) {
            sidebarEl.classList.remove('-translate-x-full');
            overlayEl.classList.remove('hidden');
        } else {
            sidebarEl.classList.add('-translate-x-full');
            overlayEl.classList.add('hidden');
        }
    }

    function toggleMinimize() {
        const isClosed = sidebarEl.classList.contains('-translate-x-full');
        if (isClosed) {
            openSidebarDesktop();
            localStorage.setItem('sidebarClosed', 'false');
        } else {
            closeSidebarDesktop();
            localStorage.setItem('sidebarClosed', 'true');
        }
    }

    function closeSidebarDesktop() {
        if (!sidebarEl || !mainContentEl) return;
        sidebarEl.classList.add('-translate-x-full');
        sidebarEl.classList.remove('md:translate-x-0');
        mainContentEl.classList.remove('md:pl-64');
        mainContentEl.classList.add('md:pl-0');
    }

    function openSidebarDesktop() {
        if (!sidebarEl || !mainContentEl) return;
        sidebarEl.classList.remove('-translate-x-full');
        sidebarEl.classList.add('md:translate-x-0');
        mainContentEl.classList.remove('md:pl-0');
        mainContentEl.classList.add('md:pl-64');
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 20px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #d1d5db; }
</style><?php /**PATH C:\laragon\www\simaset_fix\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>