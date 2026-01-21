@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-6xl px-4 py-8 lg:py-12">
    {{-- Header Section --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Input Aset Baru</h2>
            <p class="mt-2 text-base text-gray-500 max-w-2xl">
                Tambahkan aset inventaris baru ke dalam sistem. 
                Serial Number & QR Code akan digenerate otomatis setelah data tersimpan.
            </p>
        </div>
        <a href="{{ route('assets.index') }}" class="group inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all duration-200">
            <svg class="mr-2 h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    {{-- Form Wrapper --}}
    <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- KOLOM UTAMA (KIRI/TENGAH) --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- CARD 1: INFORMASI DASAR --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                            Informasi Dasar
                        </h3>
                    </div>
                    
                    <div class="p-6 md:p-8 space-y-6">
                        {{-- Nama Barang & Status --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Barang / Aset <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base" placeholder="Contoh: MacBook Pro M3 Max 16 Inch" required>
                                @error('name') <p class="text-red-500 text-sm mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kategori --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kategori Aset <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="category" class="block w-full appearance-none rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-4 pr-10 text-base" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                                @error('category') <p class="text-red-500 text-sm mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- Status Awal --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Status Awal</label>
                                <div class="relative">
                                    <select name="status" class="block w-full appearance-none rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-4 pr-10 text-base">
                                        <option value="available" class="text-green-600 font-bold" {{ old('status') == 'available' ? 'selected' : '' }}>Available (Siap Pakai)</option>
                                        <option value="maintenance" class="text-yellow-600 font-bold" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance (Perbaikan)</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi & Spesifikasi</label>
                            <textarea name="description" rows="3" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base" placeholder="Tuliskan spesifikasi detail, kelengkapan, warna, dsb..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: INFORMASI KEUANGAN (COLLAPSIBLE PREMIUM) --}}
                <div x-data="{ expanded: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300" :class="{'ring-2 ring-indigo-500 ring-offset-2': expanded}">
                    <button type="button" @click="expanded = !expanded" class="w-full flex items-center justify-between px-6 py-5 bg-white hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 transition-colors" :class="{'bg-indigo-600 text-white': expanded}">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                            <div class="text-left">
                                <h3 class="text-lg font-bold text-gray-900">Informasi Keuangan</h3>
                                <p class="text-sm text-gray-500">Harga beli, masa manfaat, dan depresiasi (Opsional).</p>
                            </div>
                        </div>
                        <span class="rounded-full p-2 text-gray-400 hover:bg-gray-200 transition-colors" :class="{'rotate-180 bg-gray-100 text-indigo-600': expanded}">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </span>
                    </button>

                    <div x-show="expanded" x-collapse>
                        <div class="p-6 md:p-8 space-y-6 border-t border-gray-100 bg-gray-50/30">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Tanggal Beli --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Pembelian <span class="text-red-500">*</span></label>
                                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" required>
                                </div>
                                
                                {{-- Harga Beli --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga Per Unit (Rp)</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="purchase_price" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-12 pr-4" placeholder="0">
                                    </div>
                                </div>

                                {{-- Masa Pakai --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Masa Pakai (Tahun)
                                        <span class="ml-1 inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">Default: 4 Tahun</span>
                                    </label>
                                    <input type="number" name="useful_life_years" value="4" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" placeholder="Contoh: 4">
                                </div>

                                {{-- Nilai Residu --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nilai Residu (Rp)</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="residual_value" value="0" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-12 pr-4" placeholder="Estimasi harga jual akhir">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Nilai sisa aset setelah habis masa pakainya.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- KOLOM SIDEBAR (KANAN) --}}
            <div class="space-y-8">
                
                {{-- CARD 3: LOGITSIK (LOKASI & JUMLAH) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-5 pb-3 border-b border-gray-100 uppercase tracking-wider">Logistik & Permintaan</h3>
                    
                    <div class="space-y-5">
                        {{-- Jumlah Massal --}}
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                            <label class="block text-sm font-bold text-blue-900 mb-2">Jumlah Aset (Input Massal)</label>
                            <div class="flex items-center">
                                <button type="button" onclick="document.getElementById('qtyInput').stepDown()" class="p-3 bg-white rounded-l-lg border border-r-0 border-blue-200 text-blue-600 hover:bg-blue-50 transition">-</button>
                                <input id="qtyInput" type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" class="text-center w-full border-y border-x-0 border-blue-200 py-3 font-bold text-blue-900 focus:ring-0">
                                <button type="button" onclick="document.getElementById('qtyInput').stepUp()" class="p-3 bg-white rounded-r-lg border border-l-0 border-blue-200 text-blue-600 hover:bg-blue-50 transition">+</button>
                            </div>
                            <p class="text-xs text-blue-600 mt-2">
                                <strong>Tips:</strong> Masukkan > 1 untuk membuat banyak aset sekaligus dengan SN berurutan otomatis.
                            </p>
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Penyimpanan</label>
                            <div class="grid grid-cols-2 gap-3">
                                <select name="lorong" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                                    <option value="">Area...</option>
                                    @foreach(range('A', 'Z') as $char)
                                        <option value="Area {{ $char }}" {{ old('lorong') == "Area $char" ? 'selected' : '' }}>Area {{ $char }}</option>
                                    @endforeach
                                </select>
                                <select name="rak" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                                    <option value="">Rak...</option>
                                    @for($i = 1; $i <= 50; $i++)
                                        @php $rakCode = 'R-' . str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $rakCode }}" {{ old('rak') == $rakCode ? 'selected' : '' }}>{{ $rakCode }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 4: FOTO DOKUMENTASI --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-5 pb-3 border-b border-gray-100 uppercase tracking-wider">Foto Dokumentasi</h3>
                    
                    <div class="space-y-4">
                        {{-- Upload 1 --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Foto Utama</label>
                            <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors cursor-pointer border rounded-lg">
                        </div>
                        {{-- Upload 2 --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tampak Samping</label>
                            <input type="file" name="image2" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-colors cursor-pointer border rounded-lg">
                        </div>
                        {{-- Upload 3 --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tampak Belakang</label>
                            <input type="file" name="image3" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-colors cursor-pointer border rounded-lg">
                        </div>
                        <p class="text-xs text-gray-400 text-center pt-2">Max 2MB per file (JPG/PNG).</p>
                    </div>
                </div>
                
                {{-- TOMBOL SIMPAN (Action Bar) --}}
                <div class="sticky bottom-6">
                    <button type="submit" class="w-full flex items-center justify-center rounded-xl bg-indigo-600 px-8 py-4 text-base font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="mr-2 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Simpan Aset Baru
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-2">Pastikan data sudah benar sebelum menyimpan.</p>
                </div>

            </div>
        </div>
    </form>
</div>

{{-- AlpineJS for Interactions is already loaded in Layout --}}
@endsection