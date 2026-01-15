<div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div>
        <h2 class="text-3xl font-bold leading-tight text-gray-900">
            <?php echo e($title); ?>

        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Selamat datang, <span class="font-semibold text-indigo-600"><?php echo e(auth()->user()->name); ?></span>.
        </p>
    </div>
    
    
    <div class="flex flex-col md:flex-row gap-3">
        
        <div class="flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-600 shadow-sm border border-gray-200">
            <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span><?php echo e(now()->isoFormat('dddd, D MMMM Y')); ?></span>
        </div>

        
        <div class="flex items-center gap-2 rounded-lg bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 shadow-sm border border-indigo-100 min-w-[140px] justify-center">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span id="live-clock" class="font-mono text-lg tracking-wide"><?php echo e(now()->format('H:i:s')); ?></span>
            <span class="text-xs font-bold ml-1">WIB</span>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\simaset_fix\resources\views/dashboard/header.blade.php ENDPATH**/ ?>