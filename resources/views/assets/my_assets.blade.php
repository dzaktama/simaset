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
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal Terima</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 rounded bg-gray-100 border border-gray-200 overflow-hidden relative group">
                                    @if($asset->image)
                                        <img class="h-full w-full object-cover" src="{{ asset('storage/' . $asset->image) }}" alt="">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                    {{-- Indikator Foto Lebih dari 1 --}}
                                    @if($asset->image2 || $asset->image3)
                                        <div class="absolute bottom-0 right-0 bg-black/60 text-white text-[9px] px-1.5 py-0.5 rounded-tl">
                                            +Foto
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">{{ $asset->name }}</div>
                                    <div class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full inline-block mt-1">
                                        Sedang Dipinjam
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            {{ $asset->serial_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $asset->assigned_date ? $asset->assigned_date->translatedFormat('d M Y') : '-' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $asset->assigned_date ? $asset->assigned_date->format('H:i') . ' WIB' : '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button onclick="openDetailModal({{ json_encode($asset) }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-200 hover:bg-indigo-100 transition font-medium text-sm">
                                Lihat Detail Barang
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                            <p class="font-medium">Anda belum meminjam aset apapun.</p>
                            <a href="/assets" class="text-indigo-600 hover:underline mt-2 inline-block text-sm">Cari barang di katalog &rarr;</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= MODAL DETAIL ASET (CAROUSEL) ================= --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>

            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
                {{-- Header --}}
                <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Detail Barang Saya</h3>
                    <button onclick="closeDetailModal()" class="text-indigo-200 hover:text-white transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        
                        {{-- 1. CAROUSEL AREA (KIRI) --}}
                        <div class="w-full md:w-1/2">
                            <div class="relative w-full h-64 bg-gray-100 rounded-lg overflow-hidden border shadow-sm group">
                                <div id="carouselSlides" class="flex transition-transform duration-500 ease-out h-full w-full">
                                    {{-- Images injected by JS --}}
                                </div>
                                {{-- Nav Buttons --}}
                                <button id="prevBtn" onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/60 text-white p-1.5 rounded-full backdrop-blur-sm transition hidden"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>
                                <button id="nextBtn" onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/60 text-white p-1.5 rounded-full backdrop-blur-sm transition hidden"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>
                                {{-- Dots --}}
                                <div id="carouselIndicators" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5"></div>
                            </div>
                        </div>

                        {{-- 2. INFO AREA (KANAN) --}}
                        <div class="w-full md:w-1/2 space-y-4">
                            <div>
                                <h2 id="modalName" class="text-2xl font-bold text-gray-900 leading-tight">-</h2>
                                <p id="modalSN" class="text-sm font-mono text-gray-500 mt-1">-</p>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <p class="text-xs font-bold text-blue-800 uppercase mb-2">Informasi Peminjaman</p>
                                <div class="grid grid-cols-1 gap-2">
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

                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Kondisi Barang:</p>
                                <p id="modalCondition" class="text-sm text-gray-700 bg-gray-50 p-2 rounded border mt-1">-</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end">
                    <button onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:w-auto sm:text-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function getImg(path) { return path ? `/storage/${path}` : 'https://via.placeholder.com/600x400?text=No+Image'; }
        
        function formatDateID(dateStr) {
            if(!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + 
                   ' ' + 
                   d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
        }

        // --- CAROUSEL LOGIC ---
        let currentSlide = 0;
        let totalSlides = 0;

        function updateCarousel() {
            const slidesContainer = document.getElementById('carouselSlides');
            const indicators = document.getElementById('carouselIndicators').children;
            slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
            for (let i = 0; i < indicators.length; i++) {
                if (i === currentSlide) {
                    indicators[i].classList.remove('bg-white/50'); indicators[i].classList.add('bg-white', 'scale-125');
                } else {
                    indicators[i].classList.add('bg-white/50'); indicators[i].classList.remove('bg-white', 'scale-125');
                }
            }
        }
        function nextSlide() { if (totalSlides <= 1) return; currentSlide = (currentSlide + 1) % totalSlides; updateCarousel(); }
        function prevSlide() { if (totalSlides <= 1) return; currentSlide = (currentSlide - 1 + totalSlides) % totalSlides; updateCarousel(); }
        function goToSlide(index) { currentSlide = index; updateCarousel(); }

        function openDetailModal(asset) {
            // Setup Info
            document.getElementById('modalName').innerText = asset.name;
            document.getElementById('modalSN').innerText = asset.serial_number;
            document.getElementById('modalCondition').innerText = asset.condition_notes || 'Kondisi Baik';
            document.getElementById('modalAssignedDate').innerText = formatDateID(asset.assigned_date);
            document.getElementById('modalReturnDate').innerText = asset.return_date ? formatDateID(asset.return_date) : 'Tidak ditentukan / Permanen';

            // Setup Carousel Images
            const slidesContainer = document.getElementById('carouselSlides');
            const indicatorsContainer = document.getElementById('carouselIndicators');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            let slidesHtml = '';
            let dotsHtml = '';
            let images = [];

            if (asset.image) images.push(asset.image);
            if (asset.image2) images.push(asset.image2);
            if (asset.image3) images.push(asset.image3);
            if (images.length === 0) images.push(null);

            totalSlides = images.length;
            currentSlide = 0;

            images.forEach((img, index) => {
                slidesHtml += `<div class="min-w-full h-full flex items-center justify-center bg-gray-200"><img src="${getImg(img)}" class="w-full h-full object-cover"></div>`;
                dotsHtml += `<button onclick="goToSlide(${index})" class="w-2 h-2 rounded-full transition-all duration-300 bg-white/50"></button>`;
            });

            slidesContainer.innerHTML = slidesHtml;
            indicatorsContainer.innerHTML = dotsHtml;

            if (totalSlides > 1) {
                prevBtn.classList.remove('hidden'); nextBtn.classList.remove('hidden'); indicatorsContainer.classList.remove('hidden');
            } else {
                prevBtn.classList.add('hidden'); nextBtn.classList.add('hidden'); indicatorsContainer.classList.add('hidden');
            }
            updateCarousel();

            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() { document.getElementById('detailModal').classList.add('hidden'); }
    </script>
@endsection