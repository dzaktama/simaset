@extends('layouts.main')

@section('container')
<div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div>
        <h2 class="text-3xl font-bold leading-tight text-gray-900">Katalog Aset IT</h2>
        <p class="mt-2 text-sm text-gray-600">Kelola daftar inventaris, status peminjaman, dan kondisi aset.</p>
    </div>
    
    @if(auth()->user()->role == 'admin')
    <div class="flex gap-2">
        {{-- Filter & Cetak --}}
        <form action="{{ route('report.assets') }}" method="GET" target="_blank" class="flex gap-1">
            <select name="status" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="all">Semua Status</option>
                <option value="available">Available</option>
                <option value="deployed">Deployed</option>
                <option value="maintenance">Maintenance</option>
            </select>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Cetak
            </button>
        </form>
        <a href="/assets/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
            + Tambah Aset
        </a>
    </div>
    @endif
</div>

{{-- Tabel Aset --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aset Info</th>
                    {{-- KOLOM BARU: STOK --}}
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kondisi</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status & Pemegang</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assets as $asset)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 rounded bg-gray-100 border border-gray-200 overflow-hidden relative">
                                @if($asset->image)
                                    <img class="h-full w-full object-cover" src="{{ asset('storage/' . $asset->image) }}" alt="">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-gray-400">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                @endif
                                {{-- Indikator Foto --}}
                                @if($asset->image2 || $asset->image3)
                                    <div class="absolute bottom-0 right-0 bg-black/60 text-white text-[9px] px-1.5 py-0.5 rounded-tl">
                                        +Foto
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-gray-900">{{ $asset->name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $asset->serial_number }}</div>
                            </div>
                        </div>
                    </td>
                    
                    {{-- DATA STOK (BARU) --}}
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $asset->quantity > 0 ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800' }}">
                            {{ $asset->quantity }} Unit
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-700">{{ $asset->condition_notes ?? 'Baik' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col items-start gap-1">
                            @if($asset->status == 'available')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                <span class="text-xs text-gray-500">di Gudang</span>
                            @elseif($asset->status == 'deployed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Deployed</span>
                                <span class="text-xs font-bold text-indigo-600">{{ $asset->holder->name ?? 'User Hapus' }}</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($asset->status) }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="openDetailModal({{ json_encode($asset) }}, {{ json_encode($asset->holder) }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded border border-indigo-200 hover:bg-indigo-100 transition mr-2">
                            Detail
                        </button>
                        
                        @if(auth()->user()->role == 'admin')
                            <a href="/assets/{{ $asset->id }}/edit" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-3 py-1 rounded border border-yellow-200 hover:bg-yellow-100 transition">
                                Edit
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Belum ada data aset.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $assets->links() }}
    </div>
</div>

{{-- MODAL DETAIL (CAROUSEL) - TIDAK PERLU DIUBAH LAGI --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>

        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
            <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Detail Informasi Aset</h3>
                <button onclick="closeDetailModal()" class="text-indigo-200 hover:text-white transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <div class="flex flex-col md:flex-row gap-6 mb-6">
                    <div class="w-full md:w-5/12">
                        <div class="relative w-full h-64 bg-gray-100 rounded-lg overflow-hidden border shadow-sm group">
                            <div id="carouselSlides" class="flex transition-transform duration-500 ease-out h-full w-full"></div>
                            <button id="prevBtn" onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/60 text-white p-1.5 rounded-full backdrop-blur-sm transition hidden"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>
                            <button id="nextBtn" onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/60 text-white p-1.5 rounded-full backdrop-blur-sm transition hidden"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>
                            <div id="carouselIndicators" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5"></div>
                        </div>
                        <p class="text-center text-xs text-gray-400 mt-2 italic">*Geser untuk melihat foto lain</p>
                    </div>

                    <div class="w-full md:w-7/12 space-y-3">
                        <div class="border-b pb-3">
                            <h2 id="modalName" class="text-2xl font-bold text-gray-900 leading-tight">-</h2>
                            <div class="flex items-center gap-2 mt-1">
                                <span id="modalSN" class="text-sm font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded">-</span>
                                <span id="modalStatus" class="px-2 py-0.5 text-xs font-bold rounded-full bg-gray-200 text-gray-800">-</span>
                                {{-- STOK DI MODAL --}}
                                <span id="modalQuantity" class="px-2 py-0.5 text-xs font-bold rounded-full bg-gray-100 text-gray-600 border border-gray-300">Stok: -</span>
                            </div>
                        </div>
                        
                        <div><p class="text-xs text-gray-500 uppercase font-bold">Deskripsi:</p><p id="modalDescription" class="text-sm text-gray-700">-</p></div>
                        <div><p class="text-xs text-gray-500 uppercase font-bold">Kondisi Fisik:</p><div class="bg-gray-50 p-2 rounded border border-gray-200 mt-1"><p id="modalCondition" class="text-sm font-medium text-gray-800">-</p></div></div>
                    </div>
                </div>

                <div id="loanInfo" class="border-t pt-4">
                    <h4 class="text-sm font-bold text-gray-900 uppercase mb-3">Status Peminjaman</h4>
                    <div id="infoDeployed" class="hidden bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold text-lg"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg></div>
                                <div><p class="text-xs text-blue-600 uppercase font-bold">Peminjam:</p><p id="modalHolderName" class="text-base font-bold text-gray-900">-</p><p id="modalHolderDept" class="text-xs text-gray-500">-</p></div>
                            </div>
                            <div class="text-right space-y-1">
                                <div><p class="text-xs text-blue-600">Dipinjam:</p><p id="modalAssignedDate" class="font-mono text-sm font-bold text-gray-800">-</p></div>
                                <div><p class="text-xs text-blue-600">Batas Kembali:</p><p id="modalReturnDate" class="font-mono text-sm font-bold text-gray-800">-</p></div>
                            </div>
                        </div>
                    </div>
                    <div id="infoAvailable" class="hidden bg-green-50 p-4 rounded-lg border border-green-200 flex items-center justify-center gap-2"><svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg><div><p class="text-green-800 font-bold text-sm">Tersedia di Gudang</p><p class="text-xs text-green-600">Aset ini siap untuk dipinjamkan.</p></div></div>
                    <div id="infoMaintenance" class="hidden bg-red-50 p-4 rounded-lg border border-red-200 flex items-center justify-center gap-2"><svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg><div><p class="text-red-800 font-bold text-sm">Sedang Perbaikan / Rusak</p><p class="text-xs text-red-600">Aset tidak dapat digunakan saat ini.</p></div></div>
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
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    let currentSlide = 0, totalSlides = 0;
    function updateCarousel() {
        document.getElementById('carouselSlides').style.transform = `translateX(-${currentSlide * 100}%)`;
        const dots = document.getElementById('carouselIndicators').children;
        for (let i = 0; i < dots.length; i++) {
            dots[i].classList.toggle('bg-white', i === currentSlide);
            dots[i].classList.toggle('scale-125', i === currentSlide);
            dots[i].classList.toggle('bg-white/50', i !== currentSlide);
        }
    }
    function nextSlide() { if(totalSlides > 1) { currentSlide = (currentSlide + 1) % totalSlides; updateCarousel(); } }
    function prevSlide() { if(totalSlides > 1) { currentSlide = (currentSlide - 1 + totalSlides) % totalSlides; updateCarousel(); } }
    function goToSlide(i) { currentSlide = i; updateCarousel(); }

    function openDetailModal(asset, holder) {
        // INFO DASAR
        document.getElementById('modalName').innerText = asset.name;
        document.getElementById('modalSN').innerText = asset.serial_number;
        document.getElementById('modalDescription').innerText = asset.description || '-';
        document.getElementById('modalCondition').innerText = asset.condition_notes || 'Kondisi Baik';
        document.getElementById('modalQuantity').innerText = 'Stok: ' + (asset.quantity || 1) + ' Unit'; // TAMPILKAN STOK DI MODAL

        // STATUS
        const statusEl = document.getElementById('modalStatus');
        statusEl.innerText = asset.status.toUpperCase();
        statusEl.className = "px-2 py-0.5 text-xs font-bold rounded-full"; 
        ['infoDeployed', 'infoAvailable', 'infoMaintenance'].forEach(id => document.getElementById(id).classList.add('hidden'));

        if (asset.status === 'deployed') {
            statusEl.classList.add('bg-blue-100', 'text-blue-800');
            document.getElementById('infoDeployed').classList.remove('hidden');
            document.getElementById('modalHolderName').innerText = holder ? holder.name : 'Unknown';
            document.getElementById('modalHolderDept').innerText = holder ? (holder.department || 'Staff') : '-';
            document.getElementById('modalAssignedDate').innerText = formatDateID(asset.assigned_date);
            document.getElementById('modalReturnDate').innerText = asset.return_date ? formatDateID(asset.return_date) : 'Selamanya';
        } else if (asset.status === 'available') {
            statusEl.classList.add('bg-green-100', 'text-green-800');
            document.getElementById('infoAvailable').classList.remove('hidden');
        } else {
            statusEl.classList.add('bg-red-100', 'text-red-800');
            document.getElementById('infoMaintenance').classList.remove('hidden');
        }

        // CAROUSEL
        let slidesHtml = '', dotsHtml = '', images = [];
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

        document.getElementById('carouselSlides').innerHTML = slidesHtml;
        document.getElementById('carouselIndicators').innerHTML = dotsHtml;
        
        const navs = [document.getElementById('prevBtn'), document.getElementById('nextBtn'), document.getElementById('carouselIndicators')];
        navs.forEach(el => totalSlides > 1 ? el.classList.remove('hidden') : el.classList.add('hidden'));
        
        updateCarousel();
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() { document.getElementById('detailModal').classList.add('hidden'); }
</script>
@endsection