@extends('layouts.main')

@section('container')
{{-- Header --}}
<div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <h2 class="text-3xl font-bold leading-tight text-gray-900">
            Katalog Aset IT
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            @if(auth()->user()->role == 'admin')
                Kelola master data aset dan inventaris perusahaan.
            @else
                Cari dan ajukan peminjaman perangkat kerja yang tersedia.
            @endif
        </p>
    </div>
    
    {{-- Tombol Tambah (Hanya Admin) --}}
    @if(auth()->user()->role == 'admin')
    <div class="flex gap-2">
        <a href="{{ route('report.assets') }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Cetak Laporan
        </a>
        <a href="/assets/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Aset
        </a>
    </div>
    @endif
</div>

{{-- Search Bar Modern --}}
<div class="mb-6 relative">
    <form action="/assets" method="GET">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>
        <input type="text" name="search" class="block w-full rounded-xl border-gray-300 bg-white pl-11 pr-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cari aset berdasarkan nama, spesifikasi, atau serial number..." value="{{ request('search') }}">
    </form>
</div>

{{-- Grid Card Aset (Lebih Visual daripada Tabel Biasa) --}}
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @forelse($assets as $asset)
    <div class="group relative flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md">
        
        {{-- Thumbnail Image --}}
        <div class="aspect-w-16 aspect-h-9 bg-gray-200 overflow-hidden h-48 relative cursor-pointer" 
             onclick="openDetailModal({{ json_encode($asset) }})">
            @if($asset->image)
                <img src="{{ asset('storage/' . $asset->image) }}" alt="{{ $asset->name }}" class="h-full w-full object-cover object-center transition group-hover:scale-105 duration-300">
            @else
                <div class="flex h-full items-center justify-center bg-gray-100 text-gray-400">
                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
            @endif
            
            {{-- Badge Status --}}
            <div class="absolute top-2 right-2">
                @php
                    $colors = [
                        'available' => 'bg-green-100 text-green-800 border-green-200',
                        'deployed' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'maintenance' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'broken' => 'bg-red-100 text-red-800 border-red-200',
                    ];
                @endphp
                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold shadow-sm {{ $colors[$asset->status] ?? 'bg-gray-100' }}">
                    {{ ucfirst($asset->status) }}
                </span>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex flex-1 flex-col p-4">
            <h3 class="text-lg font-semibold text-gray-900 cursor-pointer hover:text-indigo-600" onclick="openDetailModal({{ json_encode($asset) }})">
                {{ $asset->name }}
            </h3>
            <p class="mt-1 text-xs text-gray-500 font-mono">{{ $asset->serial_number }}</p>
            <p class="mt-2 text-sm text-gray-600 line-clamp-2 flex-1">
                {{ $asset->description ?? 'Tidak ada deskripsi detail.' }}
            </p>
            
            {{-- Action Buttons --}}
            <div class="mt-4 flex items-center justify-between gap-2 border-t pt-4">
                <button onclick="openDetailModal({{ json_encode($asset) }})" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Lihat Detail
                </button>

                @if(auth()->user()->role == 'admin')
                    <a href="/assets/{{ $asset->id }}/edit" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                        Edit
                    </a>
                @elseif($asset->status == 'available')
                    <button onclick="openBorrowModal({{ json_encode($asset) }})" class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Pinjam Sekarang
                    </button>
                @else
                    <span class="text-xs font-medium text-gray-400 cursor-not-allowed">Tidak Tersedia</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
        <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada aset ditemukan</h3>
        <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci pencarian Anda.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($assets->hasPages())
<div class="mt-8">
    {{ $assets->links() }}
</div>
@endif

{{-- ================= MODAL DETAIL ASET ================= --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 w-full text-center sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="detailModalTitle">Detail Aset</h3>
                        <div class="mt-4">
                            {{-- Image Gallery Grid --}}
                            <div class="grid grid-cols-3 gap-2 mb-4" id="detailImages">
                                </div>
                            
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Serial Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono" id="detailSN">-</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900" id="detailStatus">-</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Spesifikasi</dt>
                                    <dd class="mt-1 text-sm text-gray-900" id="detailDesc">-</dd>
                                </div>
                                <div class="sm:col-span-2 bg-yellow-50 p-3 rounded-md border border-yellow-100">
                                    <dt class="text-xs font-bold text-yellow-800 uppercase tracking-wide">Kondisi Fisik</dt>
                                    <dd class="mt-1 text-sm text-yellow-900 italic" id="detailCondition">-</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL KONFIRMASI PINJAM ================= --}}
<div id="borrowModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeBorrowModal()"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>
        
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <form id="borrowForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </div>
                        <div class="mt-3 w-full text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Konfirmasi Peminjaman</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Anda akan meminjam <span id="borrowAssetName" class="font-bold text-gray-800"></span>. Pastikan Anda telah memeriksa kondisi barang melalui foto detail.
                                </p>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="reason" class="block text-sm font-medium text-gray-700">Tujuan Peminjaman <span class="text-red-500">*</span></label>
                                        <input type="text" name="reason" id="reason" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" placeholder="Contoh: Untuk meeting luar kota">
                                    </div>
                                    
                                    <div>
                                        <label for="return_date" class="block text-sm font-medium text-gray-700">Rencana Kembali (Opsional)</label>
                                        <input type="date" name="return_date" id="return_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika pemakaian jangka panjang.</p>
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

{{-- JAVASCRIPT LOGIC --}}
<script>
    // Fungsi Helper Gambar
    function getImageUrl(filename) {
        return filename ? `/storage/${filename}` : 'https://via.placeholder.com/150?text=No+Image';
    }

    // --- LOGIC DETAIL MODAL ---
    function openDetailModal(asset) {
        document.getElementById('detailModalTitle').innerText = asset.name;
        document.getElementById('detailSN').innerText = asset.serial_number;
        document.getElementById('detailStatus').innerText = asset.status.toUpperCase();
        document.getElementById('detailDesc').innerText = asset.description || '-';
        document.getElementById('detailCondition').innerText = asset.condition_notes || 'Tidak ada catatan kondisi khusus.';

        // Render Images
        const imgContainer = document.getElementById('detailImages');
        imgContainer.innerHTML = ''; // Reset
        
        const images = [asset.image, asset.image2, asset.image3];
        images.forEach(img => {
            if(img) {
                const imgEl = document.createElement('img');
                imgEl.src = getImageUrl(img);
                imgEl.className = "h-24 w-full object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-75";
                imgEl.onclick = function() { window.open(this.src, '_blank'); }; // Klik untuk zoom
                imgContainer.appendChild(imgEl);
            }
        });

        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // --- LOGIC BORROW MODAL ---
    function openBorrowModal(asset) {
        document.getElementById('borrowAssetName').innerText = asset.name;
        
        // Set Action URL Form secara dinamis
        const form = document.getElementById('borrowForm');
        form.action = `/assets/${asset.id}/request`;
        
        document.getElementById('borrowModal').classList.remove('hidden');
    }

    function closeBorrowModal() {
        document.getElementById('borrowModal').classList.add('hidden');
    }
</script>
@endsection