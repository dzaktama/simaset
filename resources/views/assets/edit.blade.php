@extends('layouts.main')

@section('container')
<div class="container px-6 mx-auto grid">
    
    <div class="flex justify-between items-center my-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-700">Edit Aset</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui data inventaris aset.</p>
        </div>
        <a href="{{ route('assets.index') }}" class="px-4 py-2 text-sm font-medium leading-5 text-gray-600 transition-colors duration-150 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none shadow-sm">
            ← Kembali
        </a>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-md border border-gray-200 mb-8">
        {{-- Tampilkan Error Validasi jika ada --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                <p class="font-bold">Terdapat Kesalahan Input</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- === KOLOM KIRI: DATA UTAMA ASET === --}}
                <div class="space-y-5">
                    
                    {{-- Nama Barang --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2 border" value="{{ old('name', $asset->name) }}" required>
                    </div>

                    {{-- Serial Number (Readonly) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Serial Number <span class="text-gray-400 font-normal text-xs">(Auto-Generated)</span>
                        </label>
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-100 text-gray-500 text-sm font-bold">#</span>
                            <input type="text" name="serial_number" class="flex-1 block w-full text-sm rounded-none rounded-r-md border-gray-300 bg-gray-50 text-gray-500 cursor-not-allowed border px-3 py-2" value="{{ $asset->serial_number }}" readonly>
                        </div>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2 border">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $asset->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jumlah Stok & Tgl Beli --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Stok <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2 border" value="{{ old('quantity', $asset->quantity) }}" min="0" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tgl Beli <span class="text-red-500">*</span></label>
                            <input type="date" name="purchase_date" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2 border" value="{{ old('purchase_date', $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    {{-- Status Aset --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Saat Ini</label>
                        <select name="status" id="status" onchange="toggleUserField()" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2 border">
                            <option value="available" {{ $asset->status == 'available' ? 'selected' : '' }}>Available (Tersedia)</option>
                            <option value="deployed" {{ $asset->status == 'deployed' ? 'selected' : '' }}>Deployed (Dipakai)</option>
                            <option value="maintenance" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>Maintenance (Perbaikan)</option>
                            <option value="broken" {{ $asset->status == 'broken' ? 'selected' : '' }}>Broken (Rusak)</option>
                            <option value="lost" {{ $asset->status == 'lost' ? 'selected' : '' }}>Lost (Hilang)</option>
                        </select>
                    </div>

                    {{-- Form Peminjam (Hidden Logic) --}}
                    <div id="user_field" class="hidden p-4 bg-indigo-50 rounded-md border border-indigo-100">
                        <label class="block text-sm font-semibold text-indigo-700 mb-1">Peminjam (User)</label>
                        <select name="user_id" class="block w-full text-sm rounded-md border-indigo-200 shadow-sm px-3 py-2 border mb-3">
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $asset->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-medium text-gray-600">Tgl Pinjam</label>
                                <input type="date" name="assigned_date" class="block w-full text-xs rounded-md border-gray-300 px-2 py-1 border" value="{{ old('assigned_date', $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('Y-m-d') : '') }}">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Rencana Kembali</label>
                                <input type="date" name="return_date" class="block w-full text-xs rounded-md border-gray-300 px-2 py-1 border" value="{{ old('return_date', $asset->return_date ? \Carbon\Carbon::parse($asset->return_date)->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-dashed border-gray-300 my-2"></div>

                    {{-- Lokasi (Lorong & Rak) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Lorong / Area</label>
                            <select name="lorong" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2 border">
                                @foreach(range('A', 'Z') as $char)
                                    @php $val = "Area $char"; @endphp
                                    <option value="{{ $val }}" {{ old('lorong', $asset->lorong) == $val ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Rak</label>
                            <select name="rak" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2 border">
                                @for($i = 1; $i <= 50; $i++)
                                    @php $val = "R-" . sprintf('%02d', $i); @endphp
                                    <option value="{{ $val }}" {{ old('rak', $asset->rak) == $val ? 'selected' : '' }}>{{ $val }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 italic">*Wajib diisi untuk pemetaan aset di gudang.</p>
                </div>

                {{-- === KOLOM KANAN: DESKRIPSI & FOTO === --}}
                <div class="space-y-5">
                    
                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2 border" rows="4">{{ old('description', $asset->description) }}</textarea>
                    </div>

                    {{-- Catatan Kondisi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Kondisi Awal</label>
                        <textarea name="condition_notes" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2 border" rows="3">{{ old('condition_notes', $asset->condition_notes) }}</textarea>
                    </div>

                    {{-- Upload Foto (SEJAJAR / HORIZONTAL) --}}
                    <div class="border border-gray-200 rounded-lg p-5 bg-white shadow-sm">
                        <h3 class="text-sm font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Upload Foto Dokumentasi</h3>
                        
                        {{-- GRID 3 KOLOM AGAR FOTO SEJAJAR --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            <div class="flex flex-col">
                                <label class="text-xs font-bold text-gray-700 mb-2">Foto Utama (Wajib)</label>
                                @if($asset->image)
                                    <div class="mb-2 relative group flex-1">
                                        <img src="{{ asset('storage/' . $asset->image) }}" class="w-full h-32 object-cover rounded-md border border-gray-300 shadow-sm hover:opacity-90 transition" alt="Foto Utama">
                                    </div>
                                @else
                                    <div class="mb-2 w-full h-32 bg-gray-50 rounded-md border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-xs">
                                        Kosong
                                    </div>
                                @endif
                                <input type="file" name="image" class="w-full text-[10px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded shadow-sm">
                            </div>

                            <div class="flex flex-col">
                                <label class="text-xs font-bold text-gray-700 mb-2">Foto Samping</label>
                                @if($asset->image2)
                                    <div class="mb-2 relative flex-1">
                                        <img src="{{ asset('storage/' . $asset->image2) }}" class="w-full h-32 object-cover rounded-md border border-gray-300 shadow-sm" alt="Detail 1">
                                    </div>
                                @else
                                    <div class="mb-2 w-full h-32 bg-gray-50 rounded-md border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-xs">
                                        Kosong
                                    </div>
                                @endif
                                <input type="file" name="image2" class="w-full text-[10px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded shadow-sm">
                            </div>

                            <div class="flex flex-col">
                                <label class="text-xs font-bold text-gray-700 mb-2">Foto Belakang</label>
                                @if($asset->image3)
                                    <div class="mb-2 relative flex-1">
                                        <img src="{{ asset('storage/' . $asset->image3) }}" class="w-full h-32 object-cover rounded-md border border-gray-300 shadow-sm" alt="Detail 2">
                                    </div>
                                @else
                                    <div class="mb-2 w-full h-32 bg-gray-50 rounded-md border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-xs">
                                        Kosong
                                    </div>
                                @endif
                                <input type="file" name="image3" class="w-full text-[10px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded shadow-sm">
                            </div>

                        </div>
                        
                        <p class="text-[10px] text-gray-400 mt-4 text-center border-t border-gray-100 pt-2">Format: JPG, PNG, JPEG. Max 2MB.</p>
                    </div>

                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-gray-100">
                <a href="{{ route('assets.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
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
    document.addEventListener('DOMContentLoaded', toggleUserField);
</script>
@endsection