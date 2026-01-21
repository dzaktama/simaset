{{-- MODAL DETAIL --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeDetailModal()"></div>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-gray-100">
            
            {{-- Modal Header --}}
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Detail Informasi Aset</h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="bg-white px-6 py-6">
                <div class="flex flex-col md:flex-row gap-8">
                    
                    {{-- AREA KIRI: Foto & QR Code --}}
                    <div class="w-full md:w-5/12 flex flex-col gap-4">
                        {{-- Carousel Foto --}}
                        <div class="relative w-full h-56 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 shadow-inner group">
                            <div id="carouselSlides" class="flex transition-transform duration-500 ease-out h-full w-full"></div>
                            
                            {{-- Navigasi Carousel --}}
                            <button id="prevBtn" onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-1.5 rounded-full shadow-md hidden transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button id="nextBtn" onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-1.5 rounded-full shadow-md hidden transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                            <div id="carouselIndicators" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 p-1 bg-black/20 rounded-full backdrop-blur-sm"></div>
                        </div>

                        {{-- QR Code Box --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center text-center">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">QR Code Aset</p>
                            <div class="bg-white p-2 rounded-lg border border-gray-100 shadow-sm relative">
                                <img id="modalQR" src="" alt="QR Code" class="w-32 h-32 object-contain">
                                <p id="qrErrorMsg" class="hidden text-xs text-red-500 mt-2">QR tidak tersedia</p>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2">Scan untuk melihat info cepat</p>
                        </div>
                    </div>

                    {{-- AREA KANAN: Informasi Detail --}}
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

                        {{-- Status Container (Info Peminjam) --}}
                        <div id="statusContainer" class="pt-2"></div>
                    </div>
                </div>
            </div>
            
            {{-- Footer Modal --}}
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
                                    <input type="date" name="return_date" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 border">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">Jam (WIB)</label>
                                    <input type="time" name="return_time" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 border">
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
        document.getElementById('modalQuantity').innerText = 'Stok: ' + asset.quantity;
        document.getElementById('modalLocation').innerText = (asset.lorong || '-') + ' / Rak ' + (asset.rak || '-');
        
        // Populate QR Code (Logic Baru - Route Image)
        const qrImg = document.getElementById('modalQR');
        const qrError = document.getElementById('qrErrorMsg');
        qrImg.src = `/assets/${asset.id}/scan-qr-image`;
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
            
            if(authRole !== 'admin' && authRole !== 'super_admin') {
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
            if(authRole !== 'admin' && authRole !== 'super_admin' && asset.quantity > 0) {
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

        // Carousel Images
        let imgs=[]; 
        if(asset.image) imgs.push(asset.image); 
        if(asset.image2) imgs.push(asset.image2); 
        if(asset.image3) imgs.push(asset.image3);
        
        let slides='', dots='';
        totalSlides=imgs.length; currentSlide=0;
        
        if (imgs.length > 0) {
            imgs.forEach((im,i) => {
                slides += `<div class="min-w-full h-full flex items-center justify-center bg-gray-50"><img src="${getImg(im)}" class="h-full object-contain p-2" onerror="this.src='https://placehold.co/400?text=No+Image'"></div>`;
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

        document.querySelector('#loanModal h3').innerText = title;
        const btn = document.querySelector('#loanModal button[type="submit"]');
        btn.innerText = btnText;
        btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'bg-yellow-600', 'hover:bg-yellow-700');
        btn.classList.add(btnClassAdd, btnHoverAdd);
        document.getElementById('loanModal').classList.remove('hidden');
    }
    function closeLoanModal(){ document.getElementById('loanModal').classList.add('hidden'); }
</script>
