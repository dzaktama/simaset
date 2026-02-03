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
                <p class="text-xs text-gray-500 mt-1">Atur filter di bawah untuk menghasilkan laporan PDF.</p>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                <form id="reportForm" class="space-y-6">
                    
                    {{-- SECTION 1: JENIS LAPORAN --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Jenis Laporan</label>
                        <select name="type" id="reportType" onchange="toggleFilters(); refreshPreview()" class="block w-full px-4 py-3 text-base border-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm transition ease-in-out duration-150 font-medium text-gray-900 bg-white">
                            <option value="asset">Laporan Inventaris Aset</option>
                            <option value="borrowing">Laporan Riwayat Peminjaman</option>
                        </select>
                    </div>

                    {{-- SECTION 2: WAKTU --}}
                    <div id="dateFilterSection" class="hidden space-y-4 pt-4 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-800">Periode Waktu</label>
                        
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" onclick="setPreset('this_month')" class="flex items-center justify-center px-4 py-2 border-2 border-gray-300 shadow-sm text-xs font-bold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Bulan Ini</button>
                            <button type="button" onclick="setPreset('last_month')" class="flex items-center justify-center px-4 py-2 border-2 border-gray-300 shadow-sm text-xs font-bold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Bulan Lalu</button>
                            <button type="button" onclick="setPreset('this_year')" class="flex items-center justify-center px-4 py-2 border-2 border-gray-300 shadow-sm text-xs font-bold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Tahun Ini</button>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-xs font-bold text-gray-500 uppercase mb-1">Dari Tanggal</label>
                                <input type="date" name="start_date" id="start_date" onchange="refreshPreview()" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-2 border-gray-300 rounded-lg py-2 px-3 font-medium text-gray-900">
                            </div>
                            <div>
                                <label for="end_date" class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                                <input type="date" name="end_date" id="end_date" onchange="refreshPreview()" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-2 border-gray-300 rounded-lg py-2 px-3 font-medium text-gray-900">
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: FILTER DETAIL --}}
                    <div class="space-y-4 pt-4 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-800">Filter Detail</label>
                        
                        {{-- Search --}}
                        <div>
                             <input type="text" name="search" placeholder="Cari data (Nama, Serial Number)..." class="focus:ring-indigo-500 focus:border-indigo-500 block w-full px-4 text-sm border-2 border-gray-300 rounded-lg py-3 font-medium text-gray-900 shadow-sm" onchange="refreshPreview()"> 
                        </div>

                        {{-- Kategori --}}
                        <div id="categoryContainer">
                            <select name="category" onchange="refreshPreview()" class="mt-1 block w-full px-3 py-2.5 text-base border-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg font-medium text-gray-900">
                                <option value="all">Semua Kategori</option>
                                @if(isset($categories))
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Status --}}
                            <div>
                                <select name="status" id="statusSelect" onchange="refreshPreview()" class="mt-1 block w-full px-3 py-2.5 text-base border-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg font-medium text-gray-900">
                                    <option value="all">Semua Status</option>
                                    {{-- Asset Statuses --}}
                                    <optgroup label="Status Aset" id="optgroup-asset">
                                        <option value="available">Available</option>
                                        <option value="deployed">Deployed</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="broken">Broken</option>
                                    </optgroup>
                                    {{-- Borrowing Statuses --}}
                                    <optgroup label="Status Peminjaman" id="optgroup-borrowing" class="hidden">
                                        <option value="pending">Menunggu Persetujuan</option>
                                        <option value="approved">Sedang Dipinjam</option>
                                        <option value="returned">Sudah Dikembalikan</option>
                                        <option value="rejected">Ditolak</option>
                                    </optgroup>
                                </select>
                            </div>
                            {{-- Sort --}}
                            <div>
                                <select name="sort" onchange="refreshPreview()" class="mt-1 block w-full px-3 py-2.5 text-base border-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg font-medium text-gray-900">
                                    <option value="newest">Terbaru</option>
                                    <option value="oldest">Terlama</option>
                                    <option value="name_asc">Nama A-Z</option>
                                    <optgroup label="Stok">
                                        <option value="stock_low">Stok Minim</option>
                                        <option value="stock_high">Stok Banyak</option>
                                    </optgroup>
                                    <optgroup label="Prioritas Status">
                                        <option value="status_available">Available</option>
                                        <option value="status_deployed">Deployed</option>
                                        <option value="status_maintenance">Maintenance</option>
                                        <option value="status_broken">Broken</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- DOWNLOAD & OPTIONS --}}
                    <div class="pt-6 border-t border-gray-100 space-y-3">
                        <button type="button" onclick="downloadPdf()" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            Download PDF Report
                        </button>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" onclick="downloadExcel('csv')" class="flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-bold text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                <svg class="mr-2 h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Export .csv
                            </button>
                            <button type="button" onclick="downloadExcel('xlsx')" class="flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                                <svg class="mr-2 h-5 w-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                Export .xlsx
                            </button>
                        </div>
                    </div>

                    {{-- ADVANCED OPTIONS (Always Visible) --}}
                    <div id="advancedOptions" class="space-y-4 pt-6 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-800">Kustomisasi Layout</label>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Judul Laporan</label>
                            <input type="text" name="custom_title" value="Laporan Aset" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full px-4 text-sm border-2 border-gray-300 rounded-lg py-2.5 font-medium text-gray-900 shadow-sm" onchange="refreshPreview()">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Orientasi</label>
                                <select name="orientation" onchange="refreshPreview()" class="block w-full px-3 py-2.5 text-base border-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg font-medium text-gray-900">
                                    <option value="portrait">Portrait (Tegak)</option>
                                    <option value="landscape">Landscape (Miring)</option>
                                </select>
                            </div>
                            <div id="imagesContainer" class="flex items-center pt-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_images" value="1" checked onchange="refreshPreview()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-5 w-5">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Sertakan Foto</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Catatan Kaki (Footer)</label>
                            <textarea name="admin_notes" rows="2" onchange="refreshPreview()" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full px-4 text-sm border-2 border-gray-300 rounded-lg py-2.5 font-medium text-gray-900 shadow-sm" placeholder="Contoh: Mengetahui, Kepala Bagian IT..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- PANEL KANAN: PREVIEW --}}
    <div class="w-full md:w-2/3 h-full">
        <div class="bg-gray-300 rounded-xl shadow-inner border border-gray-400 h-full flex flex-col overflow-hidden relative">
            
            {{-- Loading Spinner --}}
            <div id="loading-overlay" class="absolute inset-0 bg-white z-20 flex flex-col items-center justify-center hidden">
                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-indigo-600 mb-4"></div>
                <h3 class="text-lg font-bold text-gray-800 animate-pulse">Sedang Menyiapkan Laporan...</h3>
                <p class="text-sm text-gray-500">Mohon tunggu sebentar, data sedang diproses.</p>
            </div>

            {{-- Toolbar Info --}}
            <div class="bg-gray-700 text-white px-4 py-2 flex justify-between items-center text-xs font-mono shadow-md z-10">
                <span class="font-bold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    LIVE PREVIEW
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="refreshPreview()" title="Refresh Preview" class="p-1 hover:bg-gray-600 rounded transition text-gray-300 hover:text-white focus:outline-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </button>
                    <span id="page-info" class="bg-gray-600 px-2 py-0.5 rounded">A4 Portrait</span>
                </div>
            </div>

            {{-- Iframe Preview --}}
            <div class="flex-1 p-0 flex justify-center overflow-auto bg-gray-500/10 custom-scrollbar-dark">
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
        const dateSection = document.getElementById('dateFilterSection');
        
        // OptGroups
        const optAsset = document.getElementById('optgroup-asset');
        const optBorrow = document.getElementById('optgroup-borrowing');

        // 1. Toggle Filter Visibility
        if (type === 'borrowing') {
            catContainer.classList.add('hidden');
            imgContainer.classList.add('hidden');
            dateSection.classList.remove('hidden'); 
            
            // Switch Status Options
            if(optAsset) optAsset.classList.add('hidden');
            if(optBorrow) optBorrow.classList.remove('hidden');
            
            // Reset selection to all if current selection is invalid for this type (optional logic)
            document.getElementById('statusSelect').value = 'all';

        } else {
            catContainer.classList.remove('hidden');
            imgContainer.classList.remove('hidden');
            dateSection.classList.add('hidden');
            
            // Switch Status Options
            if(optAsset) optAsset.classList.remove('hidden');
            if(optBorrow) optBorrow.classList.add('hidden');

             document.getElementById('statusSelect').value = 'all';
        }
    }



    function setPreset(preset) {
        const startEl = document.getElementById('start_date');
        const endEl = document.getElementById('end_date');
        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth();

        let start, end;

        if (preset === 'this_month') {
            start = new Date(year, month, 1);
            end = new Date(year, month + 1, 0);
        } else if (preset === 'last_month') {
            start = new Date(year, month - 1, 1);
            end = new Date(year, month, 0);
        } else if (preset === 'this_year') {
            start = new Date(year, 0, 1);
            end = new Date(year, 11, 31);
        }

        // Format to YYYY-MM-DD
        const formatDate = (d) => {
            let mo = d.getMonth() + 1;
            let da = d.getDate();
            return `${d.getFullYear()}-${mo < 10 ? '0'+mo : mo}-${da < 10 ? '0'+da : da}`;
        };

        if(start && end) {
            startEl.value = formatDate(start);
            endEl.value = formatDate(end);
            refreshPreview();
        }
    }

    function refreshPreview() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        const queryString = new URLSearchParams(formData).toString();
        
        const iframe = document.getElementById('pdf-frame');
        const loading = document.getElementById('loading-overlay');
        const pageInfo = document.getElementById('page-info');
        
        const orient = formData.get('orientation');
        pageInfo.innerText = orient === 'landscape' ? 'A4 Landscape' : 'A4 Portrait';
        iframe.style.width = orient === 'landscape' ? '297mm' : '210mm';
        iframe.style.minHeight = orient === 'landscape' ? '210mm' : '297mm';

        loading.classList.remove('hidden');
        
        iframe.src = `${pdfUrl}?${queryString}&t=${new Date().getTime()}`;

        // SAFETY: Timeout 8 detik jika stuck
        const loadTimeout = setTimeout(() => {
             loading.classList.add('hidden');
        }, 8000);

        iframe.onload = function() {
            clearTimeout(loadTimeout);
            loading.classList.add('hidden');
        };
        
        iframe.onerror = function() {
            clearTimeout(loadTimeout);
            loading.innerHTML = '<span class="text-red-500 font-bold">Gagal memuat preview. Silakan coba lagi.</span>';
        }
    }

    function downloadPdf() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        formData.append('download', '1');
        window.location.href = `${pdfUrl}?${new URLSearchParams(formData).toString()}`;
    }

    function downloadExcel(format = 'csv') {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        formData.append('format', format);
        const excelUrl = "{{ route('reports.excel') }}";
        window.location.href = `${excelUrl}?${new URLSearchParams(formData).toString()}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        toggleFilters();
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