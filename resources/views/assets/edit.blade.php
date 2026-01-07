@extends('layouts.main')

@section('container')
<div class="max-w-3xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Edit Data Aset</h2>
        <a href="/assets" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">&larr; Kembali ke List</a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-200">
        <form action="/assets/{{ $asset->id }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @method('PUT')
            @csrf
            
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                
                <div class="sm:col-span-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Perangkat</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $asset->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>

                <div class="sm:col-span-2">
                    <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 text-gray-500 shadow-sm sm:text-sm cursor-not-allowed" readonly title="Serial Number tidak boleh diubah sembarangan">
                </div>

                <div class="sm:col-span-3">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status Kondisi</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="available" {{ $asset->status == 'available' ? 'selected' : '' }}>Tersedia (Gudang)</option>
                        <option value="deployed" {{ $asset->status == 'deployed' ? 'selected' : '' }}>Digunakan (Deployed)</option>
                        <option value="maintenance" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>Sedang Perbaikan</option>
                        <option value="broken" {{ $asset->status == 'broken' ? 'selected' : '' }}>Rusak / Mati</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Penanggung Jawab (User)</label>
                    <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">-- Tidak Ada / Di Gudang --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $asset->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label for="purchase_date" class="block text-sm font-medium text-gray-700">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Foto Aset</label>
                    <div class="mt-2 flex items-center gap-4">
                        @if($asset->image)
                            <img src="{{ asset('storage/' . $asset->image) }}" alt="Foto Lama" class="h-20 w-20 object-cover rounded-md border border-gray-300">
                        @endif
                        <div class="flex-1">
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="image" name="image" type="file">
                            <p class="mt-1 text-xs text-gray-500">Upload file baru untuk mengganti foto lama (Max 2MB).</p>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi / Spesifikasi</label>
                    <div class="mt-1">
                        <textarea id="description" name="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $asset->description) }}</textarea>
                    </div>
                </div>

            </div>

            <div class="pt-5 border-t border-gray-200 flex justify-end gap-3">
                <a href="/assets" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Batal</a>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection