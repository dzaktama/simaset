@extends('layouts.main')

@section('container')
<div class="max-w-3xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Input Aset Baru</h2>
        <a href="/assets" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">&larr; Kembali ke List</a>
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-200">
        <form action="/assets" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                
                <div class="sm:col-span-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Perangkat</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: MacBook Pro M2 2023" required>
                </div>

                <div class="sm:col-span-2">
                    <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
                    <input type="text" name="serial_number" id="serial_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="SN-12345" required>
                </div>

                <div class="sm:col-span-3">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status Kondisi</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="available">Tersedia (Gudang)</option>
                        <option value="deployed">Digunakan (Deployed)</option>
                        <option value="maintenance">Sedang Perbaikan</option>
                        <option value="broken">Rusak / Mati</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Penanggung Jawab (User)</label>
                    <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">-- Tidak Ada / Di Gudang --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika barang ada di gudang.</p>
                </div>

                <div class="sm:col-span-3">
                    <label for="purchase_date" class="block text-sm font-medium text-gray-700">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" id="purchase_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Foto Aset</label>
                    <div class="mt-1 flex justify-center rounded-md border-2 border-dashed border-gray-300 px-6 pt-5 pb-6">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="image" class="relative cursor-pointer rounded-md bg-white font-medium text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500">
                                    <span>Upload a file</span>
                                    <input id="image" name="image" type="file" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi / Spesifikasi</label>
                    <div class="mt-1">
                        <textarea id="description" name="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="RAM 16GB, SSD 512GB, Kondisi lecet pemakaian..."></textarea>
                    </div>
                </div>

            </div>

            <div class="pt-5 border-t border-gray-200">
                <div class="flex justify-end">
                    <button type="button" onclick="history.back()" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Batal</button>
                    <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Simpan Aset</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection