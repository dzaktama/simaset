<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-30 w-full">
    
    
    <div class="flex items-center gap-4">
        
        <button id="topbar-toggle" type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 p-1 rounded-md" 
            onclick="handleSidebarToggle()">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        
        <h1 class="text-lg font-semibold text-gray-800 tracking-tight truncate">
            <?php echo e($title ?? 'Dashboard'); ?>

        </h1>
    </div>

    
    <div class="flex items-center gap-4">
        
        
        <div class="relative">
            
            <button id="notif-button" type="button" onclick="toggleDropdown('notif-dropdown')" class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none">
                <span class="sr-only">View notifications</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                
                
                <?php
                    $hasNotif = false;
                    $notifCount = 0;
                    $notifications = collect();

                    if(auth()->user()->role == 'admin') {
                        // Admin: Cari yang statusnya Pending
                        $notifications = \App\Models\AssetRequest::with(['user', 'asset'])
                            ->where('status', 'pending')
                            ->latest()
                            ->take(5)
                            ->get();
                        $notifCount = \App\Models\AssetRequest::where('status', 'pending')->count();
                    } else {
                        // User: Cari yang statusnya Approved/Rejected (Terbaru)
                        $notifications = \App\Models\AssetRequest::with('asset')
                            ->where('user_id', auth()->id())
                            ->whereIn('status', ['approved', 'rejected'])
                            ->where('updated_at', '>=', now()->subDays(7)) // 7 hari terakhir
                            ->latest('updated_at')
                            ->take(5)
                            ->get();
                        $notifCount = $notifications->count();
                    }
                    
                    if($notifCount > 0) $hasNotif = true;
                ?>

                
                <?php if($hasNotif): ?>
                    <span class="absolute top-1.5 right-1.5 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white animate-pulse"></span>
                <?php endif; ?>
            </button>

            
            <div id="notif-dropdown" class="absolute right-0 z-20 mt-2 w-80 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden transition-all duration-200">
                <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <span class="text-sm font-bold text-gray-700">Notifikasi</span>
                    <?php if($notifCount > 0): ?>
                        <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded-full"><?php echo e($notifCount > 5 ? '5+' : $notifCount); ?> Baru</span>
                    <?php endif; ?>
                </div>

                <div class="max-h-64 overflow-y-auto custom-scrollbar">
                    <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('borrowing.show', $notif->id)); ?>" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition group">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    <?php if($notif->status == 'pending'): ?>
                                        <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                    <?php elseif($notif->status == 'approved'): ?>
                                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                    <?php elseif($notif->status == 'rejected'): ?>
                                        <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition">
                                        <?php if(auth()->user()->role == 'admin'): ?>
                                            <?php echo e($notif->user->name ?? 'User'); ?>

                                        <?php else: ?>
                                            Permintaan <?php echo e(ucfirst($notif->status)); ?>

                                        <?php endif; ?>
                                    </p>
                                    <p class="text-xs text-gray-500 truncate w-48">
                                        <?php echo e($notif->asset->name ?? 'Aset'); ?> (<?php echo e($notif->quantity); ?> Unit)
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        <?php echo e($notif->updated_at->diffForHumans()); ?>

                                    </p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 py-8 text-center">
                            <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                            <p class="text-xs text-gray-500 mt-2">Tidak ada notifikasi baru</p>
                        </div>
                    <?php endif; ?>
                </div>

                <a href="<?php echo e(route('borrowing.index')); ?>" class="block px-4 py-2.5 text-xs text-center text-indigo-600 font-bold hover:bg-indigo-50 rounded-b-md border-t border-gray-100 transition">
                    Lihat Semua Aktivitas
                </a>
            </div>
        </div>

        
        <div class="relative ml-3">
            <button type="button" id="user-menu-button" onclick="toggleDropdown('user-menu-dropdown')" class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" aria-expanded="false" aria-haspopup="true">
                <span class="sr-only">Open user menu</span>
                
                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200">
                    <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

                </div>
                <span class="ml-3 hidden text-sm font-medium text-gray-700 lg:block"><?php echo e(auth()->user()->name); ?></span>
                <svg class="ml-1 hidden h-5 w-5 text-gray-400 lg:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>

            
            <div id="user-menu-dropdown" class="absolute right-0 z-20 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden transition-all duration-200">
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-xs text-gray-500">Login sebagai</p>
                    <p class="text-sm font-bold text-gray-900 truncate"><?php echo e(auth()->user()->role === 'admin' ? 'Administrator' : 'Karyawan'); ?></p>
                </div>
                
                <?php if(auth()->user()->role === 'admin'): ?>
                    <a href="<?php echo e(route('users.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Kelola User</a>
                <?php endif; ?>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile Saya</a>
                
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    // Logic Sidebar (Tetap)
    function handleSidebarToggle() {
        if (window.innerWidth < 768) {
            if (typeof toggleMobileSidebar === 'function') toggleMobileSidebar();
        } else {
            if (typeof toggleMinimize === 'function') toggleMinimize();
        }
    }

    // Logic Baru: Toggle Dropdown Notif & Profile
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        const allDropdowns = ['notif-dropdown', 'user-menu-dropdown'];
        
        // Tutup dropdown lain saat satu dibuka
        allDropdowns.forEach(d => {
            if (d !== id) {
                document.getElementById(d).classList.add('hidden');
            }
        });

        // Toggle dropdown yang diklik
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }

    // Tutup dropdown saat klik di luar area
    document.addEventListener('click', function(event) {
        // Cek apakah yang diklik adalah tombol atau bagian dari dropdown
        const isButton = event.target.closest('button'); // Tombol trigger
        const isDropdown = event.target.closest('.absolute'); // Area dropdown itu sendiri
        
        // Jika klik di luar tombol DAN di luar dropdown, tutup semuanya
        if (!isButton && !isDropdown) {
            document.getElementById('notif-dropdown').classList.add('hidden');
            document.getElementById('user-menu-dropdown').classList.add('hidden');
        }
    });
</script><?php /**PATH C:\laragon\www\simaset_fix\resources\views/partials/topbar.blade.php ENDPATH**/ ?>