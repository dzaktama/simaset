@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-5xl">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Data Aset</h2>
            <p class="text-sm text-gray-500">Perbarui informasi dan kondisi fisik aset.</p>
        </div>
        <a href="/assets" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition">
            &larr; Batal
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <form action="/assets/{{ $asset->id }}" method="POST" enctype="multipart/form-data" class="p-8">
            @method('put')
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Kiri: Data Utama --}}
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Nama Barang</label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Serial Number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed border p-2.5" readonly>
                        <p class="text-xs text-gray-400 mt-1">*Serial number permanen.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5">
                                <option value="available" {{ $asset->status == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="deployed" {{ $asset->status == 'deployed' ? 'selected' : '' }}>Deployed</option>
                                <option value="maintenance" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="broken" {{ $asset->status == 'broken' ? 'selected' : '' }}>Broken</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Tgl Pembelian</label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5">
                        </div>
                    </div>
                    
                    {{-- Override Holder --}}
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <label class="block text-sm font-bold text-yellow-800">Pemegang Aset (Holder Override)</label>
                        <select name="user_id" class="mt-1 block w-full rounded-lg border-yellow-300 shadow-sm focus:border-yellow-500 border p-2.5">
                            <option value="">-- Tidak Ada (Di Gudang) --</option>
                            @foreach($users as $usr)
                                <option value="{{ $usr->id }}" {{ $asset->user_id == $usr->id ? 'selected' : '' }}>
                                    {{ $usr->name }} ({{ ucfirst($usr->role) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-yellow-600 mt-1">Mengubah ini akan memindahkan kepemilikan aset secara paksa.</p>
                    </div>
                </div>

                {{-- Kanan: Detail & Foto --}}
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5">{{ old('description', $asset->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Catatan Kondisi Fisik</label>
                        <textarea name="condition_notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5" placeholder="Contoh: Lecet halus di sudut kiri">{{ old('condition_notes', $asset->condition_notes) }}</textarea>
                    </div>

                    {{-- Manajemen 3 Foto --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Update Foto Aset</label>
                        
                        <div class="flex items-center gap-4 border p-3 rounded-lg bg-gray-50 mb-3">
                            <div class="flex-shrink-0">
                                @if($asset->image)
                                    <img src="{{ asset('storage/' . $asset->image) }}" class="h-16 w-16 rounded object-cover border">
                                @else
                                    <div class="h-16 w-16 rounded bg-gray-200 flex items-center justify-center text-xs text-gray-500">No Img</div>
                                @endif
                            </div>
                            <div class="w-full">
                                <label class="text-xs font-medium text-gray-500">Foto Utama</label>
                                <input type="file" name="image" class="block w-full text-sm mt-1 text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 border p-3 rounded-lg bg-gray-50 mb-3">
                            <div class="flex-shrink-0">
                                @if($asset->image2)
                                    <img src="{{ asset('storage/' . $asset->image2) }}" class="h-16 w-16 rounded object-cover border">
                                @else
                                    <div class="h-16 w-16 rounded bg-gray-200 flex items-center justify-center text-xs text-gray-500">No Img</div>
                                @endif
                            </div>
                            <div class="w-full">
                                <label class="text-xs font-medium text-gray-500">Foto Samping/Detail 1</label>
                                <input type="file" name="image2" class="block w-full text-sm mt-1 text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                            </div>
                        </div>

                        <div class="flex items-center gap-4 border p-3 rounded-lg bg-gray-50">
                            <div class="flex-shrink-0">
                                @if($asset->image3)
                                    <img src="{{ asset('storage/' . $asset->image3) }}" class="h-16 w-16 rounded object-cover border">
                                @else
                                    <div class="h-16 w-16 rounded bg-gray-200 flex items-center justify-center text-xs text-gray-500">No Img</div>
                                @endif
                            </div>
                            <div class="w-full">
                                <label class="text-xs font-medium text-gray-500">Foto Belakang/Detail 2</label>
                                <input type="file" name="image3" class="block w-full text-sm mt-1 text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end border-t pt-5">
                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection