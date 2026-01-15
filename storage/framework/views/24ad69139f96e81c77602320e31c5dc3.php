<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'SIMASET Vitech Asia'); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #1f2937; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</head>
<body class="h-full antialiased">

    <div class="min-h-screen relative flex">
        
        <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div id="main-content" class="flex-1 flex flex-col min-w-0 md:pl-64 transition-all duration-300">
            
            <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <main class="flex-1 py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    
                    
                    <?php if(session()->has('success')): ?>
                        <div class="mb-6 rounded-lg bg-green-50 p-4 border-l-4 border-green-500 shadow-sm flex items-start animate-fade-in-down">
                            <svg class="h-5 w-5 text-green-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div><h3 class="text-sm font-bold text-green-800">Berhasil</h3><p class="text-sm text-green-700 mt-1"><?php echo e(session('success')); ?></p></div>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(session()->has('error')): ?>
                        <div class="mb-6 rounded-lg bg-red-50 p-4 border-l-4 border-red-500 shadow-sm flex items-start animate-fade-in-down">
                            <svg class="h-5 w-5 text-red-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div>
                                <h3 class="text-sm font-bold text-red-800">Gagal</h3>
                                <p class="text-sm text-red-700 mt-1"><?php echo e(session('error')); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($errors->any()): ?>
                        <div class="mb-6 rounded-lg bg-red-50 p-4 border-l-4 border-red-500 shadow-sm animate-fade-in-down">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-red-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <div>
                                    <h3 class="text-sm font-bold text-red-800">Terdapat Kesalahan Input</h3>
                                    <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if(session()->has('loginError')): ?>
                        <div class="mb-6 rounded-lg bg-red-50 p-4 border-l-4 border-red-500 shadow-sm flex items-start animate-fade-in-down">
                            <svg class="h-5 w-5 text-red-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div><h3 class="text-sm font-bold text-red-800">Error</h3><p class="text-sm text-red-700 mt-1"><?php echo e(session('loginError')); ?></p></div>
                        </div>
                    <?php endif; ?>

                    <?php echo $__env->yieldContent('container'); ?>
                    
                </div>
            </main>
        </div>
    </div>

    <form id="idle-logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display:none;"><?php echo csrf_field(); ?></form>

    <script>
        (function () {
            const IDLE_TIMEOUT = 15 * 60 * 1000;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            let idleTimer = null;

            function resetTimer() {
                if (idleTimer) clearTimeout(idleTimer);
                idleTimer = setTimeout(doLogout, IDLE_TIMEOUT);
            }

            async function doLogout() {
                const form = document.getElementById('idle-logout-form');
                if (form) form.submit();
                else window.location.href = '/login';
            }

            ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'].forEach(evt => window.addEventListener(evt, resetTimer, true));
            resetTimer();
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html><?php /**PATH C:\laragon\www\simaset_fix\resources\views/layouts/main.blade.php ENDPATH**/ ?>