@extends('layouts.main')

@section('container')
{{-- 1. HEADER & JUDUL --}}
<div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div>
        <h2 class="text-3xl font-bold leading-tight text-gray-900">Katalog Aset IT</h2>
        <p class="mt-2 text-sm text-gray-600">
            @if(auth()->user()->role == 'admin')
                Kelola inventaris, pantau stok, dan riwayat aset.
            @else
                Cari dan ajukan peminjaman aset di sini.
            @endif
        </p>
    </div>
    
    @if(auth()->user()->role == 'admin')
    <a href="/assets/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Tambah Aset
    </a>
    @endif
</div>

{{-- 2. FILTER BAR --}}
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
    <form action="/assets" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        {{-- Search --}}
        <div class="w-full md:w-1/3">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pencarian</label>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, SN, atau deskripsi..." class="block w-full rounded-lg border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
        </div>

        {{-- Filter Status --}}
        <div class="w-full md:w-1/4">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
            <select name="status" class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="all">Semua Status</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available (Tersedia)</option>
                <option value="deployed" {{ request('status') == 'deployed' ? 'selected' : '' }}>Deployed (Dipinjam)</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="broken" {{ request('status') == 'broken' ? 'selected' : '' }}>Broken (Rusak)</option>
            </select>
        </div>

        {{-- Sort --}}
        <div class="w-full md:w-1/4">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Urutkan</label>
            <select name="sort" class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                <option value="stock_low" {{ request('sort') == 'stock_low' ? 'selected' : '' }}>Stok Sedikit</option>
                <option value="stock_high" {{ request('sort') == 'stock_high' ? 'selected' : '' }}>Stok Banyak</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
            </select>
        </div>

        {{-- Tombol --}}
        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-900 transition flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                Filter
            </button>
            
            @if(auth()->user()->role == 'admin')
            <a href="{{ route('report.assets', ['status' => request('status') ?? 'all']) }}" target="_blank" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition flex items-center gap-2" title="Cetak Sesuai Filter Status">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Cetak
            </a>
            @endif
        </div>
    </form>
</div>

{{-- 3. TABEL ASET --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aset Info</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kondisi</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assets as $asset)
                <tr class="hover:bg-gray-50 transition">
                    {{-- Info --}}
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
                                @if($asset->image2 || $asset->image3) <div class="absolute bottom-0 right-0 bg-black/60 text-white text-[9px] px-1.5 py-0.5 rounded-tl">+Foto</div> @endif
                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-gray-900">{{ $asset->name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $asset->serial_number }}</div>
                            </div>
                        </div>
                    </td>
                    {{-- Stok --}}
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $asset->quantity > 0 ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800' }}">
                            {{ $asset->quantity }} Unit
                        </span>
                    </td>
                    {{-- Kondisi --}}
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-700">{{ $asset->condition_notes ?? 'Baik' }}</span>
                    </td>
                    {{-- Status --}}
                    <td class="px-6 py-4">
                        @if($asset->quantity == 0) <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Habis</span>
                        @elseif($asset->status == 'available') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                        @elseif($asset->status == 'deployed') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Deployed</span>
                        @else <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($asset->status) }}</span>
                        @endif
                    </td>
                    {{-- Aksi --}}
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openDetailModal({{ json_encode($asset) }}, {{ json_encode($asset->holder) }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded border border-indigo-200 hover:bg-indigo-100 transition">Detail</button>
                            
                            @if(auth()->user()->role == 'admin')
                                <a href="/assets/{{ $asset->id }}/edit" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-3 py-1 rounded border border-yellow-200 hover:bg-yellow-100 transition">Edit</a>
                                <form action="/assets/{{ $asset->id }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin mau HAPUS aset ini selamanya?');">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded border border-red-200 hover:bg-red-100 transition">Hapus</button>
                                </form>
                            @else
                                {{-- Tombol Pinjam User (Hanya disable jika tidak available, tapi tetap muncul) --}}
                                @if($asset->quantity > 0 && $asset->status == 'available')
                                    <button onclick="openLoanModal({{ json_encode($asset) }})" class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1 rounded border border-transparent shadow-sm transition">Pinjam</button>
                                @else
                                    <button disabled class="text-gray-400 bg-gray-100 px-3 py-1 rounded border border-gray-200 cursor-not-allowed" title="Aset sedang tidak tersedia">Pinjam</button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Data aset tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $assets->links() }}</div>
</div>

{{-- 4. MODAL DETAIL INFORMASI ASET (REVISI LENGKAP & FIX BOOKING) --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
            <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Detail Informasi Aset</h3>
                <button onclick="closeDetailModal()" class="text-indigo-200 hover:text-white transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <div class="flex flex-col md:flex-row gap-6 mb-6">
                    {{-- Carousel Gambar --}}
                    <div class="w-full md:w-5/12">
                        <div class="relative w-full h-64 bg-gray-100 rounded-lg overflow-hidden border shadow-sm group">
                            <div id="carouselSlides" class="flex transition-transform duration-500 ease-out h-full w-full"></div>
                            <button id="prevBtn" onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/60 text-white p-1.5 rounded-full backdrop-blur-sm transition hidden"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>
                            <button id="nextBtn" onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/60 text-white p-1.5 rounded-full backdrop-blur-sm transition hidden"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>
                            <div id="carouselIndicators" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5"></div>
                        </div>
                        <p class="text-center text-xs text-gray-400 mt-2 italic">*Geser untuk melihat foto lain</p>
                    </div>
                    
                    {{-- Info Text Detail --}}
                    <div class="w-full md:w-7/12 space-y-3">
                        <div class="border-b pb-3">
                            <h2 id="modalName" class="text-2xl font-bold text-gray-900 leading-tight">-</h2>
                            <div class="flex items-center gap-2 mt-1">
                                <span id="modalSN" class="text-sm font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded">-</span>
                                <span id="modalStatus" class="px-2 py-0.5 text-xs font-bold rounded-full bg-gray-200 text-gray-800">-</span>
                                <span id="modalQuantity" class="px-2 py-0.5 text-xs font-bold rounded-full bg-gray-100 text-gray-600 border border-gray-300">Stok: -</span>
                            </div>
                        </div>
                        
                        {{-- Fitur Pendukung: Spesifikasi Lebih Lengkap --}}
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><p class="text-xs text-gray-500 uppercase font-bold">Kondisi</p><p id="modalCondition" class="font-medium text-gray-800">-</p></div>
                            <div><p class="text-xs text-gray-500 uppercase font-bold">Tanggal Input</p><p id="modalCreatedAt" class="font-medium text-gray-800">-</p></div>
                        </div>
                        
                        <div><p class="text-xs text-gray-500 uppercase font-bold">Deskripsi & Spesifikasi</p><p id="modalDescription" class="text-sm text-gray-700 bg-gray-50 p-2 rounded border border-gray-100 mt-1">-</p></div>
                    </div>
                </div>

                {{-- Status Peminjaman / Action Bar --}}
                <div id="loanInfo" class="border-t pt-4">
                    <h4 class="text-sm font-bold text-gray-900 uppercase mb-3">Status & Booking</h4>
                    
                    {{-- [REVISI]: Container Status Unified --}}
                    <div id="statusContainer" class="p-4 rounded-lg border">
                        {{-- Konten dinamis via JS --}}
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end">
                <button onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:w-auto sm:text-sm">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL FORM PINJAM (Sama seperti sebelumnya) --}}
<div id="loanModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeLoanModal()"></div>
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <form action="/requests" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Ajukan Peminjaman Aset</h3>
                            <div class="mt-4 space-y-4">
                                <input type="hidden" name="asset_id" id="loanAssetId">
                                <div><label class="block text-sm font-medium text-gray-700">Nama Barang</label><input type="text" id="loanAssetName" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm p-2 border text-gray-500"></div>
                                <div><label class="block text-sm font-medium text-gray-700">Jumlah Unit <span class="text-red-500">*</span></label><div class="flex items-center gap-2"><input type="number" name="quantity" id="loanQuantity" min="1" value="1" required class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"><span class="text-xs text-gray-500" id="loanMaxStockText"></span></div></div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Rencana Pengembalian <span class="text-xs text-gray-400">(Opsional)</span></label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div><label class="text-[10px] text-gray-500 uppercase font-bold">Tanggal</label><input type="date" name="return_date" class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"></div>
                                        <div><label class="text-[10px] text-gray-500 uppercase font-bold">Jam (WIB)</label><input type="time" name="return_time" class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">*Biarkan kosong jika peminjaman jangka panjang.</p>
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700">Keperluan / Alasan <span class="text-red-500">*</span></label><textarea name="reason" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Contoh: Untuk keperluan meeting proyek X"></textarea></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Kirim Pengajuan</button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeLoanModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getImg(path) { return path ? `/storage/${path}` : 'https://via.placeholder.com/600x400?text=No+Image'; }
    function formatDateID(dateStr) {
        if(!dateStr) return '-';
        const d = new Date(dateStr);
        // [REVISI]: Menambah Jam & Detik agar lengkap
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
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
        // 1. Populate Info Dasar
        document.getElementById('modalName').innerText = asset.name;
        document.getElementById('modalSN').innerText = asset.serial_number;
        document.getElementById('modalDescription').innerText = asset.description || '-';
        document.getElementById('modalCondition').innerText = asset.condition_notes || 'Kondisi Baik';
        document.getElementById('modalQuantity').innerText = 'Total Stok: ' + (asset.quantity || 1) + ' Unit';
        
        // Populate Created At (Fitur Pendukung)
        const createdDate = new Date(asset.created_at);
        document.getElementById('modalCreatedAt').innerText = createdDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        const statusEl = document.getElementById('modalStatus');
        statusEl.innerText = asset.status.toUpperCase();
        statusEl.className = "px-2 py-0.5 text-xs font-bold rounded-full"; 
        
        const container = document.getElementById('statusContainer');
        container.className = "p-4 rounded-lg border"; // Reset class
        container.innerHTML = ''; // Reset content

        // 2. Logic Status & Booking (REVISI BESAR DI SINI)
        if (asset.status === 'deployed') {
            statusEl.classList.add('bg-blue-100', 'text-blue-800');
            container.classList.add('bg-blue-50', 'border-blue-200');
            
            // [REVISI] Detail Peminjaman Lengkap dengan Waktu
            const assignedTime = asset.assigned_date ? formatDateID(asset.assigned_date) : '-';
            const returnTime = asset.return_date ? formatDateID(asset.return_date) : 'Tidak ada batas waktu';
            
            container.innerHTML = `
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg></div>
                    <div><p class="text-xs text-blue-600 uppercase font-bold">Sedang Dipinjam Oleh:</p><p class="text-sm font-bold text-gray-900 text-lg">${holder ? holder.name : 'Unknown'}</p></div>
                </div>
                <hr class="border-blue-200 mb-3">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div><p class="text-xs text-blue-600 uppercase font-bold">Waktu Pinjam:</p><p class="text-sm font-medium text-gray-800">${assignedTime}</p></div>
                    <div><p class="text-xs text-blue-600 uppercase font-bold">Batas Kembali:</p><p class="text-sm font-medium text-gray-800">${returnTime}</p></div>
                    <div class="col-span-2"><p class="text-xs text-blue-600 uppercase font-bold">Jumlah:</p><p class="text-sm font-bold text-gray-900 bg-white px-2 py-1 rounded border inline-block mt-1">${asset.quantity} Unit</p></div>
                </div>
                <div class="bg-blue-100 p-3 rounded text-center">
                    <p class="text-xs text-blue-700 font-bold mb-1">Status: SEDANG DIPAKAI</p>
                    <button disabled class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded cursor-not-allowed text-sm font-bold">Pinjam (Tidak Tersedia)</button>
                </div>
            `;

        } else if (asset.status === 'available') {
            statusEl.classList.add('bg-green-100', 'text-green-800');
            container.classList.add('bg-green-50', 'border-green-200');
            
            container.innerHTML = `
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <div><p class="text-green-800 font-bold text-sm">Tersedia di Gudang</p><p class="text-xs text-green-600">Siap untuk dipinjamkan.</p></div>
                    </div>
                    ${ authRole != 'admin' && asset.quantity > 0 ? 
                        `<button onclick="closeDetailModal(); openLoanModal(currentAssetData)" class="w-full md:w-auto bg-green-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-sm flex items-center justify-center gap-2"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Ajukan Pinjam</button>` 
                        : '' 
                    }
                </div>
            `;

        } else {
            statusEl.classList.add('bg-red-100', 'text-red-800');
            container.classList.add('bg-red-50', 'border-red-200');
            container.innerHTML = `
                <div class="flex items-center justify-center gap-2 py-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <div><p class="text-red-800 font-bold text-sm">Maintenance / Rusak</p><p class="text-xs text-red-600">Tidak dapat digunakan.</p></div>
                </div>
                <button disabled class="w-full mt-2 bg-gray-300 text-gray-500 py-2 px-4 rounded cursor-not-allowed text-sm font-bold">Pinjam (Tidak Tersedia)</button>
            `;
        }

        // Carousel Logic (Sama)
        let slidesHtml = '', dotsHtml = '', images = [];
        if (asset.image) images.push(asset.image);
        if (asset.image2) images.push(asset.image2);
        if (asset.image3) images.push(asset.image3);
        if (images.length === 0) images.push(null);

        totalSlides = images.length; currentSlide = 0;
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

    // Variable global sementara agar tombol pinjam di modal detail bisa akses data aset
    let currentAssetData = null;
    // Helper variable auth role (dilempar dari blade ke JS)
    const authRole = "{{ auth()->user()->role }}";

    // Override openDetailModal untuk set currentAssetData
    const originalOpenDetailModal = openDetailModal;
    openDetailModal = function(asset, holder) {
        currentAssetData = asset;
        originalOpenDetailModal(asset, holder);
    }

    function openLoanModal(asset) {
        document.getElementById('loanAssetId').value = asset.id;
        document.getElementById('loanAssetName').value = asset.name;
        const qtyInput = document.getElementById('loanQuantity');
        qtyInput.max = asset.quantity; qtyInput.value = 1;
        document.getElementById('loanMaxStockText').innerText = `(Tersedia: ${asset.quantity} unit)`;
        document.getElementById('loanModal').classList.remove('hidden');
    }
    function closeLoanModal() { document.getElementById('loanModal').classList.add('hidden'); }
</script>
@endsection