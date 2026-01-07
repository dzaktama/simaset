@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-5xl">
    <div class="mb-8 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Input Aset Baru</h2>
        <a href="/assets" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Kembali</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        {{-- PENTING: enctype wajib ada buat upload foto --}}
        <form action="/assets" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                {{-- Kiri --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                        <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Serial Number</label>
                        <input type="text" name="serial_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="available">Available</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="broken">Broken</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Beli</label>
                        <input type="date" name="purchase_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>
                </div>

                {{-- Kanan --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan Kondisi Fisik</label>
                        <textarea name="condition_notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" placeholder="Contoh: Lecet di bagian siku kiri"></textarea>
                    </div>
                    
                    {{-- Upload 3 Foto --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Foto Utama</label>
                        <input type="file" name="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Foto Samping/Detail 1</label>
                            <input type="file" name="image2" class="mt-1 block w-full text-xs text-gray-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Foto Belakang/Detail 2</label>
                            <input type="file" name="image3" class="mt-1 block w-full text-xs text-gray-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection