@extends('layouts.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Aset Saya</h1>
        <p class="mt-2 text-gray-600">Daftar inventaris yang saat ini menjadi tanggung jawab Anda.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($myAssets->isEmpty())
            <div class="p-12 text-center flex flex-col items-center">
                <div class="bg-indigo-50 p-4 rounded-full mb-4">
                    <svg class="h-10 w-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Belum ada aset</h3>
                <p class="text-gray-500 mt-1">Anda belum meminjam aset apapun saat ini.</p>
                <a href="{{ route('assets.index') }}" class="mt-5 inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                    Pinjam Aset Baru
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Informasi Aset</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi & Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($myAssets as $item)
                        {{-- Mengirim object asset (dari relasi) + data assignment date ke fungsi modal --}}
                        <tr class="hover:bg-gray-50 transition cursor-pointer group" onclick="openDetailModal({{ json_encode($item->asset) }}, '{{ $item->borrowed_at }}', '{{ $item->return_date }}')">
                            {{-- Info Aset --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 flex-shrink-0 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden relative">
                                        @if($item->asset->image)
                                            <img class="h-full w-full object-cover" src="{{ asset('storage/' . $item->asset->image) }}" alt="Foto Aset">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-gray-400">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition">{{ $item->asset->name }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $item->asset->serial_number }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">Qty: {{ $item->quantity }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Lokasi & Kategori --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->asset->category ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->asset->location ?? 'Umum' }}</div>
                            </td>

                            {{-- Tanggal Pinjam --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($item->borrowed_at)->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->borrowed_at)->diffForHumans() }}
                                </div>
                            </td>

                            {{-- Tombol Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" onclick="event.stopPropagation()">
                                <div class="flex justify-end gap-2">
                                    {{-- 1. Tombol Detail --}}
                                    <button onclick="openDetailModal({{ json_encode($item->asset) }}, '{{ $item->borrowed_at }}', '{{ $item->return_date }}')" 
                                            class="text-gray-600 hover:text-indigo-600 bg-white hover:bg-gray-50 border border-gray-300 px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                                        Detail
                                    </button>

                                    {{-- 2. Tombol Kembalikan --}}
                                    @php
                                        $imgUrl = $item->asset->image ? asset('storage/' . $item->asset->image) : null;
                                        $assignedDate = $item->borrowed_at;
                                    @endphp
                                    
                                    {{-- Kirim ID Request Peminjaman ($item->id) bukan ID Aset --}}
                                    <button onclick="openReturnModal({{ $item->id }}, '{{ $item->asset->name }}', '{{ $item->asset->serial_number }}', '{{ $imgUrl }}', '{{ $assignedDate }}')"
                                            class="text-white bg-indigo-600 hover:bg-indigo-700 border border-transparent px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm flex items-center">
                                        Kembalikan
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ================= MODAL DETAIL ASET LENGKAP ================= --}}
<div id="myAssetDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" onclick="closeMyAssetDetail()"></div>
        
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
            {{-- Header Modal --}}
            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center shadow-md z-10 relative">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Detail Informasi Aset
                </h3>
                <button onclick="closeMyAssetDetail()" class="text-indigo-200 hover:text-white transition rounded-full p-1 hover:bg-indigo-500/50">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="bg-white px-0">
                <div class="flex flex-col md:flex-row">
                    {{-- Bagian Kiri: Gambar --}}
                    <div class="w-full md:w-5/12 bg-gray-50 flex flex-col items-center justify-center p-6 border-r border-gray-100">
                        <div class="aspect-square w-full rounded-2xl bg-white border border-gray-200 overflow-hidden relative shadow-sm mb-4 group">
                            <img id="detailImg" src="" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" onerror="this.style.display='none'">
                            <div id="detailImgPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-gray-300 bg-gray-50">
                                <svg class="h-16 w-16 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <span class="text-xs font-medium">Tidak ada gambar</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-green-100 text-green-800 border border-green-200 shadow-sm">
                            <span class="w-2 h-2 mr-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Sedang Dipinjam
                        </span>
                    </div>

                    {{-- Bagian Kanan: Informasi --}}
                    <div class="w-full md:w-7/12 p-6 md:p-8 space-y-6">
                        <div>
                            <h2 id="detailAssetName" class="text-2xl font-extrabold text-gray-900 leading-tight">Nama Aset</h2>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 uppercase tracking-wider">Serial Number</span>
                                <p id="detailAssetSN" class="text-sm font-mono text-gray-600 font-medium">SN-12345678</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-4">
                            {{-- Kategori --}}
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 mt-0.5">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-0.5">Kategori</p>
                                    <p id="detailCategory" class="text-sm font-semibold text-gray-800">-</p>
                                </div>
                            </div>
                            {{-- Lokasi --}}
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 mt-0.5">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-0.5">Lokasi</p>
                                    <p id="detailLocation" class="text-sm font-semibold text-gray-800">-</p>
                                </div>
                            </div>
                            {{-- Tanggal Pinjam --}}
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-blue-50 text-blue-600 mt-0.5">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-0.5">Dipinjam Pada</p>
                                    <p id="detailAssigned" class="text-sm font-semibold text-gray-800">-</p>
                                </div>
                            </div>
                            {{-- Tenggat Waktu --}}
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-orange-50 text-orange-600 mt-0.5">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-0.5">Batas Kembali</p>
                                    <p id="detailReturnDate" class="text-sm font-semibold text-gray-800">-</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <div class="flex items-center mb-2">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Kondisi & Catatan</p>
                                <span id="detailCondition" class="ml-auto text-xs px-2 py-0.5 rounded-full font-bold bg-gray-100 text-gray-700 uppercase">-</span>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 leading-relaxed border border-gray-100 italic" id="detailDesc">
                                -
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-100">
                <button type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="closeMyAssetDetail()">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL FORM PENGEMBALIAN ================= --}}
<div id="returnModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeReturnModal()"></div>
        
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md">
            {{-- PERBAIKAN: Form POST ke route borrowing.return --}}
            <form id="returnForm" method="POST" action=""> 
                @csrf
                {{-- Header --}}
                <div class="bg-white px-6 pt-6 pb-2">
                    <h3 class="text-xl font-bold text-gray-900">Form Pengembalian Aset</h3>
                    <p class="text-sm text-gray-500 mt-1">Pastikan kondisi barang sesuai sebelum dikembalikan.</p>
                </div>

                <div class="px-6 py-4">
                    {{-- Preview Barang --}}
                    <div class="flex items-center gap-4 bg-indigo-50 p-4 rounded-xl border border-indigo-100 mb-6">
                        <div class="h-14 w-14 rounded-lg bg-white border border-indigo-200 flex-shrink-0 overflow-hidden flex items-center justify-center">
                            <img id="returnAssetImg" src="" class="w-full h-full object-cover hidden">
                            <svg id="returnAssetIcon" class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold text-indigo-500 uppercase tracking-wide">Barang yang dikembalikan</p>
                            <h4 id="returnAssetName" class="text-base font-bold text-gray-900 truncate">Nama Aset</h4>
                            <p id="returnAssetSN" class="text-xs text-gray-500 font-mono truncate">SN-XXXXXX</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">

                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kondisi Barang Saat Ini</label>
                            <select name="condition" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="" disabled selected>-- Pilih Kondisi --</option>
                                <option value="good">Baik (Layak Pakai)</option>
                                <option value="minor_damage">Rusak Ringan</option>
                                <option value="major_damage">Rusak Berat</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Catatan Tambahan</label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Ada lecet sedikit di bagian bawah..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Footer Tombol --}}
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent px-5 py-2.5 bg-indigo-600 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Ajukan Pengembalian
                    </button>
                    <button type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-5 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition" onclick="closeReturnModal()">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- LOGIC DETAIL ASET ---
    function openDetailModal(asset, assignedDate, returnDate) { // Menerima object asset + assignedDate + returnDate
        document.getElementById('detailAssetName').innerText = asset.name;
        document.getElementById('detailAssetSN').innerText = asset.serial_number;
        document.getElementById('detailCategory').innerText = asset.category || '-';
        document.getElementById('detailLocation').innerText = asset.location || '-';
        document.getElementById('detailCondition').innerText = asset.condition_notes || 'Baik';
        document.getElementById('detailDesc').innerText = asset.description || 'Tidak ada deskripsi.';
        
        // Image Logic
        const imgEl = document.getElementById('detailImg');
        const placeholderEl = document.getElementById('detailImgPlaceholder');
        if (asset.image) {
            imgEl.src = `/storage/${asset.image}`;
            imgEl.style.display = 'block';
            placeholderEl.style.display = 'none';
        } else {
            imgEl.style.display = 'none';
            placeholderEl.style.display = 'flex';
        }

        // Date Logic (assignedDate format YYYY-MM-DD HH:MM:SS)
        const dateOptions = { 
            day: 'numeric', month: 'long', year: 'numeric', 
            hour: '2-digit', minute: '2-digit' 
        };

        if (assignedDate) {
            const d = new Date(assignedDate);
            document.getElementById('detailAssigned').innerText = d.toLocaleDateString('id-ID', dateOptions);
        } else {
            document.getElementById('detailAssigned').innerText = '-';
        }

        if (returnDate) {
            const r = new Date(returnDate);
            document.getElementById('detailReturnDate').innerText = r.toLocaleDateString('id-ID', dateOptions);
        } else {
            document.getElementById('detailReturnDate').innerText = '-';
        }
        
        document.getElementById('myAssetDetailModal').classList.remove('hidden');
    }
    
    function closeMyAssetDetail() { 
        document.getElementById('myAssetDetailModal').classList.add('hidden'); 
    }

    // --- LOGIC RETURN ASET ---
    function openReturnModal(reqId, assetName, assetSN, assetImgUrl, assignedDateRaw) {
        // PERBAIKAN: Action URL diarahkan ke route khusus user (borrowing.return_user)
        // Route: Route::post('/borrowing/{id}/return-user', ...)
        const form = document.getElementById('returnForm');
        form.action = `/borrowing/${reqId}/return-user`;

        document.getElementById('returnAssetName').innerText = assetName;
        document.getElementById('returnAssetSN').innerText = assetSN;
        
        // Image Preview Logic for Return Modal
        const imgEl = document.getElementById('returnAssetImg');
        const iconEl = document.getElementById('returnAssetIcon');
        
        if (assetImgUrl) {
            imgEl.src = assetImgUrl;
            imgEl.classList.remove('hidden');
            iconEl.classList.add('hidden');
        } else {
            imgEl.classList.add('hidden');
            iconEl.classList.remove('hidden');
        }


        
        document.getElementById('returnModal').classList.remove('hidden');
    }
    
    function closeReturnModal() { 
        document.getElementById('returnModal').classList.add('hidden'); 
    }
</script>
@endsection