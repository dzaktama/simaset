@extends('layouts.main')

@section('container')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="mb-8 text-center md:text-left">
        <h2 class="text-3xl font-bold text-gray-900">Generator Laporan & Audit</h2>
        <p class="mt-2 text-gray-600">Sesuaikan filter dan tampilan laporan sebelum dicetak ke PDF.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- PANEL KIRI: Statistik Singkat --}}
        <div class="md:col-span-1 space-y-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-500 text-xs uppercase mb-3">Ringkasan Data</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Aset</span>
                        <span class="font-bold text-gray-900">{{ $totalAssets }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-green-600">Available</span>
                        <span class="font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full text-xs">{{ $availableAssets }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-blue-600">Deployed</span>
                        <span class="font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full text-xs">{{ $deployedAssets }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400 italic">
                    *Data real-time saat ini
                </div>
            </div>
        </div>

        {{-- PANEL KANAN: Form Generator --}}
        <div class="md:col-span-2">
            <form action="{{ route('report.assets') }}" method="GET" target="_blank" class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="p-6 space-y-6">
                    
                    {{-- 1. Judul Laporan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Judul Laporan</label>
                        <input type="text" name="custom_title" value="Laporan Aset IT - Vitech Asia" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Judul ini akan muncul di kop surat PDF.</p>
                    </div>

                    {{-- 2. Filter & Opsi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Filter Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">Semua Aset</option>
                                <option value="available">Hanya Available</option>
                                <option value="deployed">Hanya Deployed (Dipinjam)</option>
                                <option value="maintenance">Maintenance / Rusak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Orientasi Kertas</label>
                            <select name="orientation" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="landscape">Landscape (Melebar)</option>
                                <option value="portrait">Portrait (Tegak)</option>
                            </select>
                        </div>
                    </div>

                    {{-- 3. Opsi Tampilan --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <span class="block text-xs font-bold text-gray-500 uppercase mb-2">Opsi Tampilan</span>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="show_images" id="show_images" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500 border-gray-300 h-4 w-4">
                            <label for="show_images" class="text-sm text-gray-700 select-none">Tampilkan Foto Aset (Thumbnail)</label>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 ml-7">Hilangkan centang jika ingin laporan lebih ringkas atau hemat tinta.</p>
                    </div>

                    {{-- 4. Catatan Tambahan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Catatan Admin <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        <textarea name="admin_notes" rows="3" placeholder="Contoh: Laporan ini dibuat untuk audit internal Q1 2026..." class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                    </div>
                </div>

                {{-- Footer Tombol --}}
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
                    <button type="button" onclick="window.history.back()" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold shadow-md transition transform hover:-translate-y-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection