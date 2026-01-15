@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-5xl px-4 py-8">
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Input Aset Baru</h2>
            <p class="text-sm text-gray-500">Registrasi aset inventaris baru. SN & QR Code digenerate otomatis setelah disimpan.</p>
        </div>
        <a href="{{ route('assets.index') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        {{-- Form Start --}}
        <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- KOLOM KIRI --}}
                <div class="space-y-5">
                    
                    {{-- Nama Barang --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5 @error('name') border-red-500 @enderror" placeholder="Contoh: MacBook Pro M3">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Serial Number (Readonly) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">
                            Serial Number <span class="text-xs text-gray-400 font-normal">(Auto-Generated)</span>
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-100 px-3 text-gray-500 sm:text-sm">#</span>
                            {{-- Kita tampilkan placeholder saja karena SN digenerate di backend berdasarkan Nama Aset --}}
                            <input type="text" value="Otomatis oleh Sistem" readonly class="block w-full flex-1 rounded-none rounded-r-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm cursor-not-allowed">
                        </div>
                    </div>

                    {{-- [MODIFIKASI] Kategori Dinamis --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5 @error('category') border-red-500 @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Quantity & Tanggal Beli --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Jumlah Stok <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5 @error('quantity') border-red-500 @enderror">
                            @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Tgl Beli <span class="text-red-500">*</span></label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5 @error('purchase_date') border-red-500 @enderror">
                            @error('purchase_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Status Awal --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Status Awal</label>
                        <select name="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5">
                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>

                    {{-- LOKASI (Dropdown Area & Rak) --}}
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-dashed">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Lorong / Area</label>
                            <select name="lorong" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5 bg-blue-50 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">- Pilih Area -</option>
                                @foreach(range('A', 'Z') as $char)
                                    <option value="Area {{ $char }}" {{ old('lorong') == "Area $char" ? 'selected' : '' }}>Area {{ $char }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nomor Rak</label>
                            <select name="rak" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5 bg-blue-50 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">- Pilih Rak -</option>
                                @for($i = 1; $i <= 50; $i++)
                                    @php $rakCode = 'R-' . str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $rakCode }}" {{ old('rak') == $rakCode ? 'selected' : '' }}>{{ $rakCode }}</option>
                                @endfor
                            </select>
                        </div>
                        <p class="col-span-2 text-xs text-gray-500">*Wajib diisi untuk pemetaan aset di gudang (Peta Lokasi).</p>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="space-y-5">
                    
                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5" placeholder="Spesifikasi teknis...">{{ old('description') }}</textarea>
                    </div>

                    {{-- Catatan Kondisi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Catatan Kondisi Awal</label>
                        <textarea name="condition_notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5" placeholder="Kondisi fisik saat barang diterima...">{{ old('condition_notes') }}</textarea>
                    </div>
                    
                    {{-- Upload Foto (Multi Image) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Foto Dokumentasi (Max 3)</label>
                        <div class="space-y-3 p-4 border rounded-lg bg-gray-50">
                            {{-- Foto Utama --}}
                            <div>
                                <label class="text-xs font-medium text-gray-500">Foto Utama (Wajib)</label>
                                <input type="file" name="image" class="block w-full text-sm mt-1 border border-gray-300 rounded p-1 bg-white @error('image') border-red-500 @enderror">
                                @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Foto 2 --}}
                            <div>
                                <label class="text-xs font-medium text-gray-500">Foto Samping / Detail 1</label>
                                <input type="file" name="image2" class="block w-full text-sm mt-1 border border-gray-300 rounded p-1 bg-white">
                            </div>
                            
                            {{-- Foto 3 --}}
                            <div>
                                <label class="text-xs font-medium text-gray-500">Foto Belakang / Detail 2</label>
                                <input type="file" name="image3" class="block w-full text-sm mt-1 border border-gray-300 rounded p-1 bg-white">
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Format: JPG, PNG, JPEG. Max 2MB per file.</p>
                    </div>
                </div>
            </div>

            {{-- Footer Tombol --}}
            <div class="mt-8 flex justify-end border-t pt-5">
                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    Simpan Aset Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection