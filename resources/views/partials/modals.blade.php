{{-- MODAL DETAIL --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900" id="detailModalTitle">Detail Aset</h3>
                <div class="mt-4">
                    <div class="grid grid-cols-3 gap-2 mb-4" id="detailImages"></div>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div class="sm:col-span-1"><dt class="text-sm font-medium text-gray-500">Serial Number</dt><dd class="mt-1 text-sm text-gray-900 font-mono" id="detailSN">-</dd></div>
                        <div class="sm:col-span-1"><dt class="text-sm font-medium text-gray-500">Status</dt><dd class="mt-1 text-sm text-gray-900" id="detailStatus">-</dd></div>
                        <div class="sm:col-span-2"><dt class="text-sm font-medium text-gray-500">Deskripsi</dt><dd class="mt-1 text-sm text-gray-900" id="detailDesc">-</dd></div>
                        <div class="sm:col-span-2 bg-gray-50 p-3 rounded"><dt class="text-xs font-bold text-gray-500">Kondisi Fisik</dt><dd class="mt-1 text-sm text-gray-800" id="detailCondition">-</dd></div>
                    </dl>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BORROW / BOOKING --}}
<div id="borrowModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeBorrowModal()"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <form id="borrowForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10" id="modalIconBg">
                            <svg class="h-6 w-6 text-indigo-600" id="modalIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </div>
                        <div class="mt-3 w-full text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="borrowModalTitle">Form Peminjaman</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4" id="borrowModalDesc">Isi detail di bawah ini.</p>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tujuan <span class="text-red-500">*</span></label>
                                        <input type="text" name="reason" required class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Rencana Kembali</label>
                                        <input type="date" name="return_date" class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" id="borrowSubmitBtn">Ajukan</button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeBorrowModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getImageUrl(filename) { return filename ? `/storage/${filename}` : 'https://via.placeholder.com/150?text=No+Image'; }

    function openDetailModal(asset) {
        document.getElementById('detailModalTitle').innerText = asset.name;
        document.getElementById('detailSN').innerText = asset.serial_number;
        document.getElementById('detailStatus').innerText = asset.status.toUpperCase();
        document.getElementById('detailDesc').innerText = asset.description || '-';
        document.getElementById('detailCondition').innerText = asset.condition_notes || '-';
        
        const imgContainer = document.getElementById('detailImages');
        imgContainer.innerHTML = '';
        [asset.image, asset.image2, asset.image3].forEach(img => {
            if(img) {
                const el = document.createElement('img');
                el.src = getImageUrl(img);
                el.className = "h-24 w-full object-cover rounded border cursor-pointer hover:opacity-75";
                el.onclick = () => window.open(el.src, '_blank');
                imgContainer.appendChild(el);
            }
        });
        document.getElementById('detailModal').classList.remove('hidden');
    }
    function closeDetailModal() { document.getElementById('detailModal').classList.add('hidden'); }

    // MODE: 'pinjam' atau 'booking'
    function openBorrowModal(asset, mode) {
        const form = document.getElementById('borrowForm');
        form.action = `/assets/${asset.id}/request`;
        
        const title = document.getElementById('borrowModalTitle');
        const desc = document.getElementById('borrowModalDesc');
        const btn = document.getElementById('borrowSubmitBtn');
        const iconBg = document.getElementById('modalIconBg');

        if(mode === 'booking') {
            title.innerText = "Booking Antrean Aset";
            desc.innerHTML = `Barang <b>${asset.name}</b> sedang dipinjam. Ajukan booking agar Anda masuk antrean prioritas saat barang kembali.`;
            btn.innerText = "Booking Sekarang";
            btn.className = "inline-flex w-full justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-yellow-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm";
            iconBg.className = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10";
        } else {
            title.innerText = "Pinjam Aset";
            desc.innerHTML = `Ajukan peminjaman untuk <b>${asset.name}</b>.`;
            btn.innerText = "Ajukan Pinjaman";
            btn.className = "inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm";
            iconBg.className = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10";
        }

        document.getElementById('borrowModal').classList.remove('hidden');
    }
    function closeBorrowModal() { document.getElementById('borrowModal').classList.add('hidden'); }
</script>