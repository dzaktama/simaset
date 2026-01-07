@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-5xl">
    <div class="mb-8 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Edit Aset: {{ $asset->name }}</h2>
        <a href="/assets" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Batal</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <form action="/assets/{{ $asset->id }}" method="POST" enctype="multipart/form-data" class="p-8">
            @method('put')
            @csrf
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Serial Number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="available" {{ $asset->status == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="deployed" {{ $asset->status == 'deployed' ? 'selected' : '' }}>Deployed (Dipakai)</option>
                            <option value="maintenance" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="broken" {{ $asset->status == 'broken' ? 'selected' : '' }}>Broken</option>
                        </select>
                    </div>
                    
                    {{-- Dropdown Pemegang Aset (Manual Override oleh Admin) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pemegang Saat Ini (Holder)</label>
                        <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">-- Tidak Ada (Gudang) --</option>
                            @foreach($users as $usr) {{-- Pake variabel $users dari controller --}}
                                <option value="{{ $usr->id }}" {{ $asset->user_id == $usr->id ? 'selected' : '' }}>
                                    {{ $usr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">{{ old('description', $asset->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan Kondisi</label>
                        <textarea name="condition_notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">{{ old('condition_notes', $asset->condition_notes) }}</textarea>
                    </div>

                    {{-- Preview Gambar --}}
                    <div class="grid grid-cols-3 gap-2">
                        @if($asset->image) <img src="{{ asset('storage/' . $asset->image) }}" class="h-16 w-16 rounded object-cover border"> @endif
                        @if($asset->image2) <img src="{{ asset('storage/' . $asset->image2) }}" class="h-16 w-16 rounded object-cover border"> @endif
                        @if($asset->image3) <img src="{{ asset('storage/' . $asset->image3) }}" class="h-16 w-16 rounded object-cover border"> @endif
                    </div>

                    <div class="grid grid-cols-1 gap-2">
                        <input type="file" name="image" class="text-xs text-gray-500">
                        <input type="file" name="image2" class="text-xs text-gray-500">
                        <input type="file" name="image3" class="text-xs text-gray-500">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection