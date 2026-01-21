@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-6xl px-4 py-8 lg:py-12">
    {{-- Header Section --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Edit Aset</h2>
            <p class="mt-2 text-base text-gray-500 max-w-2xl">
                Perbarui informasi dan detail aset inventaris.
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
    <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
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
                        {{-- Nama Barang & Serial --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Barang <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $asset->name) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base" required>
                            </div>

                            {{-- Serial Number --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Serial Number</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <span class="text-gray-500 font-bold">#</span>
                                    </div>
                                    <input type="text" value="{{ $asset->serial_number }}" class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-500 shadow-sm py-3 pl-10 pr-4 cursor-not-allowed" readonly>
                                </div>
                            </div>

                            {{-- Kategori --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="category" class="block w-full appearance-none rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-4 pr-10 text-base" required>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" {{ old('category', $asset->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Status Saat Ini</label>
                                <div class="relative">
                                    <select name="status" id="status" onchange="toggleUserField()" class="block w-full appearance-none rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-4 pr-10 text-base">
                                        <option value="available" class="text-green-600 font-bold" {{ $asset->status == 'available' ? 'selected' : '' }}>Available (Tersedia)</option>
                                        <option value="deployed" class="text-blue-600 font-bold" {{ $asset->status == 'deployed' ? 'selected' : '' }}>Deployed (Dipakai)</option>
                                        <option value="maintenance" class="text-yellow-600 font-bold" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>Maintenance (Perbaikan)</option>
                                        <option value="broken" class="text-red-600 font-bold" {{ $asset->status == 'broken' ? 'selected' : '' }}>Broken (Rusak)</option>
                                        <option value="lost" class="text-gray-600 font-bold" {{ $asset->status == 'lost' ? 'selected' : '' }}>Lost (Hilang)</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Peminjam (Logic Hidden) --}}
                        <div id="user_field" class="hidden bg-blue-50/50 rounded-xl p-5 border border-blue-100">
                            <h4 class="text-sm font-bold text-blue-900 mb-3 uppercase tracking-wide">Data Peminjam</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-blue-900 mb-1">Nama Peminjam</label>
                                    <div class="relative">
                                        <select name="user_id" class="block w-full appearance-none rounded-lg border-blue-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 pl-4 pr-10">
                                            <option value="">-- Pilih User --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $asset->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-blue-500">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-blue-800 mb-1">Tanggal Pinjam</label>
                                        <input type="date" name="assigned_date" value="{{ old('assigned_date', $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('Y-m-d') : '') }}" class="block w-full rounded-lg border-blue-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-blue-800 mb-1">Rencana Kembali</label>
                                        <input type="date" name="return_date" value="{{ old('return_date', $asset->return_date ? \Carbon\Carbon::parse($asset->return_date)->format('Y-m-d') : '') }}" class="block w-full rounded-lg border-blue-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi & Spesifikasi</label>
                            <textarea name="description" rows="3" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base">{{ old('description', $asset->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: INFORMASI KEUANGAN --}}
                <div x-data="{ expanded: true }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300" :class="{'ring-2 ring-indigo-500 ring-offset-2': expanded}">
                    <button type="button" @click="expanded = !expanded" class="w-full flex items-center justify-between px-6 py-5 bg-white hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 transition-colors" :class="{'bg-indigo-600 text-white': expanded}">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                            <div class="text-left">
                                <h3 class="text-lg font-bold text-gray-900">Informasi Keuangan</h3>
                                <p class="text-sm text-gray-500">Update harga, umur ekonomis, dan nilai residu.</p>
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
                                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : '') }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" required>
                                </div>
                                
                                {{-- Harga Beli --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga Per Unit (Rp)</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="purchase_price" value="{{ old('purchase_price', $asset->purchase_price) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-12 pr-4" placeholder="0">
                                    </div>
                                </div>

                                {{-- Masa Pakai --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Masa Pakai (Tahun)</label>
                                    <input type="number" name="useful_life_years" value="{{ old('useful_life_years', $asset->useful_life_years) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" placeholder="Contoh: 4">
                                </div>

                                {{-- Nilai Residu --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nilai Residu (Rp)</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="residual_value" value="{{ old('residual_value', $asset->residual_value) }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-12 pr-4" placeholder="Estimasi harga jual akhir">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- KOLOM SIDEBAR (KANAN) --}}
            <div class="space-y-8">
                
                {{-- CARD 3: LOGITSIK --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-5 pb-3 border-b border-gray-100 uppercase tracking-wider">Logistik</h3>
                    
                    <div class="space-y-5">
                        {{-- Jumlah Stok --}}
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                            <label class="block text-sm font-bold text-blue-900 mb-2">Jumlah Stok Sistem</label>
                            <input type="number" name="quantity" value="{{ old('quantity', $asset->quantity) }}" min="0" class="text-center w-full rounded-lg border-blue-200 py-2.5 font-bold text-blue-900 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 mt-2">Ubah manual hanya jika stok fisik berubah.</p>
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Penyimpanan</label>
                            <div class="grid grid-cols-2 gap-3">
                                <select name="lorong" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                                    @foreach(range('A', 'Z') as $char)
                                        <option value="Area {{ $char }}" {{ old('lorong', $asset->lorong) == "Area $char" ? 'selected' : '' }}>Area {{ $char }}</option>
                                    @endforeach
                                </select>
                                <select name="rak" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
                                    @for($i = 1; $i <= 50; $i++)
                                        @php $rakCode = 'R-' . str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $rakCode }}" {{ old('rak', $asset->rak) == $rakCode ? 'selected' : '' }}>{{ $rakCode }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 4: FOTO DOKUMENTASI --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-5 pb-3 border-b border-gray-100 uppercase tracking-wider">Foto Dokumentasi</h3>
                    
                    <div class="space-y-6">
                        {{-- Upload 1 --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase">Foto Utama</label>
                            @if($asset->image)
                                <img src="{{ asset('storage/' . $asset->image) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200 mb-2">
                            @endif
                            <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors cursor-pointer border rounded-lg">
                        </div>

                        {{-- Upload 2 --}}
                         <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase">Tampak Samping</label>
                            @if($asset->image2)
                                <img src="{{ asset('storage/' . $asset->image2) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200 mb-2">
                            @endif
                            <input type="file" name="image2" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-colors cursor-pointer border rounded-lg">
                        </div>

                        {{-- Upload 3 --}}
                         <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase">Tampak Belakang</label>
                            @if($asset->image3)
                                <img src="{{ asset('storage/' . $asset->image3) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200 mb-2">
                            @endif
                            <input type="file" name="image3" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-colors cursor-pointer border rounded-lg">
                        </div>
                    </div>
                </div>
                
                {{-- TOMBOL ACTION --}}
                <div class="sticky bottom-6 space-y-3">
                    <button type="submit" class="w-full flex items-center justify-center rounded-xl bg-indigo-600 px-8 py-4 text-base font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="mr-2 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('assets.index') }}" class="w-full flex items-center justify-center rounded-xl bg-white px-8 py-3 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    function toggleUserField() {
        const status = document.getElementById('status').value;
        const userField = document.getElementById('user_field');
        if (status === 'deployed') {
            userField.classList.remove('hidden');
        } else {
            userField.classList.add('hidden');
        }
    }
    // Run on create (in case editing a deployed asset)
    document.addEventListener('DOMContentLoaded', toggleUserField);
</script>
@endsection