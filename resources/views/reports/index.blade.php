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
                <p class="text-xs text-gray-500 mt-1">Atur filter & tampilan sebelum download.</p>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-5">
                <form id="reportForm">
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Filter Data</label>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Aset</label>
                            <input type="text" name="search" placeholder="Nama, SN, Kategori..." class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 shadow-sm" onchange="refreshPreview()"> 
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                                    <option value="all">Semua</option>
                                    <option value="available">Available</option>
                                    <option value="deployed">Deployed</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="broken">Broken</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                                <select name="sort" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                                    <option value="latest">Terbaru</option>
                                    <option value="name_asc">Nama (A-Z)</option>
                                    <option value="stock_low">Stok Sedikit</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 my-4">

                    <div class="space-y-3">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tampilan PDF</label>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Laporan</label>
                            <input type="text" name="custom_title" value="Laporan Inventaris Aset IT" class="w-full rounded-lg border-gray-300 text-sm shadow-sm" onchange="refreshPreview()">
                        </div>
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Kertas</label>
                                <select name="orientation" onchange="refreshPreview()" class="rounded border-gray-300 text-xs py-1 shadow-sm">
                                    <option value="portrait">A4 Portrait</option>
                                    <option value="landscape">A4 Landscape</option>
                                </select>
                            </div>
                            <div class="flex items-center pt-3">
                                <label class="inline-flex items-center cursor-pointer select-none">
                                    <input type="checkbox" name="show_images" value="1" checked onchange="refreshPreview()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                    <span class="ml-2 text-sm text-gray-700 font-medium">Foto Aset</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Kaki (Admin)</label>
                            <textarea name="admin_notes" rows="2" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm" placeholder="Contoh: Disetujui oleh Manager IT..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-200 space-y-2">
                {{-- TOMBOL PRINT LANGSUNG --}}
                <button type="button" onclick="triggerPrint()" class="w-full flex justify-center items-center gap-2 bg-indigo-600 text-white py-3 rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-md transition transform hover:-translate-y-0.5 active:scale-95">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print Laporan
                </button>
                
                {{-- TOMBOL DOWNLOAD PDF --}}
                <button type="button" onclick="triggerDownload()" class="w-full flex justify-center items-center gap-2 bg-green-600 text-white py-3 rounded-lg text-sm font-bold hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5 active:scale-95">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Download PDF
                </button>
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
    // Pastikan route ini benar (pakai 's')
    const pdfUrl = "{{ route('reports.pdf') }}"; 

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
        
        // Load PDF Stream ke Iframe agar preview = hasil print
        iframe.src = `${pdfUrl}?${queryString}&stream=1`;

        iframe.onload = function() {
            loading.classList.add('hidden');
        };
    }

    function triggerPrint() {
        const iframe = document.getElementById('pdf-frame');
        if (iframe.contentWindow && iframe.src !== 'about:blank') {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } else {
            alert('Tunggu preview selesai dimuat.');
        }
    }

    function triggerDownload() {
        const form = document.getElementById('reportForm');
        const queryString = new URLSearchParams(new FormData(form)).toString();
        
        // Buat Nama File dengan Timestamp Hari Ini
        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const hh = String(now.getHours()).padStart(2, '0');
        const min = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        
        const filename = `Laporan_Aset_${yyyy}-${mm}-${dd}_${hh}-${min}-${ss}.pdf`;

        // URL Download (Langsung ke route PDF)
        const downloadUrl = `${pdfUrl}?${queryString}&download=1`;
        
        // Trigger Download
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.setAttribute('download', filename); // Paksa nama file di browser
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    document.addEventListener('DOMContentLoaded', refreshPreview);
</script>

<style>
    .custom-scrollbar-dark::-webkit-scrollbar { width: 10px; height: 10px; }
    .custom-scrollbar-dark::-webkit-scrollbar-track { background: #e5e7eb; }
    .custom-scrollbar-dark::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 5px; }
    .custom-scrollbar-dark::-webkit-scrollbar-thumb:hover { background: #6b7280; }
</style>
@endsection