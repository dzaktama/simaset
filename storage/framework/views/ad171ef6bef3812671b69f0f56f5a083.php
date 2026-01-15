<?php $__env->startSection('container'); ?>
<div class="container mx-auto px-4 py-8">
    
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Daftar Aset IT</h1>
            <p class="text-gray-600 mt-1">Kelola inventaris, stok, dan lokasi aset.</p>
        </div>
        
        <div class="flex gap-2">
            <?php if(auth()->user()->role == 'admin'): ?>
            <a href="<?php echo e(route('assets.create')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg flex items-center gap-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Aset
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('assets.map')); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg flex items-center gap-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0121 18.382V7.618a1 1 0 01-1.447-.894L15 7m0 13V7m0 0L9 4"></path></svg>
                Peta Lokasi
            </a>
        </div>
    </div>

    
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <form action="<?php echo e(route('assets.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="pl-10 block w-full rounded-lg border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5" placeholder="Cari nama aset, serial number, atau kategori...">
            </div>

            
            <div>
                <select name="status" class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5">
                    <option value="all">Semua Status</option>
                    <option value="available" <?php echo e(request('status') == 'available' ? 'selected' : ''); ?>>Available</option>
                    <option value="deployed" <?php echo e(request('status') == 'deployed' ? 'selected' : ''); ?>>Deployed</option>
                    <option value="maintenance" <?php echo e(request('status') == 'maintenance' ? 'selected' : ''); ?>>Maintenance</option>
                    <option value="broken" <?php echo e(request('status') == 'broken' ? 'selected' : ''); ?>>Broken</option>
                </select>
            </div>

            
            <div>
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-lg text-sm px-5 py-2.5 transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th scope="col" class="px-6 py-4">Info Aset</th>
                        <th scope="col" class="px-6 py-4">Kategori & Lokasi</th>
                        <th scope="col" class="px-6 py-4">Status & Stok</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-white hover:bg-gray-50 transition duration-150">
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 relative group">
                                        <?php if($asset->image): ?>
                                            <img class="h-full w-full object-cover" src="<?php echo e(asset('storage/' . $asset->image)); ?>" alt="">
                                        <?php else: ?>
                                            <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($asset->image2 || $asset->image3): ?> <div class="absolute bottom-0 right-0 bg-black/60 text-white text-[9px] px-1.5 py-0.5 rounded-tl">+Foto</div> <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900"><?php echo e($asset->name); ?></div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5"><?php echo e($asset->serial_number); ?></div>
                                    </div>
                                </div>
                            </td>

                            
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium"><?php echo e($asset->category ?? '-'); ?></div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <?php echo e($asset->lorong ?? '-'); ?> - Rak <?php echo e($asset->rak ?? '-'); ?>

                                </div>
                            </td>

                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <?php if($asset->quantity == 0): ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Habis</span>
                                    <?php elseif($asset->status === 'available'): ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Available</span>
                                    <?php elseif($asset->status === 'deployed'): ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">Deployed</span>
                                    <?php elseif($asset->status === 'maintenance'): ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Maintenance</span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Broken</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-gray-500">Stok: <b><?php echo e($asset->quantity); ?></b> Unit</div>
                            </td>

                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    
                                    <?php
                                        // Generate URL Scan
                                        $scanUrl = route('assets.scan', $asset->id);
                                        
                                        // Default QR kosong
                                        $qrCodeBase64 = '';

                                        // Cek apakah Facade QR Code bisa dipanggil
                                        try {
                                            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode(
                                                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                                                    ->size(200)
                                                    ->margin(1)
                                                    ->generate($scanUrl)
                                            );
                                        } catch (\Exception $e) {
                                            // Jika error (misal library belum install), biarkan kosong atau log error
                                            // $qrCodeBase64 = ''; 
                                        }
                                    ?>

                                    
                                    <button onclick="openDetailModal(<?php echo e(json_encode($asset)); ?>, <?php echo e(json_encode($asset->holder)); ?>, '<?php echo e($qrCodeBase64); ?>')" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 hover:text-indigo-800 transition border border-indigo-200" title="Detail">
                                        Detail
                                    </button>

                                    <?php if(auth()->user()->role === 'admin'): ?>
                                        
                                        <a href="<?php echo e(route('assets.edit', $asset->id)); ?>" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 border border-yellow-200 transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        
                                        <form action="<?php echo e(route('assets.destroy', $asset->id)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus aset ini?');" class="inline-block">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-200 transition" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        
                                        <?php if($asset->quantity > 0 && $asset->status == 'available'): ?>
                                            <button onclick="openLoanModal(<?php echo e(json_encode($asset)); ?>)" class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs font-bold">Pinjam</button>
                                        <?php elseif($asset->status == 'deployed'): ?>
                                            <button onclick="openDetailModal(<?php echo e(json_encode($asset)); ?>, <?php echo e(json_encode($asset->holder)); ?>, '<?php echo e($qrCodeBase64); ?>')" class="p-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition text-xs font-bold">Booking</button>
                                        <?php else: ?>
                                            <button disabled class="p-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed border border-gray-200 text-xs">Pinjam</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center bg-gray-50 rounded-b-xl">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-gray-500 font-medium">Belum ada data aset.</p>
                                    <p class="text-xs text-gray-400 mt-1">Silakan tambahkan aset baru.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <?php echo e($assets->links()); ?>

        </div>
    </div>
</div>


<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeDetailModal()"></div>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-gray-100">
            
            
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Detail Informasi Aset</h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="bg-white px-6 py-6">
                <div class="flex flex-col md:flex-row gap-8">
                    
                    
                    <div class="w-full md:w-5/12 flex flex-col gap-4">
                        
                        <div class="relative w-full h-56 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 shadow-inner group">
                            <div id="carouselSlides" class="flex transition-transform duration-500 ease-out h-full w-full"></div>
                            
                            
                            <button id="prevBtn" onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-1.5 rounded-full shadow-md hidden transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button id="nextBtn" onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-1.5 rounded-full shadow-md hidden transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                            <div id="carouselIndicators" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 p-1 bg-black/20 rounded-full backdrop-blur-sm"></div>
                        </div>

                        
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center text-center">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">QR Code Aset</p>
                            <div class="bg-white p-2 rounded-lg border border-gray-100 shadow-sm relative">
                                <img id="modalQR" src="" alt="QR Code" class="w-32 h-32 object-contain">
                                <p id="qrErrorMsg" class="hidden text-xs text-red-500 mt-2">QR tidak tersedia</p>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2">Scan untuk melihat info cepat</p>
                        </div>
                    </div>

                    
                    <div class="w-full md:w-7/12 space-y-5">
                        <div class="border-b border-gray-100 pb-4">
                            <h2 id="modalName" class="text-2xl font-bold text-gray-900 leading-tight mb-2">-</h2>
                            <div class="flex flex-wrap items-center gap-2">
                                <span id="modalSN" class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded border border-gray-200">-</span>
                                <span id="modalStatus" class="px-2 py-1 text-xs font-bold rounded-full bg-gray-200 text-gray-800 uppercase tracking-wider">-</span>
                                <span id="modalQuantity" class="px-2 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600 border border-gray-300">-</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-y-4 gap-x-4 text-sm">
                            <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Kategori</p><p id="modalKategori" class="font-medium text-gray-800">-</p></div>
                            <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Kondisi</p><p id="modalCondition" class="font-medium text-gray-800">-</p></div>
                            <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Terdaftar</p><p id="modalCreatedAt" class="font-medium text-gray-800">-</p></div>
                            <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Lokasi</p><p id="modalLocation" class="font-medium text-gray-800">-</p></div>
                        </div>

                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Deskripsi</p>
                            <div id="modalDescription" class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed max-h-32 overflow-y-auto custom-scrollbar">-</div>
                        </div>

                        
                        <div id="statusContainer" class="pt-2"></div>
                    </div>
                </div>
            </div>
            
            
            <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-2 border-t border-gray-200">
                <button id="btnBooking" type="button" onclick="openBookingForm()" class="hidden w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-yellow-500 text-sm font-bold text-white hover:bg-yellow-600 sm:w-auto transition">
                    Booking Antrian
                </button>

                <button id="btnPinjam" type="button" onclick="closeDetailModal(); openLoanModal(currentAssetData)" class="hidden w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-sm font-bold text-white hover:bg-indigo-700 sm:w-auto transition">
                    Ajukan Peminjaman
                </button>
                
                <button onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>


<div id="loanModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeLoanModal()"></div>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
            <form action="<?php echo e(route('borrowing.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="is_booking" id="isBookingInput" value="0">

                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Form Pengajuan</h3>
                        <button type="button" onclick="closeLoanModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    
                    <div class="flex items-start gap-4 p-4 bg-indigo-50 border border-indigo-100 rounded-lg mb-6">
                        <div class="h-16 w-16 flex-shrink-0 bg-white rounded-md border border-indigo-200 overflow-hidden flex items-center justify-center">
                            <img id="loanAssetImg" src="" class="h-full w-full object-cover hidden">
                            <svg id="loanAssetIcon" class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900" id="loanAssetNameDisplay">-</h4>
                            <p class="text-xs text-indigo-600 font-mono mt-0.5" id="loanAssetSNDisplay">-</p>
                            <p class="text-xs text-gray-500 mt-1" id="loanAssetConditionDisplay">Kondisi: Baik</p>
                        </div>
                    </div>

                    <input type="hidden" name="asset_id" id="loanAssetId">
                    
                    <div class="space-y-5">
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Unit</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="quantity" id="loanQuantity" min="1" value="1" required class="block w-24 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 border">
                                <span class="text-xs text-gray-500" id="loanMaxStockText"></span>
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Rencana Kembali <span class="text-gray-400 font-normal text-xs">(Opsional)</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">Tanggal</label>
                                    <input type="date" name="return_date" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 border">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">Jam (WIB)</label>
                                    <input type="time" name="return_time" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 border">
                                </div>
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Keperluan / Alasan <span class="text-red-500">*</span></label>
                            <textarea name="reason" rows="3" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 border" placeholder="Contoh: Untuk setup event di ruang meeting utama"></textarea>
                        </div>
                    </div>
                </div>
                
                
                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-sm font-bold text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto transition">Kirim Pengajuan</button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto transition" onclick="closeLoanModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getImg(path) { return path ? `/storage/${path}` : ''; }
    function formatDateID(dateStr) { if(!dateStr) return '-'; const d = new Date(dateStr); return d.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'})+' '+d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})+' WIB'; }

    let currentSlide=0, totalSlides=0;
    function updateCarousel() {
        document.getElementById('carouselSlides').style.transform = `translateX(-${currentSlide * 100}%)`;
        const dots = document.getElementById('carouselIndicators').children;
        for(let i=0; i<dots.length; i++) { 
            dots[i].classList.toggle('bg-white', i===currentSlide); 
            dots[i].classList.toggle('bg-white/50', i!==currentSlide); 
        }
    }
    function nextSlide(){ if(totalSlides>1) { currentSlide=(currentSlide+1)%totalSlides; updateCarousel(); } }
    function prevSlide(){ if(totalSlides>1) { currentSlide=(currentSlide-1+totalSlides)%totalSlides; updateCarousel(); } }
    function goToSlide(i){ currentSlide=i; updateCarousel(); }

    // Helper Global
    let currentAssetData = null;
    const authRole = "<?php echo e(auth()->user()->role); ?>";

    // Update Function: Menerima qrCodeBase64
    function openDetailModal(asset, holder, qrCodeBase64) {
        currentAssetData = asset;
        
        // Populate Basic Info
        document.getElementById('modalName').innerText = asset.name;
        document.getElementById('modalSN').innerText = asset.serial_number;
        document.getElementById('modalDescription').innerHTML = asset.description || '<span class="text-gray-400 italic">Tidak ada deskripsi.</span>';
        document.getElementById('modalKategori').innerText = asset.category || '-';
        document.getElementById('modalCondition').innerText = asset.condition_notes || 'Kondisi Baik';
        document.getElementById('modalQuantity').innerText = 'Stok: ' + asset.quantity;
        document.getElementById('modalLocation').innerText = (asset.lorong || '-') + ' / Rak ' + (asset.rak || '-');
        
        // Populate QR Code (Logic Baru)
        const qrImg = document.getElementById('modalQR');
        const qrError = document.getElementById('qrErrorMsg');
        
        if(qrCodeBase64 && qrCodeBase64.length > 20) {
            qrImg.src = qrCodeBase64;
            qrImg.classList.remove('hidden');
            qrError.classList.add('hidden');
        } else {
            qrImg.classList.add('hidden');
            qrError.classList.remove('hidden');
        }

        const cDate = new Date(asset.created_at);
        document.getElementById('modalCreatedAt').innerText = cDate.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});

        // Populate Status Badge & Info Container
        const st = document.getElementById('modalStatus');
        st.innerText = asset.status.toUpperCase();
        st.className = "px-2 py-1 text-xs font-bold rounded-full uppercase tracking-wider";
        
        const cont = document.getElementById('statusContainer');
        cont.innerHTML = '';

        // Logic Tombol Footer
        const btnPinjam = document.getElementById('btnPinjam');
        const btnBooking = document.getElementById('btnBooking');
        if(btnPinjam) btnPinjam.classList.add('hidden');
        if(btnBooking) btnBooking.classList.add('hidden');

        if(asset.status === 'deployed') {
            st.classList.add('bg-blue-100', 'text-blue-800');
            const assignTime = asset.assigned_date ? formatDateID(asset.assigned_date) : '-';
            const retTime = asset.return_date ? formatDateID(asset.return_date) : 'Jangka Panjang';
            
            // Tampilkan Tombol Booking
            if(authRole !== 'admin') {
                if(btnBooking) btnBooking.classList.remove('hidden');
            }

            cont.innerHTML = `
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-8 w-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg></div>
                        <div><p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Sedang Dipinjam Oleh</p><p class="text-sm font-bold text-gray-900">${holder?holder.name:'Unknown'}</p></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm mt-2 border-t border-blue-100 pt-2">
                        <div><span class="text-blue-500 text-[10px] font-bold uppercase">Waktu Pinjam</span><br><span class="font-medium text-gray-800 text-xs">${assignTime}</span></div>
                        <div><span class="text-blue-500 text-[10px] font-bold uppercase">Batas Kembali</span><br><span class="font-medium text-gray-800 text-xs">${retTime}</span></div>
                    </div>
                </div>
            `;
        } else if(asset.status === 'available') {
            st.classList.add('bg-green-100', 'text-green-800');
            
            // Tampilkan Tombol Pinjam
            if(authRole !== 'admin' && asset.quantity > 0) {
                if(btnPinjam) btnPinjam.classList.remove('hidden');
            }

            cont.innerHTML = `
                <div class="flex items-center gap-3 bg-green-50 p-4 rounded-lg border border-green-100">
                    <div class="h-8 w-8 rounded-full bg-green-200 flex items-center justify-center text-green-700 font-bold"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                    <div><p class="text-green-800 font-bold text-sm">Tersedia</p><p class="text-xs text-green-600">Aset ini siap untuk dipinjamkan.</p></div>
                </div>
            `;
        } else {
            st.classList.add('bg-red-100', 'text-red-800');
            cont.innerHTML = `<div class="bg-red-50 p-4 rounded-lg text-center border border-red-100"><p class="text-red-800 font-bold text-sm">Sedang Maintenance</p><p class="text-xs text-red-600">Aset tidak dapat digunakan saat ini.</p></div>`;
        }

        // Carousel Images Logic
        let imgs=[]; if(asset.image) imgs.push(asset.image); if(asset.image2) imgs.push(asset.image2); if(asset.image3) imgs.push(asset.image3);
        
        let slides='', dots='';
        totalSlides=imgs.length; currentSlide=0;
        
        if (imgs.length > 0) {
            imgs.forEach((im,i) => {
                slides += `<div class="min-w-full h-full flex items-center justify-center bg-gray-50"><img src="${getImg(im)}" class="w-full h-full object-contain p-2"></div>`;
                dots += `<button onclick="goToSlide(${i})" class="w-2 h-2 rounded-full transition-all bg-white/50 border border-black/10 hover:bg-white"></button>`;
            });
        } else {
            slides = `<div class="min-w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 text-sm italic">Tidak ada foto</div>`;
        }

        document.getElementById('carouselSlides').innerHTML=slides;
        document.getElementById('carouselIndicators').innerHTML=dots;
        
        const navs = [document.getElementById('prevBtn'),document.getElementById('nextBtn'),document.getElementById('carouselIndicators')];
        navs.forEach(e => totalSlides>1 ? e.classList.remove('hidden') : e.classList.add('hidden'));
        
        updateCarousel();

        document.getElementById('detailModal').classList.remove('hidden');
    }
    function closeDetailModal(){ document.getElementById('detailModal').classList.add('hidden'); }

    // Fungsi Buka Form Pinjam Standar
    function openLoanModal(asset) {
        prepareForm(asset, "0", "Form Pengajuan Peminjaman", "Kirim Pengajuan", "bg-indigo-600", "hover:bg-indigo-700");
    }

    // Fungsi Buka Form Booking
    function openBookingForm() {
        closeDetailModal();
        prepareForm(currentAssetData, "1", "Booking Antrian Aset", "Booking Sekarang", "bg-yellow-600", "hover:bg-yellow-700");
    }

    // Helper untuk menyiapkan Form
    function prepareForm(asset, isBooking, title, btnText, btnClassAdd, btnHoverAdd) {
        document.getElementById('loanAssetId').value = asset.id;
        document.getElementById('isBookingInput').value = isBooking;
        
        // Populate Kartu Ringkasan Aset
        document.getElementById('loanAssetNameDisplay').innerText = asset.name;
        document.getElementById('loanAssetSNDisplay').innerText = asset.serial_number;
        document.getElementById('loanAssetConditionDisplay').innerText = "Kondisi: " + (asset.condition_notes || 'Baik');
        
        const imgEl = document.getElementById('loanAssetImg');
        const iconEl = document.getElementById('loanAssetIcon');
        
        if(asset.image) {
            imgEl.src = `/storage/${asset.image}`;
            imgEl.classList.remove('hidden');
            iconEl.classList.add('hidden');
        } else {
            imgEl.classList.add('hidden');
            iconEl.classList.remove('hidden');
        }

        const qtyInput = document.getElementById('loanQuantity');
        qtyInput.max = isBooking === "1" ? 99 : asset.quantity; 
        qtyInput.value = 1;
        document.getElementById('loanMaxStockText').innerText = `(Tersedia: ${asset.quantity} unit)`;

        // Ubah Judul & Tombol
        document.querySelector('#loanModal h3').innerText = title;
        const btn = document.querySelector('#loanModal button[type="submit"]');
        btn.innerText = btnText;
        
        // Reset Class Tombol
        btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'bg-yellow-600', 'hover:bg-yellow-700');
        btn.classList.add(btnClassAdd, btnHoverAdd);

        document.getElementById('loanModal').classList.remove('hidden');
    }

    function closeLoanModal(){ document.getElementById('loanModal').classList.add('hidden'); }
</script>
<?php $__env->stopSection(); ?>       
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\simaset_fix\resources\views/assets/index.blade.php ENDPATH**/ ?>