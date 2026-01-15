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
                        <tr class="hover:bg-gray-50 transition cursor-pointer group" onclick="openDetailModal({{ json_encode($item->asset) }}, '{{ $item->borrowed_at }}')">
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
                                    <button onclick="openDetailModal({{ json_encode($item->asset) }}, '{{ $item->borrowed_at }}')" 
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
        
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
            {{-- Header Modal --}}
            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Detail Informasi Aset
                </h3>
                <button onclick="closeMyAssetDetail()" class="text-indigo-200 hover:text-white transition rounded-full p-1 hover:bg-indigo-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="bg-white px-6 py-6">
                <div class="flex flex-col md:flex-row gap-6">
                    {{-- Bagian Gambar --}}
                    <div class="w-full md:w-1/3">
                        <div class="aspect-square rounded-xl bg-gray-100 border border-gray-200 overflow-hidden relative shadow-inner">
                            <img id="detailImg" src="" class="w-full h-full object-cover" onerror="this.style.display='none'">
                            <div id="detailImgPlaceholder" class="absolute inset-0 flex items-center justify-center text-gray-400">
                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-blue-100 text-blue-800 border border-blue-200">
                                Sedang Dipinjam
                            </span>
                        </div>
                    </div>

                    {{-- Bagian Informasi --}}
                    <div class="w-full md:w-2/3 space-y-4">
                        <div>
                            <h2 id="detailAssetName" class="text-2xl font-bold text-gray-900 leading-tight">Nama Aset</h2>
                            <p id="detailAssetSN" class="text-sm text-gray-500 font-mono mt-1">SN-12345678</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Kategori</span>
                                <p id="detailCategory" class="text-sm font-semibold text-gray-800 mt-0.5">-</p>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Lokasi</span>
                                <p id="detailLocation" class="text-sm font-semibold text-gray-800 mt-0.5">-</p>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Tanggal Pinjam</span>
                                <p id="detailAssigned" class="text-sm font-semibold text-gray-800 mt-0.5">-</p>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Kondisi Awal</span>
                                <p id="detailCondition" class="text-sm font-semibold text-gray-800 mt-0.5">-</p>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Deskripsi / Spesifikasi</span>
                            <div class="relative">
                                <p id="detailDesc" class="text-sm text-gray-600 mt-1 leading-relaxed bg-white border border-gray-100 p-3 rounded-lg shadow-sm h-24 overflow-y-auto custom-scrollbar">
                                    -
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="closeMyAssetDetail()">
                    Tutup
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
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Pengembalian</label>
                            <input type="date" name="return_date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                        </div>
                        
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
    function openDetailModal(asset, assignedDate) { // Menerima object asset + assignedDate
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
        if (assignedDate) {
            const d = new Date(assignedDate);
            // Format ID: 12 Januari 2026
            document.getElementById('detailAssigned').innerText = d.toLocaleDateString('id-ID', {
                day: 'numeric', month: 'long', year: 'numeric'
            });
        } else {
            document.getElementById('detailAssigned').innerText = '-';
        }
        
        document.getElementById('myAssetDetailModal').classList.remove('hidden');
    }
    
    function closeMyAssetDetail() { 
        document.getElementById('myAssetDetailModal').classList.add('hidden'); 
    }

    // --- LOGIC RETURN ASET ---
    function openReturnModal(reqId, assetName, assetSN, assetImgUrl, assignedDateRaw) {
        // PERBAIKAN: Action URL diarahkan ke route borrowing.return
        // Route: Route::post('/borrowing/{id}/return', ...)
        const form = document.getElementById('returnForm');
        form.action = `/borrowing/${reqId}/return`;

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

        // [FIX LOGIC] Set Minimal Tanggal Pengembalian = Tanggal Pinjam
        const dateInput = document.getElementsByName('return_date')[0];
        if(assignedDateRaw) {
            const minDate = new Date(assignedDateRaw).toISOString().split('T')[0];
            dateInput.min = minDate; 
        }
        
        document.getElementById('returnModal').classList.remove('hidden');
    }
    
    function closeReturnModal() { 
        document.getElementById('returnModal').classList.add('hidden'); 
    }
</script>
@endsection