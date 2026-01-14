@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-5xl px-4 py-8">
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Data Aset</h2>
            <p class="text-sm text-gray-500">Perbarui informasi, lokasi, atau kondisi aset.</p>
        </div>
        <a href="{{ route('assets.index') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition">
            &larr; Batal
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        {{-- Form Start --}}
        <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @method('PUT')
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- KOLOM KIRI --}}
                <div class="space-y-5">
                    
                    {{-- Nama Barang --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5 @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Serial Number --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Serial Number</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-100 px-3 text-gray-500 sm:text-sm">#</span>
                            <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="block w-full flex-1 rounded-none rounded-r-md border-gray-300 bg-gray-50 text-gray-900 sm:text-sm border p-2.5" readonly>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Serial number tidak dapat diubah sembarangan.</p>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5 @error('category') border-red-500 @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @php $cats = ['Laptop', 'Monitor', 'PC', 'Printer', 'Proyektor', 'Aksesoris', 'Furniture', 'Lainnya']; @endphp
                            @foreach($cats as $c)
                                <option value="{{ $c }}" {{ old('category', $asset->category) == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Quantity & Tanggal Beli --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Jumlah Stok <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity', $asset->quantity) }}" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5 @error('quantity') border-red-500 @enderror">
                            @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Tgl Beli <span class="text-red-500">*</span></label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date', optional($asset->purchase_date)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5 @error('purchase_date') border-red-500 @enderror">
                            @error('purchase_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Status Saat Ini --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Status Saat Ini</label>
                        <select name="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5">
                            <option value="available" {{ old('status', $asset->status) == 'available' ? 'selected' : '' }}>Available (Tersedia)</option>
                            <option value="deployed" {{ old('status', $asset->status) == 'deployed' ? 'selected' : '' }}>Deployed (Dipinjam)</option>
                            <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Maintenance (Perbaikan)</option>
                            <option value="broken" {{ old('status', $asset->status) == 'broken' ? 'selected' : '' }}>Broken (Rusak)</option>
                        </select>
                    </div>

                    {{-- LOKASI (PERBAIKAN: Menggunakan Dropdown) --}}
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-dashed mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Lorong / Area</label>
                            <select name="lorong" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5 bg-blue-50 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">- Pilih Area -</option>
                                @foreach(range('A', 'Z') as $char)
                                    @php $val = "Area $char"; @endphp
                                    <option value="{{ $val }}" {{ old('lorong', $asset->lorong) == $val ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nomor Rak</label>
                            <select name="rak" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5 bg-blue-50 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">- Pilih Rak -</option>
                                @for($i = 1; $i <= 50; $i++)
                                    @php $rakCode = 'R-' . str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $rakCode }}" {{ old('rak', $asset->rak) == $rakCode ? 'selected' : '' }}>{{ $rakCode }}</option>
                                @endfor
                            </select>
                        </div>
                        <p class="col-span-2 text-xs text-gray-500">*Lokasi ini akan muncul di Peta Aset.</p>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="space-y-5">
                    
                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5">{{ old('description', $asset->description) }}</textarea>
                    </div>

                    {{-- Catatan Kondisi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Catatan Kondisi</label>
                        <textarea name="condition_notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5">{{ old('condition_notes', $asset->condition_notes) }}</textarea>
                    </div>
                    
                    {{-- Upload Foto (Multi Image Update) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Update Foto Dokumentasi</label>
                        <div class="space-y-4 p-4 border rounded-lg bg-gray-50">
                            
                            {{-- Foto 1 --}}
                            <div>
                                <label class="text-xs font-medium text-gray-500">Foto Utama (Ganti?)</label>
                                <div class="flex items-center gap-4 mt-1">
                                    @if($asset->image)
                                        <img src="{{ asset('storage/'.$asset->image) }}" class="h-12 w-12 rounded object-cover border">
                                    @endif
                                    <input type="file" name="image" class="block w-full text-sm border border-gray-300 rounded p-1 bg-white">
                                </div>
                            </div>
                            
                            {{-- Foto 2 --}}
                            <div>
                                <label class="text-xs font-medium text-gray-500">Foto Samping (Ganti?)</label>
                                <div class="flex items-center gap-4 mt-1">
                                    @if($asset->image2)
                                        <img src="{{ asset('storage/'.$asset->image2) }}" class="h-12 w-12 rounded object-cover border">
                                    @endif
                                    <input type="file" name="image2" class="block w-full text-sm border border-gray-300 rounded p-1 bg-white">
                                </div>
                            </div>
                            
                            {{-- Foto 3 --}}
                            <div>
                                <label class="text-xs font-medium text-gray-500">Foto Belakang (Ganti?)</label>
                                <div class="flex items-center gap-4 mt-1">
                                    @if($asset->image3)
                                        <img src="{{ asset('storage/'.$asset->image3) }}" class="h-12 w-12 rounded object-cover border">
                                    @endif
                                    <input type="file" name="image3" class="block w-full text-sm border border-gray-300 rounded p-1 bg-white">
                                </div>
                            </div>

                        </div>
                        <p class="mt-2 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah foto.</p>
                    </div>
                </div>
            </div>

            {{-- Footer Tombol --}}
            <div class="mt-8 flex justify-end border-t pt-5 gap-3">
                <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Batal</a>
                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection