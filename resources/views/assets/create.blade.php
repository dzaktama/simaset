@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-5xl">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Input Aset Baru</h2>
            <p class="text-sm text-gray-500">Registrasi aset inventaris baru ke dalam sistem. QR Code akan digenerate otomatis setelah aset disimpan.</p>
        </div>
        <a href="/assets" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        {{-- Enctype wajib ada untuk upload file --}}
        <form action="/assets" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Kiri --}}
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5" placeholder="Contoh: MacBook Pro M3">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">
                            Serial Number <span class="text-xs text-gray-400 font-normal">(Auto-Generated)</span>
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-100 px-3 text-gray-500 sm:text-sm">
                                #
                            </span>
                            <input type="text" name="serial_number" 
                                   value="{{ $suggestedSN }}" 
                                   readonly 
                                   class="block w-full flex-1 rounded-none rounded-r-md border-gray-300 bg-gray-50 text-gray-500 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm cursor-not-allowed">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Nomor seri digenerate otomatis oleh sistem.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Jumlah Stok (Quantity)</label>
                        <input type="number" name="quantity" value="1" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="1">
                        <p class="mt-1 text-xs text-gray-500">Biarkan 1 untuk aset unik (Laptop/PC). Isi lebih dari 1 untuk barang habis pakai (Mouse/Kabel).</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Status Awal</label>
                            <select name="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5">
                                <option value="available">Available</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Tgl Beli</label>
                            <input type="date" name="purchase_date" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5">
                        </div>
                    </div>
                </div>

                {{-- Kanan --}}
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5" placeholder="Spesifikasi teknis..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Catatan Kondisi Awal</label>
                        <textarea name="condition_notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5" placeholder="Kondisi fisik saat barang diterima..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Foto Dokumentasi (Max 3)</label>
                        <div class="space-y-3 p-4 border rounded-lg bg-gray-50">
                            <div>
                                <label class="text-xs font-medium text-gray-500">Foto Utama</label>
                                <input type="file" name="image" class="block w-full text-sm mt-1 border border-gray-300 rounded p-1 bg-white">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Foto Samping / Detail 1</label>
                                <input type="file" name="image2" class="block w-full text-sm mt-1 border border-gray-300 rounded p-1 bg-white">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Foto Belakang / Detail 2</label>
                                <input type="file" name="image3" class="block w-full text-sm mt-1 border border-gray-300 rounded p-1 bg-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end border-t pt-5">
                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    Simpan Aset Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection