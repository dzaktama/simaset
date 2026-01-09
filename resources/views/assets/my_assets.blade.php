@extends('layouts.main')

@section('container')
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="text-3xl font-bold leading-tight text-gray-900">Aset Saya</h2>
            <p class="mt-2 text-sm text-gray-600">Daftar barang inventaris yang sedang Anda gunakan.</p>
        </div>
        
        <div class="flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-600 shadow-sm border border-gray-200">
            <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ now()->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Serial Number</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 rounded bg-gray-100 border border-gray-200 overflow-hidden relative group">
                                    @if($asset->image) <img class="h-full w-full object-cover" src="{{ asset('storage/' . $asset->image) }}">
                                    @else <div class="h-full w-full flex items-center justify-center text-gray-400"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div> @endif
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">{{ $asset->name }}</div>
                                    <div class="text-xs text-blue-600 font-medium">Qty: {{ $asset->quantity }} Unit</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $asset->serial_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Sedang Dipinjam
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button onclick="openDetailModal({{ json_encode($asset) }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-200 hover:bg-indigo-100 transition font-medium text-sm">
                                Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">Anda belum meminjam aset apapun.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL DETAIL ASET LENGKAP (REVISI POIN 5) --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
                <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Detail Barang Saya</h3>
                    <button onclick="closeDetailModal()" class="text-indigo-200 hover:text-white transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="w-full md:w-1/2">
                            <div class="relative w-full h-64 bg-gray-100 rounded-lg overflow-hidden border shadow-sm group">
                                <div id="carouselSlides" class="flex transition-transform duration-500 ease-out h-full w-full"></div>
                                <button id="prevBtn" onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 text-white p-1.5 rounded-full hidden"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>
                                <button id="nextBtn" onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 text-white p-1.5 rounded-full hidden"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>
                                <div id="carouselIndicators" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5"></div>
                            </div>
                        </div>

                        <div class="w-full md:w-1/2 space-y-4">
                            <div>
                                <h2 id="modalName" class="text-2xl font-bold text-gray-900 leading-tight">-</h2>
                                <p id="modalSN" class="text-sm font-mono text-gray-500 mt-1">-</p>
                            </div>
                            
                            {{-- Info Peminjaman --}}
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <p class="text-xs font-bold text-blue-800 uppercase mb-2">Informasi Peminjaman</p>
                                <div class="space-y-2">
                                    <div class="flex justify-between border-b border-blue-200 pb-1">
                                        <span class="text-sm text-blue-600">Tanggal Terima</span>
                                        <span class="text-sm font-bold text-gray-800" id="modalAssignedDate">-</span>
                                    </div>
                                    <div class="flex justify-between pt-1">
                                        <span class="text-sm text-blue-600">Batas Kembali</span>
                                        <span class="text-sm font-bold text-gray-800" id="modalReturnDate">-</span>
                                    </div>
                                </div>
                            </div>

                            {{-- [REVISI POIN 5] Tambah Deskripsi & Kondisi --}}
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Deskripsi:</p>
                                <div id="modalDescription" class="text-sm text-gray-700 bg-gray-50 p-2 rounded border border-gray-200 max-h-24 overflow-y-auto">-</div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Kondisi Barang:</p>
                                <p id="modalCondition" class="text-sm font-medium text-gray-800">-</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col md:flex-row justify-between items-center gap-3">
                    {{-- [PREP POIN 7] Tombol Kembalikan (Placeholder dulu) --}}
                    <button type="button" disabled class="w-full md:w-auto bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg text-sm font-bold border border-yellow-200 hover:bg-yellow-200 transition opacity-50 cursor-not-allowed" title="Fitur ini akan segera hadir">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                        Kembalikan Aset
                    </button>

                    <button onclick="closeDetailModal()" class="w-full md:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function getImg(path) { return path ? `/storage/${path}` : 'https://via.placeholder.com/600x400?text=No+Image'; }
        function formatDateID(dateStr) { if(!dateStr) return '-'; const d = new Date(dateStr); return d.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'})+' '+d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})+' WIB'; }

        let currentSlide=0, totalSlides=0;
        function updateCarousel() {
            document.getElementById('carouselSlides').style.transform = `translateX(-${currentSlide * 100}%)`;
            const dots = document.getElementById('carouselIndicators').children;
            for(let i=0; i<dots.length; i++) { dots[i].classList.toggle('bg-white', i===currentSlide); dots[i].classList.toggle('bg-white/50', i!==currentSlide); }
        }
        function nextSlide(){ if(totalSlides<=1)return; currentSlide=(currentSlide+1)%totalSlides; updateCarousel(); }
        function prevSlide(){ if(totalSlides<=1)return; currentSlide=(currentSlide-1+totalSlides)%totalSlides; updateCarousel(); }
        function goToSlide(i){ currentSlide=i; updateCarousel(); }

        function openDetailModal(asset) {
            document.getElementById('modalName').innerText = asset.name;
            document.getElementById('modalSN').innerText = asset.serial_number;
            document.getElementById('modalCondition').innerText = asset.condition_notes || 'Kondisi Baik';
            document.getElementById('modalDescription').innerText = asset.description || 'Tidak ada deskripsi.';
            
            document.getElementById('modalAssignedDate').innerText = formatDateID(asset.assigned_date);
            // [REVISI POIN 6] Teks lebih jelas
            document.getElementById('modalReturnDate').innerText = asset.return_date ? formatDateID(asset.return_date) : 'Jangka Panjang / Permanen';

            // Carousel Logic (Same as Catalog)
            const slidesContainer = document.getElementById('carouselSlides');
            const indicators = document.getElementById('carouselIndicators');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            let imgs=[]; if(asset.image) imgs.push(asset.image); if(asset.image2) imgs.push(asset.image2); if(asset.image3) imgs.push(asset.image3); if(imgs.length==0) imgs.push(null);
            
            let slides='', dots='';
            totalSlides=imgs.length; currentSlide=0;
            imgs.forEach((im,i) => {
                slides += `<div class="min-w-full h-full flex items-center justify-center bg-gray-200"><img src="${getImg(im)}" class="w-full h-full object-cover"></div>`;
                dots += `<button onclick="goToSlide(${i})" class="w-2 h-2 rounded-full transition-all bg-white/50"></button>`;
            });
            slidesContainer.innerHTML=slides; indicators.innerHTML=dots;
            if(totalSlides>1){ prevBtn.classList.remove('hidden'); nextBtn.classList.remove('hidden'); } else { prevBtn.classList.add('hidden'); nextBtn.classList.add('hidden'); }
            updateCarousel();

            document.getElementById('detailModal').classList.remove('hidden');
        }
        function closeDetailModal(){ document.getElementById('detailModal').classList.add('hidden'); }
    </script>
@endsection