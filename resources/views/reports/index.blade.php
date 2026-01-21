@extends('layouts.main')

@section('container')
<div class="h-[calc(100vh-140px)] flex flex-col md:flex-row gap-6">
    
    {{-- PANEL KIRI: KONFIGURASI --}}
    <div class="w-full md:w-1/3 flex flex-col">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 flex-1 flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                    Konfigurasi Laporan
                </h2>
                <p class="text-xs text-gray-500 mt-1">Gunakan tombol print/download di dalam preview.</p>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                <form id="reportForm">
                    
                    {{-- 1. DATA FILTERING --}}
                    <div class="space-y-4">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 block pb-1">Tipe & Waktu</label>
                        
                        {{-- TIPE LAPORAN --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Laporan</label>
                            <select name="type" id="reportType" onchange="toggleFilters(); refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm font-semibold text-indigo-700 bg-indigo-50">
                                <option value="asset">Laporan Aset (Inventaris)</option>
                                <option value="borrowing">Laporan Peminjaman (Riwayat)</option>
                            </select>
                        </div>

                        {{-- FILTER TANGGAL (RANGE) --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="start_date" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="end_date" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                            </div>
                        </div>

                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 block pb-1 mt-4">Filter Detail</label>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                            <input type="text" name="search" placeholder="Nama, User, atau SN..." class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 shadow-sm" onchange="refreshPreview()"> 
                        </div>

                        {{-- FILTER KATEGORI (BARU) --}}
                        <div id="categoryContainer">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select name="category" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                                <option value="all">Semua Kategori</option>
                                @if(isset($categories))
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="statusSelect" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                                    <option value="all">Semua Status</option>
                                    <option value="available">Available</option>
                                    <option value="deployed">Deployed</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="broken">Broken</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                                <select name="sort" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                                    <option value="newest">Terbaru (Default)</option>
                                    <option value="oldest">Terlama</option>
                                    <option value="name_asc">Nama (A-Z)</option>
                                    <option value="stock_low">Stok Paling Sedikit</option>
                                    <option value="stock_high">Stok Paling Banyak</option>
                                    <optgroup label="Berdasarkan Status">
                                        <option value="status_available">Available Dulu</option>
                                        <option value="status_deployed">Deployed Dulu</option>
                                        <option value="status_maintenance">Maintenance Dulu</option>
                                        <option value="status_broken">Broken Dulu</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 2. LAYOUT & TAMPILAN --}}
                    <div class="space-y-4 mt-6">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 block pb-1">Tampilan PDF</label>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen</label>
                            <input type="text" name="custom_title" value="Laporan Inventaris Aset IT" class="w-full rounded-lg border-gray-300 text-sm shadow-sm" onchange="refreshPreview()">
                        </div>

                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Orientasi Kertas</label>
                                <select name="orientation" onchange="refreshPreview()" class="rounded border-gray-300 text-xs py-1 shadow-sm w-32">
                                    <option value="portrait">Portrait (Tegak)</option>
                                    <option value="landscape">Landscape (Miring)</option>
                                </select>
                            </div>
                            <div class="flex items-center pt-3" id="imagesContainer">
                                <label class="inline-flex items-center cursor-pointer select-none">
                                    <input type="checkbox" name="show_images" value="1" checked onchange="refreshPreview()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                    <span class="ml-2 text-sm text-gray-700 font-medium">Tampilkan Foto</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Kaki (Footer)</label>
                            <textarea name="admin_notes" rows="3" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm" placeholder="Contoh: Disetujui oleh Manager IT pada tanggal..."></textarea>
                        </div>
                    </div>

                    {{-- TOMBOL DOWNLOAD --}}
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <button type="button" onclick="downloadPdf()" class="w-full flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg shadow transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Download PDF Disini
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- PANEL KANAN: PREVIEW --}}
    <div class="w-full md:w-2/3 h-full">
        <div class="bg-gray-300 rounded-xl shadow-inner border border-gray-400 h-full flex flex-col overflow-hidden relative">
            
            {{-- Loading Spinner --}}
            <div id="loading-overlay" class="absolute inset-0 bg-white/90 z-20 flex flex-col items-center justify-center backdrop-blur-sm hidden">
                <svg class="w-12 h-12 text-indigo-600 animate-spin mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-sm font-bold text-gray-600 animate-pulse">Memuat Preview...</span>
            </div>

            {{-- Toolbar Info --}}
            <div class="bg-gray-700 text-white px-4 py-2 flex justify-between items-center text-xs font-mono shadow-md z-10">
                <span>PREVIEW MODE</span>
                <span id="page-info">A4 Portrait</span>
            </div>

            {{-- Iframe Preview --}}
            <div class="flex-1 p-4 md:p-8 flex justify-center overflow-auto bg-gray-500/10 custom-scrollbar-dark">
                <iframe id="pdf-frame" class="bg-white shadow-2xl rounded-sm transition-all duration-300" 
                        style="width: 210mm; min-height: 297mm; height: auto;" 
                        src="about:blank"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    const pdfUrl = "{{ route('reports.pdf') }}"; 
    
    // Status Options Data
    const assetStatuses = [
        {val: 'all', text: 'Semua Status'},
        {val: 'available', text: 'Available'},
        {val: 'deployed', text: 'Deployed'},
        {val: 'maintenance', text: 'Maintenance'},
        {val: 'broken', text: 'Broken'}
    ];
    const borrowingStatuses = [
        {val: 'all', text: 'Semua Status'},
        {val: 'pending', text: 'Menunggu Persetujuan'},
        {val: 'approved', text: 'Sedang Dipinjam'},
        {val: 'rejected', text: 'Ditolak'},
        {val: 'returned', text: 'Sudah Dikembalikan'}
    ];

    function toggleFilters() {
        const type = document.getElementById('reportType').value;
        const catContainer = document.getElementById('categoryContainer');
        const imgContainer = document.getElementById('imagesContainer');
        const statusSelect = document.getElementById('statusSelect');

        // 1. Toggle Category & Images
        if (type === 'borrowing') {
            catContainer.classList.add('hidden');
            imgContainer.classList.add('hidden');
            
            // Swap Status Options to Borrowing
            updateSelectOptions(statusSelect, borrowingStatuses);

        } else {
            catContainer.classList.remove('hidden');
            imgContainer.classList.remove('hidden');
            
            // Swap Status Options to Assets
            updateSelectOptions(statusSelect, assetStatuses);
        }
    }

    function updateSelectOptions(select, options) {
        // Simpan nilai lama jika ada yang cocok
        const oldVal = select.value;

        select.innerHTML = '';
        options.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.val;
            option.innerText = opt.text;
            select.appendChild(option);
        });

        // Restore value if possible, else default to 'all'
        // Cek apakah oldVal ada di opsi baru
        const exists = options.some(o => o.val === oldVal);
        if(exists) select.value = oldVal;
        else select.value = 'all';
    }

    function refreshPreview() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        const queryString = new URLSearchParams(formData).toString();
        
        const iframe = document.getElementById('pdf-frame');
        const loading = document.getElementById('loading-overlay');
        const pageInfo = document.getElementById('page-info');
        
        // Update tampilan ukuran iframe
        const orient = formData.get('orientation');
        pageInfo.innerText = orient === 'landscape' ? 'A4 Landscape' : 'A4 Portrait';
        iframe.style.width = orient === 'landscape' ? '297mm' : '210mm';
        iframe.style.minHeight = orient === 'landscape' ? '210mm' : '297mm';

        loading.classList.remove('hidden');
        
        // Tambahkan timestamp untuk menghindari cache browser
        iframe.src = `${pdfUrl}?${queryString}&t=${new Date().getTime()}`;

        iframe.onload = function() {
            loading.classList.add('hidden');
        };
    }

    function downloadPdf() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        // Tambahkan parameter download
        formData.append('download', '1');
        const queryString = new URLSearchParams(formData).toString();
        
        // Buka di tab baru / trigger download
        window.location.href = `${pdfUrl}?${queryString}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        toggleFilters(); // Set initial state
        refreshPreview();
    });
</script>

<style>
    .custom-scrollbar-dark::-webkit-scrollbar { width: 10px; height: 10px; }
    .custom-scrollbar-dark::-webkit-scrollbar-track { background: #e5e7eb; }
    .custom-scrollbar-dark::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 5px; }
    .custom-scrollbar-dark::-webkit-scrollbar-thumb:hover { background: #6b7280; }
</style>
@endsection