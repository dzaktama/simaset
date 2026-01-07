@extends('layouts.main')

@section('container')
<div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <h2 class="text-3xl font-bold leading-tight text-gray-900">Katalog Aset IT</h2>
        <p class="mt-1 text-sm text-gray-600">
            @if(auth()->user()->role == 'admin') Kelola data aset. @else Cari, pinjam, atau booking aset yang sedang digunakan. @endif
        </p>
    </div>
    
    @if(auth()->user()->role == 'admin')
    <div class="flex gap-2">
        <a href="{{ route('report.assets') }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Cetak PDF</a>
        <a href="/assets/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">+ Tambah Aset</a>
    </div>
    @endif
</div>

{{-- Search --}}
<div class="mb-6 relative">
    <form action="/assets" method="GET">
        <input type="text" name="search" class="block w-full rounded-xl border-gray-300 pl-4 pr-10 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cari aset..." value="{{ request('search') }}">
    </form>
</div>

{{-- Grid Aset --}}
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @forelse($assets as $asset)
    <div class="group relative flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition">
        
        {{-- Thumbnail --}}
        <div class="h-48 bg-gray-200 overflow-hidden relative cursor-pointer" onclick="openDetailModal({{ json_encode($asset) }})">
            @if($asset->image)
                <img src="{{ asset('storage/' . $asset->image) }}" class="h-full w-full object-cover transition group-hover:scale-105 duration-300">
            @else
                <div class="flex h-full items-center justify-center text-gray-400">No Image</div>
            @endif
            
            {{-- Badge Status --}}
            @php
                $statusColors = [
                    'available' => 'bg-green-100 text-green-800',
                    'deployed' => 'bg-blue-100 text-blue-800',
                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                    'broken' => 'bg-red-100 text-red-800',
                ];
                $statusLabel = [
                    'available' => 'Tersedia',
                    'deployed' => 'Dipinjam',
                    'maintenance' => 'Perbaikan',
                    'broken' => 'Rusak',
                ];
            @endphp
            <span class="absolute top-2 right-2 rounded-full px-2 py-1 text-xs font-bold shadow-sm {{ $statusColors[$asset->status] ?? 'bg-gray-100' }}">
                {{ $statusLabel[$asset->status] ?? ucfirst($asset->status) }}
            </span>
        </div>

        {{-- Content --}}
        <div class="flex flex-1 flex-col p-4">
            <h3 class="text-lg font-semibold text-gray-900 cursor-pointer hover:text-indigo-600" onclick="openDetailModal({{ json_encode($asset) }})">{{ $asset->name }}</h3>
            <p class="text-xs text-gray-500 font-mono mb-2">{{ $asset->serial_number }}</p>
            
            {{-- INFO PEMINJAM (JIKA DIPINJAM) --}}
            @if($asset->status == 'deployed' && $asset->holder)
                <div class="bg-blue-50 p-2 rounded-md border border-blue-100 mb-2">
                    <p class="text-xs text-blue-800 font-semibold flex items-center gap-1">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        {{ $asset->holder->name }}
                    </p>
                    @if($asset->latestApprovedRequest && $asset->latestApprovedRequest->return_date)
                        <p class="text-xs text-blue-600 mt-1 flex items-center gap-1">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Kembali: {{ \Carbon\Carbon::parse($asset->latestApprovedRequest->return_date)->format('d M Y') }}
                        </p>
                    @else
                        <p class="text-xs text-blue-600 mt-1 italic">Tidak ada tanggal kembali.</p>
                    @endif
                </div>
            @endif

            <div class="mt-auto flex items-center justify-between border-t pt-4">
                <button onclick="openDetailModal({{ json_encode($asset) }})" class="text-sm font-medium text-gray-600 hover:text-gray-900">Detail</button>
                
                @if(auth()->user()->role == 'admin')
                    <a href="/assets/{{ $asset->id }}/edit" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Edit</a>
                @elseif($asset->status == 'available')
                    <button onclick="openBorrowModal({{ json_encode($asset) }}, 'pinjam')" class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-green-700 shadow-sm">
                        Pinjam
                    </button>
                @elseif($asset->status == 'deployed')
                    <button onclick="openBorrowModal({{ json_encode($asset) }}, 'booking')" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-indigo-700 shadow-sm flex items-center gap-1">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Booking
                    </button>
                @else
                    <span class="text-xs font-medium text-gray-400 cursor-not-allowed">Tidak Tersedia</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-12 text-center text-gray-500">Tidak ada aset ditemukan.</div>
    @endforelse
</div>

<div class="mt-8">{{ $assets->links() }}</div>

{{-- MODAL DETAIL --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-xl font-bold leading-6 text-gray-900" id="detailTitle">Detail Aset</h3>
                <div class="grid grid-cols-3 gap-2 mt-4" id="detailImages"></div>
                <dl class="mt-4 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-1"><dt class="text-sm font-medium text-gray-500">Serial Number</dt><dd class="mt-1 text-sm text-gray-900 font-mono" id="detailSN"></dd></div>
                    <div class="sm:col-span-1"><dt class="text-sm font-medium text-gray-500">Status</dt><dd class="mt-1 text-sm text-gray-900 font-bold" id="detailStatus"></dd></div>
                    <div class="sm:col-span-2"><dt class="text-sm font-medium text-gray-500">Deskripsi</dt><dd class="mt-1 text-sm text-gray-900" id="detailDesc"></dd></div>
                    <div class="sm:col-span-2 bg-yellow-50 p-3 rounded border border-yellow-200"><dt class="text-xs font-bold text-yellow-800">Kondisi Fisik</dt><dd class="mt-1 text-sm text-yellow-900" id="detailCondition"></dd></div>
                </dl>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BORROW / BOOKING --}}
<div id="borrowModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeBorrowModal()"></div>
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <form id="borrowForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="mx-auto flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="borrowModalTitle">Form Peminjaman</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-indigo-50 p-3 rounded-md border border-indigo-100 text-sm text-indigo-800" id="bookingAlert" style="display: none;">
                            <strong>Info:</strong> Barang ini sedang dipinjam. Permintaan Anda akan masuk daftar antrean (Booking).
                        </div>

                        <p class="text-sm text-gray-500">Anda akan mengajukan permintaan untuk: <span id="borrowAssetName" class="font-bold text-gray-900"></span></p>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tujuan Peminjaman</label>
                            <input type="text" name="reason" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2" placeholder="Contoh: Kebutuhan project...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rencana Kembali (Opsional)</label>
                            <input type="date" name="return_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="submit" id="btnSubmitBorrow" class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Ajukan</button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeBorrowModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getImg(path) { return path ? `/storage/${path}` : 'https://via.placeholder.com/150?text=No+Image'; }

    // DETAIL MODAL
    function openDetailModal(asset) {
        document.getElementById('detailTitle').innerText = asset.name;
        document.getElementById('detailSN').innerText = asset.serial_number;
        document.getElementById('detailStatus').innerText = asset.status.toUpperCase();
        document.getElementById('detailDesc').innerText = asset.description || '-';
        document.getElementById('detailCondition').innerText = asset.condition_notes || '-';
        const gallery = document.getElementById('detailImages');
        gallery.innerHTML = '';
        [asset.image, asset.image2, asset.image3].forEach(img => {
            if(img) gallery.innerHTML += `<img src="${getImg(img)}" class="h-24 w-full object-cover rounded border cursor-pointer" onclick="window.open(this.src)">`;
        });
        document.getElementById('detailModal').classList.remove('hidden');
    }
    function closeDetailModal() { document.getElementById('detailModal').classList.add('hidden'); }

    // BOOKING MODAL
    function openBorrowModal(asset, type) {
        document.getElementById('borrowAssetName').innerText = asset.name;
        document.getElementById('borrowForm').action = `/assets/${asset.id}/request`;
        
        // Atur tampilan beda antara Pinjam vs Booking
        const alertBox = document.getElementById('bookingAlert');
        const title = document.getElementById('borrowModalTitle');
        const btn = document.getElementById('btnSubmitBorrow');

        if (type === 'booking') {
            alertBox.style.display = 'block';
            title.innerText = "Form Booking (Antrean)";
            btn.innerText = "Booking Sekarang";
            btn.classList.replace('bg-green-600', 'bg-indigo-600'); // Ganti warna
        } else {
            alertBox.style.display = 'none';
            title.innerText = "Form Peminjaman";
            btn.innerText = "Pinjam Sekarang";
            btn.classList.replace('bg-indigo-600', 'bg-green-600');
        }

        document.getElementById('borrowModal').classList.remove('hidden');
    }
    function closeBorrowModal() { document.getElementById('borrowModal').classList.add('hidden'); }
</script>
@endsection