
<div class="mt-4 mb-8">
    <div class="relative bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden">
        
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg width="100%" height="100%">
                <defs>
                    <pattern id="hero-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1" fill="#fff"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#hero-pattern)"/>
            </svg>
        </div>
        
        <div class="relative z-10 px-8 py-10 md:py-12 md:px-12 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left text-white space-y-3">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Butuh Alat Kerja Baru?</h2>
                <p class="text-indigo-100 text-lg max-w-2xl">Ajukan peminjaman aset IT dengan mudah dan cepat untuk menunjang produktivitas kerja Anda.</p>
            </div>
            <div class="shrink-0">
                <a href="<?php echo e(route('assets.index')); ?>" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-bold rounded-full text-indigo-700 bg-white hover:bg-gray-50 hover:scale-105 transition-transform duration-200 shadow-lg group">
                    <svg class="w-6 h-6 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Pinjam Aset Sekarang
                </a>
            </div>
        </div>
    </div>
</div>


<div class="grid gap-6 mb-8 md:grid-cols-3">
    <a href="<?php echo e(route('assets.my')); ?>" class="group flex items-center justify-between p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-indigo-200 cursor-pointer">
        <div class="flex items-center">
            <div class="p-3 mr-4 text-indigo-500 bg-indigo-50 rounded-full group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-500">Aset Saya Saat Ini</p>
                
                <p class="text-2xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors"><?php echo e($myAssetsCount ?? 0); ?> Unit</p>
            </div>
        </div>
        <div class="flex items-center text-xs font-semibold text-gray-400 group-hover:text-indigo-500">
            Detail <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </div>
    </a>

    <a href="<?php echo e(route('borrowing.history')); ?>" class="group flex items-center justify-between p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-yellow-200 cursor-pointer">
        <div class="flex items-center">
            <div class="p-3 mr-4 text-yellow-500 bg-yellow-50 rounded-full group-hover:bg-yellow-500 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-500">Menunggu Persetujuan</p>
                <p class="text-2xl font-bold text-gray-800 group-hover:text-yellow-600 transition-colors"><?php echo e($pendingRequests ?? 0); ?> Pengajuan</p>
            </div>
        </div>
        <div class="text-xs font-bold text-yellow-600 bg-yellow-100 px-2 py-1 rounded group-hover:bg-yellow-200">
            Cek Status
        </div>
    </a>

    <a href="<?php echo e(route('borrowing.history')); ?>" class="group flex items-center justify-between p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-blue-200 cursor-pointer">
        <div class="flex items-center">
            <div class="p-3 mr-4 text-blue-500 bg-blue-50 rounded-full group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-500">Total Riwayat Pinjam</p>
                <p class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">Lihat Semua</p>
            </div>
        </div>
        <svg class="w-5 h-5 text-gray-300 group-hover:text-blue-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </a>
</div>


<div class="w-full mb-8 overflow-hidden rounded-xl shadow-sm border border-gray-100 bg-white">
    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div>
            <h3 class="font-bold text-gray-800 text-lg">Riwayat Permintaan Terkini</h3>
            <p class="text-sm text-gray-500">Pantau status pengajuan aset Anda di sini.</p>
        </div>
        <a href="<?php echo e(route('borrowing.history')); ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center group">
            Lihat Selengkapnya
            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
        </a>
    </div>
    
    <div class="w-full overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-6 py-4">Aset yang Diminta</th>
                    <th class="px-6 py-4">Tanggal Pengajuan</th>
                    <th class="px-6 py-4">Durasi / Rencana Kembali</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $recentActivities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="text-gray-700 hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center text-sm">
                            <div class="relative hidden w-10 h-10 mr-3 rounded bg-indigo-50 md:block shrink-0">
                                <?php if(isset($activity->asset->image) && $activity->asset->image): ?>
                                    <img class="object-cover w-full h-full rounded" src="<?php echo e(asset('storage/' . $activity->asset->image)); ?>" alt="" loading="lazy" />
                                <?php else: ?>
                                    <div class="flex items-center justify-center w-full h-full text-indigo-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800"><?php echo e($activity->asset->name ?? 'Aset Tidak Ditemukan'); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($activity->asset->serial_number ?? '-'); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <?php echo e(\Carbon\Carbon::parse($activity->created_at)->translatedFormat('d M Y')); ?>

                        <p class="text-xs text-gray-400 mt-0.5"><?php echo e(\Carbon\Carbon::parse($activity->created_at)->format('H:i')); ?> WIB</p>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <?php if($activity->return_date): ?>
                            <span class="text-gray-800 font-medium"><?php echo e(\Carbon\Carbon::parse($activity->return_date)->translatedFormat('d M Y')); ?></span>
                            <p class="text-xs text-gray-500">
                                (<?php echo e(\Carbon\Carbon::parse($activity->created_at)->diffInDays(\Carbon\Carbon::parse($activity->return_date))); ?> Hari)
                            </p>
                        <?php else: ?>
                            <span class="text-gray-400 italic">Tidak ditentukan</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <?php if($activity->status == 'pending'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Menunggu</span>
                        <?php elseif($activity->status == 'approved' && !$activity->returned_at): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Dipinjam</span>
                        <?php elseif($activity->status == 'rejected'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Ditolak</span>
                        <?php elseif($activity->returned_at): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">Selesai</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="<?php echo e(route('borrowing.show', $activity->id)); ?>" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm hover:underline">Detail &rarr;</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada riwayat permintaan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div><?php /**PATH C:\laragon\www\simaset_fix\resources\views/dashboard/user_view.blade.php ENDPATH**/ ?>