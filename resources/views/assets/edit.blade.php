@extends('layouts.main')

@section('container')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 bg-gray-50 min-h-screen">
    {{-- Header Section --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Aset</h2>
            <p class="mt-1 text-sm text-gray-500">Perbarui informasi, lokasi, dan dokumentasi aset.</p>
        </div>
        <a href="{{ route('assets.index') }}" class="group inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all duration-200">
            <svg class="w-4 h-4 mr-2 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>
    </div>

    {{-- Form Wrapper --}}
    <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data" 
          x-data="{
              status: '{{ old('status', $asset->status) }}',
              previews: { 
                  img1: '{{ $asset->image ? asset('storage/' . $asset->image) : null }}', 
                  img2: '{{ $asset->image2 ? asset('storage/' . $asset->image2) : null }}', 
                  img3: '{{ $asset->image3 ? asset('storage/' . $asset->image3) : null }}' 
              },
              handleFile(e, key) {
                  const file = e.target.files[0];
                  if(file) {
                      const reader = new FileReader();
                      reader.onload = (e) => this.previews[key] = e.target.result;
                      reader.readAsDataURL(file);
                  }
              },
              dragover: false,
              handleDrop(e) {
                  this.dragover = false;
                  const files = e.dataTransfer.files;
                  if (files.length > 3) {
                      alert('Maksimal 3 foto sekaligus!');
                      return;
                  }
                  
                  const inputs = ['image', 'image2', 'image3'];
                  const keys = ['img1', 'img2', 'img3'];

                  for (let i = 0; i < files.length; i++) {
                      if (i >= 3) break;
                      
                      const file = files[i];
                      const inputId = inputs[i] + 'Input';
                      const key = keys[i];

                      // Update Input File
                      const dataTransfer = new DataTransfer();
                      dataTransfer.items.add(file);
                      document.getElementById(inputId).files = dataTransfer.files;

                      // Update Preview
                      const reader = new FileReader();
                      reader.onload = (e) => this.previews[key] = e.target.result;
                      reader.readAsDataURL(file);
                  }
              },
              removeFile(key, inputId) {
                  this.previews[key] = null;
                  const input = document.getElementById(inputId);
                  if (input) input.value = '';
              }
          }">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            {{-- KOLOM UTAMA (KIRI/TENGAH) --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- CARD 1: INFORMASI DASAR --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3 bg-indigo-50 border-b border-indigo-100 flex items-center">
                        <div class="bg-indigo-100 p-1.5 rounded-md mr-3 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-base font-bold text-indigo-900">Informasi Dasar</h3>
                    </div>
                    
                    <div class="p-5 space-y-5">
                        {{-- Nama Barang --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2 group">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">Nama Barang / Aset <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $asset->name) }}" 
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 font-medium" 
                                    required>
                                @error('name') <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            {{-- Serial Number (Readonly) --}}
                            <div class="md:col-span-2 group">
                                <label class="block text-sm font-bold text-gray-500 mb-1.5">Serial Number (Auto-Generated)</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-400 font-bold text-sm">#</span>
                                    </div>
                                    <input type="text" name="serial_number" value="{{ $asset->serial_number }}" class="w-full rounded-lg border border-gray-200 bg-gray-100 pl-8 pr-3 py-2.5 text-sm text-gray-500 font-medium cursor-not-allowed" readonly>
                                </div>
                            </div>

                            {{-- Kategori --}}
                            <div class="group">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">Kategori Aset <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="category" required class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 font-medium focus:border-indigo-600 focus:bg-white focus:ring-0 appearance-none cursor-pointer transition-all">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" {{ old('category', $asset->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                                @error('category') <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            {{-- Status Awal --}}
                            <div class="group">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">Status Saat Ini</label>
                                <div class="relative">
                                    <select name="status" x-model="status" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 font-medium focus:border-indigo-600 focus:bg-white focus:ring-0 appearance-none cursor-pointer transition-all">
                                        <option value="available" class="text-green-600 font-bold">Available (Siap Pakai)</option>
                                        <option value="deployed" class="text-blue-600 font-bold">Deployed (Dipakai)</option>
                                        <option value="maintenance" class="text-yellow-600 font-bold">Maintenance (Perbaikan)</option>
                                        <option value="broken" class="text-red-600 font-bold">Broken (Rusak)</option>
                                        <option value="lost" class="text-gray-600 font-bold">Lost (Hilang)</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                         {{-- Data Peminjam (Conditional) --}}
                         <div x-show="status === 'deployed'" x-transition class="bg-blue-50 rounded-xl p-5 border border-blue-100">
                            <h4 class="text-xs font-extrabold text-blue-900 mb-3 uppercase tracking-wider flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Data Peminjam
                            </h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-bold text-blue-900 mb-1">Nama Peminjam</label>
                                    <div class="relative">
                                        <select name="user_id" class="w-full rounded-lg border border-blue-200 bg-white px-3 py-2.5 text-sm text-blue-900 font-medium focus:border-blue-600 focus:ring-0 cursor-pointer">
                                            <option value="">-- Pilih User --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $asset->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-blue-800 mb-1">Tanggal Pinjam</label>
                                        <input type="date" name="assigned_date" value="{{ old('assigned_date', $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-medium text-blue-900 focus:border-blue-600 focus:ring-0">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-blue-800 mb-1">Rencana Kembali</label>
                                        <input type="date" name="return_date" value="{{ old('return_date', $asset->return_date ? \Carbon\Carbon::parse($asset->return_date)->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-medium text-blue-900 focus:border-blue-600 focus:ring-0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="group">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">Deskripsi & Spesifikasi</label>
                            <textarea name="description" rows="3" 
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 font-medium">{{ old('description', $asset->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: FOTO DOKUMENTASI (DIPINDAHKAN KE SINI) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                     x-on:dragover.prevent="dragover = true"
                     x-on:dragleave.prevent="dragover = false"
                     x-on:drop.prevent="handleDrop($event)"
                     :class="{'ring-2 ring-indigo-500 bg-indigo-50': dragover}">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                        <h3 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">Foto Dokumentasi</h3>
                    </div>
                    
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach(['image' => 'Foto Utama', 'image2' => 'Tampak Samping', 'image3' => 'Tampak Belakang'] as $key => $label)
                            <div class="relative group">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">{{ $label }}</label>
                                
                                {{-- Preview Area --}}
                                <div class="mb-2 w-full h-32 rounded-lg border border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center overflow-hidden relative cursor-pointer hover:bg-gray-100 hover:border-indigo-400 transition-all"
                                     @click="document.getElementById('{{ $key }}Input').click()">
                                    
                                    <template x-if="!previews.img{{ $loop->iteration }}">
                                        <div class="text-center p-3">
                                            <svg class="h-6 w-6 text-gray-400 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            <span class="text-[10px] text-gray-500 font-bold">Klik Upload</span>
                                        </div>
                                    </template>
                                    
                                    <template x-if="previews.img{{ $loop->iteration }}">
                                        <div class="relative w-full h-full group">
                                            <img :src="previews.img{{ $loop->iteration }}" class="w-full h-full object-cover rounded-lg">
                                            <button type="button" 
                                                @click.stop="removeFile('img{{ $loop->iteration }}', '{{ $key }}Input')"
                                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition-colors focus:outline-none"
                                                title="Hapus Foto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>

                                    {{-- Hidden Input --}}
                                    <input id="{{ $key }}Input" type="file" name="{{ $key }}" class="hidden" 
                                        accept="image/*"
                                        @change="handleFile($event, 'img{{ $loop->iteration }}')">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <p class="text-[10px] text-center text-gray-400 bg-gray-50 py-1.5 rounded border border-gray-200">
                            Format: JPG/PNG, Max 2MB
                        </p>
                    </div>
                </div>

            </div>

            {{-- KOLOM SIDEBAR (KANAN) --}}
            <div class="space-y-6">
                
                {{-- CARD 3: LOGITSIK --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                        <h3 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">Logistik</h3>
                    </div>
                    
                    <div class="p-5 space-y-5">
                        {{-- Jumlah Stok --}}
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 dash-border">
                            <label class="block text-sm font-bold text-blue-900 mb-2">Jumlah Stok Sistem</label>
                            <input type="number" name="quantity" value="{{ old('quantity', $asset->quantity) }}" min="0" 
                                class="text-center w-full rounded-lg border border-blue-200 py-2.5 font-bold text-blue-900 focus:ring-0 focus:border-blue-500 bg-white text-sm">
                            <p class="text-xs text-blue-600 mt-1.5 font-medium">Ubah manual hanya jika stok fisik berubah.</p>
                        </div>

                        {{-- Lokasi --}}
                        <div class="group">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Lokasi Penyimpanan</label>
                            <div class="grid grid-cols-2 gap-3">
                                <select name="lorong" class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm font-medium focus:border-indigo-600 focus:ring-0 transition-all">
                                    @foreach(range('A', 'Z') as $char)
                                        <option value="Area {{ $char }}" {{ old('lorong', $asset->lorong) == "Area $char" ? 'selected' : '' }}>Area {{ $char }}</option>
                                    @endforeach
                                </select>
                                <select name="rak" class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm font-medium focus:border-indigo-600 focus:ring-0 transition-all">
                                    @for($i = 1; $i <= 50; $i++)
                                        @php $rakCode = 'R-' . str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $rakCode }}" {{ old('rak', $asset->rak) == $rakCode ? 'selected' : '' }}>{{ $rakCode }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 4: INFORMASI KEUANGAN (DIPINDAHKAN KE SINI & SELALU TERBUKA) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3 bg-white border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md bg-emerald-100 text-emerald-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Informasi Keuangan</h3>
                                <p class="text-xs text-gray-500">Harga beli & penyusutan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 space-y-5 bg-gray-50/50">
                        <div class="grid grid-cols-1 gap-5">
                            {{-- Tanggal Beli --}}
                            <div class="group">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">Tanggal Pembelian <span class="text-red-500">*</span></label>
                                <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : '') }}" 
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all font-medium" required>
                            </div>
                            
                            {{-- Harga Beli --}}
                            <div class="group">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">Harga Per Unit (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold text-sm">Rp</span>
                                    <input type="number" name="purchase_price" value="{{ old('purchase_price', $asset->purchase_price) }}" 
                                        class="w-full rounded-lg border border-gray-300 bg-white pl-10 pr-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all font-medium" placeholder="0">
                                </div>
                            </div>

                            {{-- Masa Pakai --}}
                            <div class="group">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">
                                    Masa Pakai (Tahun)
                                </label>
                                <input type="number" name="useful_life_years" value="{{ old('useful_life_years', $asset->useful_life_years) }}" 
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all font-medium" placeholder="Contoh: 4">
                            </div>

                            {{-- Nilai Residu --}}
                            <div class="group">
                                <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">Nilai Residu (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold text-sm">Rp</span>
                                    <input type="number" name="residual_value" value="{{ old('residual_value', $asset->residual_value) }}" 
                                        class="w-full rounded-lg border border-gray-300 bg-white pl-10 pr-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all font-medium" placeholder="Estimasi harga jual akhir">
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Estimasi nilai sisa.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- TOMBOL SIMPAN --}}
                <div class="sticky bottom-6">
                    <button type="submit" class="w-full flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:-translate-y-1 hover:shadow-xl transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('assets.index') }}" class="mt-2 w-full flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
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