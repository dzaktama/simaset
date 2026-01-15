@extends('layouts.main')

@section('container')
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700">
        Edit Aset: {{ $asset->name }}
    </h2>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md">
        <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Nama Aset --}}
                <label class="block text-sm">
                    <span class="text-gray-700">Nama Aset</span>
                    <input type="text" name="name" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('name', $asset->name) }}" required>
                </label>

                {{-- Serial Number --}}
                <label class="block text-sm">
                    <span class="text-gray-700">Serial Number (SN)</span>
                    <input type="text" name="serial_number" class="block w-full mt-1 text-sm border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" value="{{ $asset->serial_number }}" readonly>
                    <span class="text-xs text-gray-500">SN tidak dapat diubah sembarangan.</span>
                </label>

                {{-- Kategori --}}
                <label class="block text-sm">
                    <span class="text-gray-700">Kategori</span>
                    <select name="category" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $asset->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </label>

                {{-- Jumlah Stok Total --}}
                <label class="block text-sm">
                    <span class="text-gray-700">Jumlah Stok Total</span>
                    <input type="number" name="quantity" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('quantity', $asset->quantity) }}" min="0" required>
                </label>

                {{-- Tanggal Pembelian --}}
                <label class="block text-sm">
                    <span class="text-gray-700">Tanggal Pembelian</span>
                    <input type="date" name="purchase_date" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('purchase_date', $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : '') }}">
                </label>

                {{-- [BAGIAN INI YANG DIUBAH JADI DROPDOWN] --}}
                {{-- Lokasi (Lorong & Rak) --}}
                <div class="grid grid-cols-2 gap-4">
                    {{-- Dropdown Lorong A-Z --}}
                    <label class="block text-sm">
                        <span class="text-gray-700">Lorong</span>
                        <select name="lorong" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="" disabled>Pilih Area</option>
                            @foreach(range('A', 'Z') as $char)
                                @php $val = "Area $char"; @endphp
                                <option value="{{ $val }}" {{ old('lorong', $asset->lorong) == $val ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    {{-- Dropdown Rak 1-50 --}}
                    <label class="block text-sm">
                        <span class="text-gray-700">Rak</span>
                        <select name="rak" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="" disabled>Pilih Rak</option>
                            @for($i = 1; $i <= 50; $i++)
                                @php $val = "R-" . sprintf('%02d', $i); @endphp
                                <option value="{{ $val }}" {{ old('rak', $asset->rak) == $val ? 'selected' : '' }}>
                                    {{ $val }} (Rak {{ $i }})
                                </option>
                            @endfor
                        </select>
                    </label>
                </div>

                {{-- Status Aset --}}
                <label class="block text-sm">
                    <span class="text-gray-700">Status Saat Ini</span>
                    <select name="status" id="status" onchange="toggleUserField()" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="available" {{ $asset->status == 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                        <option value="deployed" {{ $asset->status == 'deployed' ? 'selected' : '' }}>Dipinjam (Deployed)</option>
                        <option value="maintenance" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>Perbaikan (Maintenance)</option>
                        <option value="broken" {{ $asset->status == 'broken' ? 'selected' : '' }}>Rusak (Broken)</option>
                    </select>
                </label>

                {{-- Form Peminjam (Muncul jika Deployed) --}}
                <div id="user_field" class="block text-sm {{ $asset->status == 'deployed' ? '' : 'hidden' }} transition-all duration-300">
                    <span class="text-gray-700 font-semibold text-indigo-600">Peminjam (Override)</span>
                    <select name="user_id" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">-- Pilih User Peminjam --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $asset->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} - {{ $user->department ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        *Mengubah user di sini akan memindahkan kepemilikan aset ini secara paksa.
                    </p>
                    
                    @if($asset->quantity > 1)
                        <div class="mt-3 p-3 bg-yellow-50 rounded border border-yellow-200">
                            <span class="text-xs font-bold text-yellow-700 block mb-1">Opsi Pecah Stok (Opsional)</span>
                            <label class="text-xs text-gray-600">Jumlah yang dipinjam user ini:</label>
                            <input type="number" name="manual_quantity" class="w-20 text-sm border-gray-300 rounded-md" min="1" max="{{ $asset->quantity }}" placeholder="Qty">
                            <p class="text-[10px] text-gray-500 mt-1">Isi jika hanya sebagian stok yang dipinjam user ini. Sisanya akan tetap di gudang.</p>
                        </div>
                    @endif
                </div>

                {{-- Tanggal Pinjam & Kembali (Muncul jika Deployed) --}}
                <div id="date_fields" class="{{ $asset->status == 'deployed' ? '' : 'hidden' }} grid grid-cols-2 gap-4">
                    <label class="block text-sm">
                        <span class="text-gray-700">Tanggal Dipinjam</span>
                        <input type="date" name="assigned_date" class="block w-full mt-1 text-sm border-gray-300 rounded-md" 
                               value="{{ old('assigned_date', $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('Y-m-d') : '') }}">
                    </label>
                    <label class="block text-sm">
                        <span class="text-gray-700">Rencana Kembali</span>
                        <input type="date" name="return_date" class="block w-full mt-1 text-sm border-gray-300 rounded-md" 
                               value="{{ old('return_date', $asset->return_date ? \Carbon\Carbon::parse($asset->return_date)->format('Y-m-d') : '') }}">
                    </label>
                </div>

                {{-- Deskripsi --}}
                <label class="block text-sm md:col-span-2">
                    <span class="text-gray-700">Deskripsi / Spesifikasi</span>
                    <textarea name="description" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('description', $asset->description) }}</textarea>
                </label>

                {{-- Catatan Kondisi --}}
                <label class="block text-sm md:col-span-2">
                    <span class="text-gray-700">Catatan Kondisi</span>
                    <textarea name="condition_notes" class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="2">{{ old('condition_notes', $asset->condition_notes) }}</textarea>
                </label>

                {{-- Foto Aset --}}
                <div class="md:col-span-2">
                    <span class="text-gray-700">Foto Aset</span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                        <div class="border p-2 rounded-md">
                            <label class="block text-xs font-bold mb-1">Foto Utama</label>
                            @if($asset->image)
                                <img src="{{ asset('storage/' . $asset->image) }}" alt="Main Image" class="w-full h-32 object-cover rounded mb-2">
                            @endif
                            <input type="file" name="image" class="text-sm">
                        </div>
                        <div class="border p-2 rounded-md">
                            <label class="block text-xs font-bold mb-1">Foto Samping/Detail</label>
                            @if($asset->image2)
                                <img src="{{ asset('storage/' . $asset->image2) }}" alt="Image 2" class="w-full h-32 object-cover rounded mb-2">
                            @endif
                            <input type="file" name="image2" class="text-sm">
                        </div>
                        <div class="border p-2 rounded-md">
                            <label class="block text-xs font-bold mb-1">Foto Lainnya</label>
                            @if($asset->image3)
                                <img src="{{ asset('storage/' . $asset->image3) }}" alt="Image 3" class="w-full h-32 object-cover rounded mb-2">
                            @endif
                            <input type="file" name="image3" class="text-sm">
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex justify-end mt-6 gap-4">
                <a href="{{ route('assets.index') }}" class="px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition-colors duration-150 bg-gray-200 border border-transparent rounded-lg hover:bg-gray-300 focus:outline-none">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:shadow-outline-indigo">
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
        const dateFields = document.getElementById('date_fields');

        if (status === 'deployed') {
            userField.classList.remove('hidden');
            dateFields.classList.remove('hidden');
        } else {
            userField.classList.add('hidden');
            dateFields.classList.add('hidden');
        }
    }

    // Jalankan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        toggleUserField();
    });
</script>
@endsection