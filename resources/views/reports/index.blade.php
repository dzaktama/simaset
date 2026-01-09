@extends('layouts.main')

@section('container')
<div class="h-[calc(100vh-140px)] flex flex-col md:flex-row gap-6">
    
    {{-- PANEL KIRI: KONTROL FILTER & OPSI --}}
    <div class="w-full md:w-1/3 flex flex-col">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 flex-1 flex flex-col overflow-hidden">
            
            {{-- Header Panel --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                    Konfigurasi Laporan
                </h2>
                <p class="text-xs text-gray-500 mt-1">Atur filter dan tampilan sebelum mencetak.</p>
            </div>

            {{-- Form Scrollable --}}
            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-5">
                <form id="reportForm">
                    
                    {{-- 1. FILTER DATA --}}
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Filter Data</label>
                        
                        {{-- Search --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci Pencarian</label>
                            <input type="text" id="search" name="search" placeholder="Nama, SN, atau Deskripsi..." 
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                   onchange="refreshPreview()"> </div>

                        {{-- Status & Sort --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="all">Semua Status</option>
                                    <option value="available">Available</option>
                                    <option value="deployed">Deployed</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="broken">Broken</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Urutkan</label>
                                <select id="sort" name="sort" onchange="refreshPreview()" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="newest">Terbaru</option>
                                    <option value="oldest">Terlama</option>
                                    <option value="stock_low">Stok Sedikit</option>
                                    <option value="stock_high">Stok Banyak</option>
                                    <option value="name_asc">Nama (A-Z)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- 2. TAMPILAN LAPORAN --}}
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Opsi Tampilan</label>
                        
                        {{-- Judul --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen</label>
                            <input type="text" name="custom_title" value="Laporan Aset IT - Vitech Asia" 
                                   onchange="refreshPreview()"
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>

                        {{-- Orientasi & Foto --}}
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Orientasi</label>
                                <select name="orientation" onchange="refreshPreview()" class="rounded border-gray-300 text-xs py-1">
                                    <option value="landscape">Landscape (Melebar)</option>
                                    <option value="portrait">Portrait (Tegak)</option>
                                </select>
                            </div>
                            <div class="flex items-center h-full pt-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_images" value="1" checked onchange="refreshPreview()" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Tampilkan Foto</span>
                                </label>
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Kaki (Admin)</label>
                            <textarea name="admin_notes" rows="2" onchange="refreshPreview()"
                                      placeholder="Tambahkan catatan untuk penerima laporan..." 
                                      class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer Actions --}}
            <div class="p-4 bg-gray-50 border-t border-gray-200 space-y-2">
                <button type="button" onclick="refreshPreview()" class="w-full flex justify-center items-center gap-2 bg-white border border-gray-300 text-gray-700 py-2 rounded-lg text-sm font-bold hover:bg-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Refresh Preview
                </button>
                <button type="button" onclick="printPDF()" class="w-full flex justify-center items-center gap-2 bg-indigo-600 text-white py-2.5 rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-md transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak / Download PDF
                </button>
            </div>
        </div>
    </div>

    {{-- PANEL KANAN: LIVE PREVIEW (IFRAME) --}}
    <div class="w-full md:w-2/3 h-full">
        <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 h-full flex flex-col overflow-hidden">
            <div class="px-4 py-2 bg-gray-900 border-b border-gray-700 flex justify-between items-center">
                <span class="text-xs font-mono text-gray-400">PDF Preview Mode</span>
                <span id="loading-badge" class="hidden px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-500/20 text-yellow-500 animate-pulse">MEMUAT...</span>
            </div>
            <div class="flex-1 relative bg-gray-500/10">
                <iframe id="pdf-frame" class="w-full h-full border-0" src=""></iframe>
                
                {{-- Placeholder saat loading pertama kali --}}
                <div id="placeholder" class="absolute inset-0 flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <p class="text-sm">Menyiapkan Pratinjau...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // URL dasar dari Route Laravel
    const baseUrl = "{{ route('report.assets') }}";

    function getQueryString() {
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        return new URLSearchParams(formData).toString();
    }

    function refreshPreview() {
        const iframe = document.getElementById('pdf-frame');
        const loading = document.getElementById('loading-badge');
        const placeholder = document.getElementById('placeholder');
        
        // Tampilkan loading
        loading.classList.remove('hidden');
        
        // Set URL iframe dengan parameter terbaru
        const finalUrl = `${baseUrl}?${getQueryString()}`;
        iframe.src = finalUrl;

        iframe.onload = function() {
            loading.classList.add('hidden');
            placeholder.classList.add('hidden');
        };
    }

    function printPDF() {
        // Buka URL yang sama di tab baru untuk trigger print native browser
        const finalUrl = `${baseUrl}?${getQueryString()}`;
        window.open(finalUrl, '_blank');
    }

    // Load preview saat halaman dibuka
    document.addEventListener('DOMContentLoaded', () => {
        refreshPreview();
    });
</script>
@endsection