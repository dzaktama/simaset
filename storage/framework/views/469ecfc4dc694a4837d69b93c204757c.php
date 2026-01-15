<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    
    <div onclick="openModal('modalTotalAssets')" class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition cursor-pointer group relative">
        <div class="p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3 group-hover:bg-indigo-600 transition">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                    <div class="ml-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Aset</dt>
                        <dd class="text-2xl font-semibold text-gray-900"><?php echo e($stats['total']); ?></dd>
                    </div>
                </div>
                <div class="text-gray-300 group-hover:text-indigo-500 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></div>
            </div>
        </div>
    </div>
    
    
    <div onclick="openModal('modalAvailableAssets')" class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition cursor-pointer group relative">
        <div class="p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3 group-hover:bg-green-600 transition">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div class="ml-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Tersedia</dt>
                        <dd class="text-2xl font-semibold text-gray-900"><?php echo e($stats['available']); ?></dd>
                    </div>
                </div>
                <div class="text-gray-300 group-hover:text-green-500 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></div>
            </div>
        </div>
    </div>

    
    <div onclick="openModal('modalDeployedAssets')" class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition cursor-pointer group relative">
        <div class="p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3 group-hover:bg-blue-600 transition">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <div class="ml-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Sedang Dipinjam</dt>
                        <dd class="text-2xl font-semibold text-gray-900"><?php echo e($stats['deployed']); ?></dd>
                    </div>
                </div>
                <div class="text-gray-300 group-hover:text-blue-500 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></div>
            </div>
        </div>
    </div>

    
    <div onclick="openModal('modalMaintenanceAssets')" class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition cursor-pointer group relative">
        <div class="p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-md p-3 group-hover:bg-red-600 transition">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <div class="ml-5">
                        <dt class="text-sm font-medium text-gray-500 truncate">Service / Rusak</dt>
                        <dd class="text-2xl font-semibold text-gray-900"><?php echo e($stats['maintenance']); ?></dd>
                    </div>
                </div>
                <div class="text-gray-300 group-hover:text-red-500 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\simaset_fix\resources\views/dashboard/admin_stats.blade.php ENDPATH**/ ?>