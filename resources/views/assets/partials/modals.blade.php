{{-- MODAL DETAIL --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    {{-- Backdrop (Black 50%, No Blur) --}}
    <div class="absolute inset-0 bg-black/50 transition-opacity duration-300" onclick="closeDetailModal()"></div>
    
    {{-- Modal Panel (Slide from Top) --}}
    <div class="relative w-full flex justify-center pt-10 px-4">
        <div class="relative w-full max-w-4xl bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all duration-500 ease-out translate-y-0 border border-gray-100">
            
            {{-- Header --}}
            <div class="bg-white px-5 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 p-1.5 rounded text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight">Detail Aset</h2>
                        <p class="text-[10px] text-gray-500">Informasi spesifikasi & status terkini.</p>
                    </div>
                </div>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded transition" title="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="flex flex-col lg:flex-row">
                {{-- Kiri: Visual (Gambar & QR) --}}
                <div class="w-full lg:w-5/12 bg-gray-50 p-5 border-b lg:border-b-0 lg:border-r border-gray-100 flex flex-col gap-4">
                    
                    {{-- Carousel --}}
                    <div class="relative w-full aspect-[4/3] bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden group">
                        <div id="carouselSlides" class="flex transition-transform duration-500 ease-out h-full w-full">
                            {{-- Slides injected via JS --}}
                        </div>
                        
                        {{-- Controls --}}
                        <button id="prevBtn" onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-1.5 rounded-full shadow-md backdrop-blur-sm transition hidden">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        <button id="nextBtn" onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-1.5 rounded-full shadow-md backdrop-blur-sm transition hidden">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>
                        <div id="carouselIndicators" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 p-1 bg-black/20 rounded-full backdrop-blur-sm"></div>
                    </div>

                    {{-- QR Code Card --}}
                    <div class="bg-white rounded-lg p-3 border border-gray-200 shadow-sm text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Digital Identity</p>
                        <div class="flex justify-center mb-1">
                             <img id="modalQR" src="" alt="QR Code" class="w-24 h-24 object-contain p-1 border border-gray-100 rounded">
                             <p id="qrErrorMsg" class="hidden text-[10px] text-red-500 self-center">QR Unavailable</p>
                        </div>
                        <p class="text-[10px] text-indigo-600 font-medium cursor-pointer hover:underline" onclick="window.print()">Cetak Label</p>
                    </div>
                </div>

                {{-- Kanan: Detail Info --}}
                <div class="w-full lg:w-7/12 p-5 space-y-5">
                    
                    {{-- Title & Badges --}}
                    <div>
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-1">
                            <h3 id="modalName" class="text-xl font-bold text-gray-900 leading-tight">-</h3>
                            <span id="modalStatus" class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-gray-100 text-gray-800 uppercase tracking-wide shadow-sm">-</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span class="flex items-center gap-1 bg-gray-50 px-1.5 py-0.5 rounded font-mono border border-gray-200" id="modalSN">-</span>
                            <span>•</span>
                            <span id="modalKategori" class="font-medium text-gray-700">-</span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Deskripsi</h4>
                        <div id="modalDescription" class="text-xs text-gray-600 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100">
                            -
                        </div>
                    </div>

                    {{-- Key Details Grid --}}
                    <div>
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Informasi Teknis</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            
                            <div class="flex flex-col border-b border-gray-100 pb-1.5">
                                <span class="text-[10px] text-gray-500 mb-0.5">Lokasi</span>
                                <span class="font-semibold text-gray-900 text-sm" id="modalLocation">-</span>
                            </div>

                            <div class="flex flex-col border-b border-gray-100 pb-1.5">
                                <span class="text-[10px] text-gray-500 mb-0.5">Kondisi</span>
                                <span class="font-semibold text-gray-900 text-sm" id="modalCondition">-</span>
                            </div>

                            <div class="flex flex-col border-b border-gray-100 pb-1.5">
                                <span class="text-[10px] text-gray-500 mb-0.5">Terdaftar</span>
                                <span class="font-semibold text-gray-900 text-sm" id="modalCreatedAt">-</span>
                            </div>

                            <!-- Admin Only Info -->
                            <div class="flex flex-col border-b border-gray-100 pb-1.5 admin-only">
                                <span class="text-[10px] text-gray-500 mb-0.5">Pembelian</span>
                                <span class="font-semibold text-gray-900 text-sm" id="modalPurchase">-</span>
                            </div>

                             <div class="flex flex-col border-b border-gray-100 pb-1.5 admin-only">
                                <span class="text-[10px] text-gray-500 mb-0.5">Nilai (Est.)</span>
                                <span class="font-semibold text-gray-900 text-sm" id="modalPrice">-</span>
                            </div>

                             <div class="flex flex-col border-b border-gray-100 pb-1.5 admin-only">
                                <span class="text-[10px] text-gray-500 mb-0.5">Manfaat</span>
                                <span class="font-semibold text-gray-900 text-sm" id="modalLife">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Status Container --}}
                    <div id="statusContainer" class="mt-2"></div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-8 py-5 border-t border-gray-200 flex justify-end gap-3 rounded-b-2xl">
                 <button id="btnBooking" type="button" onclick="openBookingForm()" class="hidden px-5 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg shadow-sm transition text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Booking Antrian
                </button>

                <button id="btnPinjam" type="button" onclick="closeDetailModal(); openLoanModal(currentAssetData)" class="hidden px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-lg hover:shadow-indigo-500/30 transition text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Ajukan Peminjaman
                </button>
                    
                <button onclick="closeDetailModal()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition text-sm shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL FORM PINJAM / BOOKING --}}
<div id="loanModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeLoanModal()"></div>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
            <form action="{{ route('borrowing.store') }}" method="POST">
                @csrf
                <input type="hidden" name="is_booking" id="isBookingInput" value="0">

                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Form Pengajuan</h3>
                        <button type="button" onclick="closeLoanModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    {{-- Kartu Ringkasan Aset --}}
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
                        {{-- Jumlah Unit --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Unit</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="quantity" id="loanQuantity" min="1" value="1" required class="block w-24 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 border">
                                <span class="text-xs text-gray-500" id="loanMaxStockText"></span>
                            </div>
                        </div>

                        {{-- Rencana Kembali --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Rencana Kembali <span class="text-gray-400 font-normal text-xs">(Opsional)</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">Tanggal</label>
                                    <input type="date" name="return_date" id="returnDateInput" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 border">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">Jam (WIB)</label>
                                    <input type="time" name="return_time" id="returnTimeInput" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 border">
                                </div>
                            </div>
                        </div>

                        {{-- Alasan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Keperluan / Alasan <span class="text-red-500">*</span></label>
                            <textarea name="reason" rows="3" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 border" placeholder="Contoh: Untuk setup event di ruang meeting utama"></textarea>
                        </div>
                    </div>
                </div>
                
                {{-- Footer Form --}}
                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-sm font-bold text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto transition">Kirim Pengajuan</button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto transition" onclick="closeLoanModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL ADD STOCK (Tambah Stok Aset Sejenis) --}}
<div id="addStockModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeAddStockModal()"></div>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
            <form action="{{ route('assets.addStock') }}" method="POST">
                @csrf
                <input type="hidden" name="asset_id" id="stockAssetId">

                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Tambah Stok Aset</h3>
                        <button type="button" onclick="closeAddStockModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-100 mb-5">
                        <p class="text-xs text-indigo-500 font-bold uppercase tracking-wider mb-1">Menambah Stok Untuk:</p>
                        <p class="font-bold text-gray-800 text-lg" id="stockAssetName">-</p>
                        <p class="text-xs text-gray-500" id="stockAssetCategory">-</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Unit Baru</label>
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="adjustStock(-1)" class="p-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold">-</button>
                                <input type="number" name="quantity" id="stockQuantity" min="1" value="1" class="block w-full text-center rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-lg p-2 border">
                                <button type="button" onclick="adjustStock(1)" class="p-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold">+</button>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-2">Nomor Seri baru akan digenerate otomatis melanjutkan urutan terakhir.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-sm font-bold text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto transition">Simpan Tambahan</button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto transition" onclick="closeAddStockModal()">Batal</button>
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
    const authRole = "{{ auth()->user()->role }}";

    // Update Function: Menerima qrCodeBase64
    function openDetailModal(asset, holder, qrCodeBase64) {
        currentAssetData = asset;
        
        // Populate Basic Info
        document.getElementById('modalName').innerText = asset.name;
        document.getElementById('modalSN').innerText = asset.serial_number;
        document.getElementById('modalDescription').innerHTML = asset.description || '<span class="text-gray-400 italic">Tidak ada deskripsi.</span>';
        document.getElementById('modalKategori').innerText = asset.category || '-';
        document.getElementById('modalCondition').innerText = asset.condition_notes || 'Kondisi Baik';
        document.getElementById('modalLocation').innerText = (asset.lorong || '-') + ' / Rak ' + (asset.rak || '-');
        
        // Mobile Fields
        const mobSN = document.getElementById('modalSN_mobile');
        if(mobSN) mobSN.innerText = asset.serial_number;
        const mobQty = document.getElementById('modalQty_mobile');
        if(mobQty) mobQty.innerText = asset.quantity + ' Unit';

        // Populate ADMIN ONLY Fields
        const purchaseEl = document.getElementById('modalPurchase');
        const priceEl = document.getElementById('modalPrice');
        const lifeEl = document.getElementById('modalLife');
        const adminElements = document.querySelectorAll('.admin-only');

        if (['admin', 'super_admin'].includes(authRole)) {
            adminElements.forEach(el => el.classList.remove('hidden'));
            
            // Format Purchase Date
            if (asset.purchase_date) {
                const pDate = new Date(asset.purchase_date);
                purchaseEl.innerText = pDate.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
            } else {
                purchaseEl.innerText = '-';
            }

            // Format Price (IDR)
            if (asset.purchase_price) {
                priceEl.innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(asset.purchase_price);
            } else {
                priceEl.innerText = '-';
            }

            // Useful Life
            lifeEl.innerText = asset.useful_life_years ? asset.useful_life_years + ' Tahun' : '-';
        } else {
            // Hide for non-admins
            adminElements.forEach(el => el.classList.add('hidden'));
        }

        // Populate QR Code
        const qrImg = document.getElementById('modalQR');
        const qrError = document.getElementById('qrErrorMsg');
        // Use the base64 string passed from blade if available, else fallback to route
        if (qrCodeBase64 && qrCodeBase64.length > 20) {
             qrImg.src = qrCodeBase64;
        } else {
             qrImg.src = `/assets/${asset.id}/scan-qr-image`;
        }
        
        qrImg.classList.remove('hidden');
        qrError.classList.add('hidden');
        qrImg.onerror = function() {
            qrImg.classList.add('hidden');
            qrError.classList.remove('hidden');
        };

        const cDate = new Date(asset.created_at);
        document.getElementById('modalCreatedAt').innerText = cDate.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});

        // Populate Status Badge & Info Container
        const st = document.getElementById('modalStatus');
        st.innerText = asset.status; // Biarkan CSS capitalize handling atau text transform
        
        // Reset Classes
        st.className = "px-3 py-1 text-sm font-bold rounded-full uppercase tracking-wide shadow-sm";
        
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
            
            // Allow booking if not admin
            if(authRole !== 'admin' && authRole !== 'super_admin') {
                if(btnBooking) btnBooking.classList.remove('hidden');
            }

            cont.innerHTML = `
                <div class="bg-blue-50 p-5 rounded-xl border border-blue-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold shadow-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Sedang Dipinjam Oleh</p>
                            <p class="text-lg font-bold text-gray-900">${holder ? holder.name : 'Unknown'}</p>
                            <p class="text-xs text-gray-500">${holder ? (holder.department || '-') : ''}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-6 text-sm mt-3 border-t border-blue-200/50 pt-3">
                        <div>
                            <span class="text-blue-500 text-[10px] font-bold uppercase block mb-1">Tanggal Pinjam</span>
                            <span class="font-semibold text-gray-800">${assignTime}</span>
                        </div>
                        <div>
                            <span class="text-blue-500 text-[10px] font-bold uppercase block mb-1">Batas Kembali</span>
                            <span class="font-semibold text-gray-800">${retTime}</span>
                        </div>
                    </div>
                </div>
            `;
        } else if(asset.status === 'available') {
            st.classList.add('bg-green-100', 'text-green-800');
            // Allow Borrow if not admin and stock > 0
            if(authRole !== 'admin' && authRole !== 'super_admin' && asset.quantity > 0) {
                if(btnPinjam) btnPinjam.classList.remove('hidden');
            }
            cont.innerHTML = `
                <div class="flex items-start gap-4 bg-green-50 p-5 rounded-xl border border-green-100">
                    <div class="h-10 w-10 rounded-full bg-green-200 flex items-center justify-center text-green-700 font-bold shadow-sm flex-shrink-0">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div>
                        <p class="text-green-800 font-bold text-base">Aset Tersedia</p>
                        <p class="text-sm text-green-700 mt-1">Aset ini siap untuk dipinjamkan atau digunakan.</p>
                        <p class="text-xs text-green-600 mt-2 font-mono bg-green-100/50 px-2 py-1 rounded inline-block">Stok Saat Ini: ${asset.quantity} Unit</p>
                    </div>
                </div>
            `;
        } else if (asset.status === 'maintenance') {
            st.classList.add('bg-yellow-100', 'text-yellow-800');
            cont.innerHTML = `
                <div class="bg-yellow-50 p-5 rounded-xl text-center border border-yellow-100">
                    <div class="inline-flex h-12 w-12 rounded-full bg-yellow-100 text-yellow-600 items-center justify-center mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <p class="text-yellow-800 font-bold text-base">Sedang Dalam Perbaikan</p>
                    <p class="text-sm text-yellow-700 mt-1">Aset sedang menjalani maintenance dan tidak dapat digunakan.</p>
                </div>
            `;
        } else {
            // Broken / Lost
            st.classList.add('bg-red-100', 'text-red-800');
            cont.innerHTML = `
                 <div class="bg-red-50 p-5 rounded-xl text-center border border-red-100">
                    <p class="text-red-800 font-bold text-base">Status: ${asset.status.toUpperCase()}</p>
                    <p class="text-sm text-red-700 mt-1">Aset ini dilaporkan rusak atau hilang.</p>
                </div>
            `;
        }

        // Carousel Images
        let imgs=[]; 
        if(asset.image && asset.image !== 'null') imgs.push(asset.image); 
        if(asset.image2 && asset.image2 !== 'null') imgs.push(asset.image2); 
        if(asset.image3 && asset.image3 !== 'null') imgs.push(asset.image3);
        
        let slides='', dots='';
        totalSlides=imgs.length; currentSlide=0;
        
        if (imgs.length > 0) {
            imgs.forEach((im,i) => {
                slides += `<div class="min-w-full h-full flex items-center justify-center bg-gray-50"><img src="${getImg(im)}" class="h-full object-contain p-4 mix-blend-multiply" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'flex flex-col items-center justify-center text-gray-400\\'><svg class=\\'w-12 h-12 mb-2\\' fill=\\'none\\' stroke=\\'currentColor\\' viewBox=\\'0 0 24 24\\'><path stroke-linecap=\\'round\\' stroke-linejoin=\\'round\\' stroke-width=\\'2\\' d=\\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\\'></path></svg><span class=\\'text-xs\\'>Gambar Tidak Ditemukan</span></div>';"></div>`;
                dots += `<button onclick="goToSlide(${i})" class="w-2.5 h-2.5 rounded-full transition-all bg-white/50 border border-black/10 hover:bg-white hover:scale-110"></button>`;
            });
        } else {
            slides = `<div class="min-w-full h-full flex flex-col items-center justify-center bg-gray-50 text-gray-400">
                <svg class="w-16 h-16 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-xs font-medium uppercase tracking-widest text-gray-300">No Image Available</span>
            </div>`;
        }

        document.getElementById('carouselSlides').innerHTML=slides;
        document.getElementById('carouselIndicators').innerHTML=dots;
        
        const navs = [document.getElementById('prevBtn'),document.getElementById('nextBtn'),document.getElementById('carouselIndicators')];
        navs.forEach(e => totalSlides>1 ? e.classList.remove('hidden') : e.classList.add('hidden'));
        updateCarousel();
        
        // ANIMATION LOGIC
        const modal = document.getElementById('detailModal');
        const panel = modal.querySelector('div.transform'); // Select the panel
        
        // 1. Show Wrapper
        modal.classList.remove('hidden');
        
        // 2. Start Animation (Slide Down)
        // Reset to off-screen first (in case it wasn't)
        panel.classList.remove('translate-y-0');
        panel.classList.add('-translate-y-full');
        
        // Trigger reflow/frame
        requestAnimationFrame(() => {
            panel.classList.remove('-translate-y-full');
            panel.classList.add('translate-y-0');
        });
    }

    function closeDetailModal(){ 
        const modal = document.getElementById('detailModal');
        const panel = modal.querySelector('div.transform');
        
        // 1. Slide Up
        panel.classList.remove('translate-y-0');
        panel.classList.add('-translate-y-full');
        
        // 2. Hide Wrapper after transition
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300); // 300ms matches duration-300 (or 500)
    }

    // Fungsi Form
    function openLoanModal(asset) {
        prepareForm(asset, "0", "Form Pengajuan Peminjaman", "Kirim Pengajuan", "bg-indigo-600", "hover:bg-indigo-700");
    }
    function openBookingForm() {
        closeDetailModal();
        prepareForm(currentAssetData, "1", "Booking Antrian Aset", "Booking Sekarang", "bg-yellow-600", "hover:bg-yellow-700");
    }
    function prepareForm(asset, isBooking, title, btnText, btnClassAdd, btnHoverAdd) {
        document.getElementById('loanAssetId').value = asset.id;
        document.getElementById('isBookingInput').value = isBooking;
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

        // VALIDASI TANGGAL & JAM
        const dateInput = document.getElementById('returnDateInput');
        const timeInput = document.getElementById('returnTimeInput');
        
        // 1. Set Min Date hari ini
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const todayStr = `${year}-${month}-${day}`;
        dateInput.min = todayStr;
        
        // Reset value
        dateInput.value = '';
        timeInput.value = '';
        timeInput.min = '';

        // 2. Listener perubahan tanggal
        dateInput.onchange = function() {
            if(this.value === todayStr) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                timeInput.min = `${hours}:${minutes}`;
            } else {
                timeInput.removeAttribute('min');
            }
        };

        // 3. Listener perubahan jam (extra protection)
        timeInput.onchange = function() {
            if(dateInput.value === todayStr) {
                const now = new Date();
                const currentHm = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                if(this.value < currentHm) {
                    alert('Jam pengembalian tidak boleh kurang dari jam saat ini jika tanggalnya hari ini.');
                    this.value = currentHm;
                }
            }
        }

        document.querySelector('#loanModal h3').innerText = title;
        const btn = document.querySelector('#loanModal button[type="submit"]');
        btn.innerText = btnText;
        btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'bg-yellow-600', 'hover:bg-yellow-700');
        btn.classList.add(btnClassAdd, btnHoverAdd);
        document.getElementById('loanModal').classList.remove('hidden');
    }
    function closeLoanModal(){ document.getElementById('loanModal').classList.add('hidden'); }

    // --- ADD STOCK MODAL FUNCTIONS ---
    function openAddStockModal(asset) {
        document.getElementById('stockAssetId').value = asset.id;
        document.getElementById('stockAssetName').innerText = asset.name;
        document.getElementById('stockAssetCategory').innerText = asset.category;
        document.getElementById('stockQuantity').value = 1;
        document.getElementById('addStockModal').classList.remove('hidden');
    }
    
    function closeAddStockModal() {
        document.getElementById('addStockModal').classList.add('hidden');
    }

    function adjustStock(delta) {
        const input = document.getElementById('stockQuantity');
        let val = parseInt(input.value) || 0;
        val += delta;
        if(val < 1) val = 1;
        input.value = val;
    }
</script>
