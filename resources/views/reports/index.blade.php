@extends('layouts.main')

@section('container')
<div class="h-[calc(100vh-140px)] flex flex-col md:flex-row gap-6">
    
    {{-- PANEL KIRI: FILTER --}}
    <div class="w-full md:w-1/3 flex flex-col">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 flex-1 flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                    Konfigurasi Cetak
                </h2>
                <p class="text-xs text-gray-500 mt-1">Sesuaikan tampilan sebelum mencetak.</p>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-5">
                <form id="reportForm">
                    {{-- 1. FILTER --}}
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Data Filter</label>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Aset</label>
                            <input type="text" name="search" placeholder="Cari nama, SN..." class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500" onchange="refreshPreview()"> 
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm">
                                    <option value="all">Semua</option>
                                    <option value="available">Available</option>
                                    <option value="deployed">Deployed</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="broken">Broken</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                                <select name="sort" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm">
                                    <option value="newest">Terbaru</option>
                                    <option value="stock_low">Stok Minimum</option>
                                    <option value="name_asc">Nama (A-Z)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- 2. TAMPILAN --}}
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Layout Cetak</label>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Laporan</label>
                            <input type="text" name="custom_title" value="Laporan Inventaris Aset IT" class="w-full rounded-lg border-gray-300 text-sm" onchange="refreshPreview()">
                        </div>

                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kertas</label>
                                <select name="orientation" onchange="refreshPreview()" class="rounded border-gray-300 text-xs py-1">
                                    <option value="portrait">A4 Portrait (Tegak)</option>
                                    <option value="landscape">A4 Landscape (Melebar)</option>
                                </select>
                            </div>
                            <div class="flex items-center pt-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_images" value="1" checked onchange="refreshPreview()" class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">Foto</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Kaki</label>
                            <textarea name="admin_notes" rows="2" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="p-4 bg-gray-50 border-t border-gray-200 space-y-2">
                {{-- [REVISI] Teks Tombol Diubah --}}
                <button type="button" onclick="triggerPrint()" class="w-full flex justify-center items-center gap-2 bg-indigo-600 text-white py-3 rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-md transition transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak Laporan PDF
                </button>
            </div>
        </div>
    </div>

    {{-- PANEL KANAN: PREVIEW (WYSIWYG) --}}
    <div class="w-full md:w-2/3 h-full">
        <div class="bg-gray-200 rounded-xl shadow-inner border border-gray-300 h-full flex flex-col overflow-hidden relative">
            
            {{-- Loading Indicator --}}
            <div id="loading-overlay" class="absolute inset-0 bg-white/80 z-20 flex items-center justify-center backdrop-blur-sm hidden">
                <div class="flex flex-col items-center">
                    <svg class="w-10 h-10 text-indigo-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="mt-2 text-xs font-bold text-gray-500">Memuat Preview...</span>
                </div>
            </div>

            {{-- IFRAME PREVIEW --}}
            <div class="flex-1 p-4 md:p-8 flex items-center justify-center overflow-auto bg-gray-500/10">
                <iframe id="pdf-frame" class="w-full h-full bg-white shadow-2xl rounded-sm" style="max-width: 210mm;" src=""></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    const baseUrl = "{{ route('report.pdf') }}";

    function refreshPreview() {
        const form = document.getElementById('reportForm');
        const queryString = new URLSearchParams(new FormData(form)).toString();
        const iframe = document.getElementById('pdf-frame');
        const loading = document.getElementById('loading-overlay');
        
        loading.classList.remove('hidden');
        iframe.src = `${baseUrl}?${queryString}`;

        iframe.onload = function() {
            loading.classList.add('hidden');
        };
    }

    function triggerPrint() {
        const iframe = document.getElementById('pdf-frame');
        if (iframe.contentWindow) {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } else {
            alert('Preview belum dimuat sepenuhnya.');
        }
    }

    document.addEventListener('DOMContentLoaded', refreshPreview);
</script>
@endsection