<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Aset Saya</h3>
        <p class="text-3xl font-bold text-indigo-600"><?php echo e($activeAssetsCount ?? 0); ?> <span class="text-sm text-gray-500 font-normal">Unit Aktif</span></p>
        <div class="mt-4">
            <a href="<?php echo e(route('assets.my')); ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat Detail &rarr;</a>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Permintaan Saya</h3>
        <p class="text-3xl font-bold text-yellow-600"><?php echo e($pendingRequestsCount ?? 0); ?> <span class="text-sm text-gray-500 font-normal">Pending</span></p>
        <div class="mt-4">
            <a href="<?php echo e(route('borrowing.index')); ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Cek Status &rarr;</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
    <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition">
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Riwayat Permintaan Anda</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Request</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $myRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900"><?php echo e($req->asset->name); ?></div>
                                <div class="text-xs text-gray-500 truncate max-w-[200px]" title="<?php echo e($req->reason); ?>">
                                    <?php echo e($req->reason); ?>

                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo e(\Carbon\Carbon::parse($req->created_at)->translatedFormat('d M Y')); ?>

                                </div>
                                <div class="text-xs text-gray-500 font-mono mt-0.5">
                                    <?php echo e(\Carbon\Carbon::parse($req->created_at)->setTimezone('Asia/Jakarta')->format('H:i:s')); ?> WIB
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                    $colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'returned' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $labels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'returned' => 'Dikembalikan',
                                    ];
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($colors[$req->status] ?? 'bg-gray-100'); ?>">
                                    <?php echo e($labels[$req->status] ?? ucfirst($req->status)); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500 italic">
                                Belum ada riwayat permintaan.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white flex flex-col justify-center items-center text-center">
        <svg class="h-12 w-12 text-indigo-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <h3 class="text-lg font-bold">Butuh Alat Kerja?</h3>
        <p class="text-indigo-100 text-sm mt-1 mb-4">Ajukan peminjaman aset kantor dengan mudah.</p>
        <a href="/assets" class="w-full bg-white text-indigo-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-50 transition shadow-sm">
            Cari & Pinjam Aset &rarr;
        </a>
    </div>
</div><?php /**PATH C:\laragon\www\simaset_fix\resources\views/dashboard/user_view.blade.php ENDPATH**/ ?>