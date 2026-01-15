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

{{-- MODAL PINJAM ASET (BORROW) --}}
<div id="borrowModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Overlay Background --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeBorrowModal()"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>
        
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <form id="borrowForm" method="POST" action="{{ route('borrowing.store') }}">
                @csrf
                
                <input type="hidden" name="asset_id" id="borrowAssetId" value="{{ old('asset_id') }}">
                <input type="hidden" name="asset_name" id="borrowAssetName" value="{{ old('asset_name') }}">

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="mt-3 w-full text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="borrowModalTitle">Form Peminjaman</h3>
                            
                            @if(session('error'))
                                <div class="mt-2 p-2 bg-red-100 border border-red-400 text-red-700 text-xs rounded">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-4" id="borrowModalDesc">Isi detail peminjaman di bawah ini.</p>
                                
                                <div class="space-y-4">
                                    {{-- Input Jumlah --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Jumlah Unit</label>
                                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required 
                                               class="mt-1 block w-20 rounded-md border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('quantity')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Input Alasan --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tujuan Peminjaman <span class="text-red-500">*</span></label>
                                        <textarea name="reason" rows="2" required placeholder="Contoh: Untuk keperluan meeting" 
                                                  class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('reason') }}</textarea>
                                        @error('reason')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Input Tanggal --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Rencana Pengembalian <span class="text-red-500">*</span></label>
                                        <input type="date" name="return_date" id="returnDateInput" value="{{ old('return_date') }}" required 
                                               class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <p class="text-[10px] text-gray-400 mt-1">Minimal hari ini atau masa depan.</p>
                                        
                                        @error('return_date')
                                            <p class="text-red-600 text-xs mt-1 font-bold">⚠️ {{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Ajukan Peminjaman
                    </button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeBorrowModal()">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT: Dipisahkan Logicnya agar lebih aman --}}
<script>
    function getImageUrl(filename) { 
        return filename ? `/storage/${filename}` : 'https://via.placeholder.com/150?text=No+Image'; 
    }

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
    
    function closeDetailModal() { 
        document.getElementById('detailModal').classList.add('hidden'); 
    }

    function openBorrowModal(asset) {
        document.getElementById('borrowAssetId').value = asset.id;
        document.getElementById('borrowAssetName').value = asset.name; 
        
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('returnDateInput').setAttribute('min', today);

        document.getElementById('borrowModalTitle').innerText = "Pinjam Aset: " + asset.name;
        document.getElementById('borrowModalDesc').innerText = `Stok tersedia: ${asset.quantity} unit.`;

        document.getElementById('borrowModal').classList.remove('hidden');
    }

    function closeBorrowModal() { 
        document.getElementById('borrowModal').classList.add('hidden'); 
    }

    // Logic Auto-Open saat Error (PHP -> JS Variable passing)
    document.addEventListener("DOMContentLoaded", function() {
        // Kita kirim data dari PHP ke JS variable dulu
        const hasErrors = {{ $errors->any() || session('error') ? 'true' : 'false' }};
        const oldAssetId = "{{ old('asset_id') }}";
        const oldAssetName = "{{ old('asset_name') }}";

        if (hasErrors && oldAssetId) {
            const modal = document.getElementById('borrowModal');
            
            if(oldAssetName) {
                document.getElementById('borrowModalTitle').innerText = "Pinjam Aset: " + oldAssetName;
            }
            
            modal.classList.remove('hidden');
            
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('returnDateInput');
            if(dateInput) dateInput.setAttribute('min', today);
        }
    });
</script>