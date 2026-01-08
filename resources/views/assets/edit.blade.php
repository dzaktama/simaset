@extends('layouts.main')

@section('container')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Data Aset</h2>
            <p class="text-sm text-gray-600">Perbarui informasi, stok, atau status kepemilikan aset.</p>
        </div>
        <a href="/assets" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="/assets/{{ $asset->id }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @method('put')
            @csrf

            {{-- SECTION 1: INFO DASAR --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Aset</label>
                    <input type="text" name="name" value="{{ old('name', $asset->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Serial Number</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                    @error('serial_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah Stok Total</label>
                    <input type="number" name="quantity" id="totalStock" value="{{ old('quantity', $asset->quantity) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                    <p class="text-xs text-gray-500 mt-1">Stok saat ini yang tercatat di sistem.</p>
                    @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">{{ old('description', $asset->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kondisi / Catatan</label>
                    <textarea name="condition_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">{{ old('condition_notes', $asset->condition_notes) }}</textarea>
                </div>
            </div>

            {{-- SECTION 2: GAMBAR --}}
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-bold text-gray-900 mb-4">Foto Aset</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach(['image', 'image2', 'image3'] as $imgKey)
                    <div class="border rounded-lg p-3 text-center">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Foto {{ $loop->iteration }}</label>
                        @if($asset->$imgKey)
                            <img src="{{ asset('storage/' . $asset->$imgKey) }}" class="h-20 mx-auto mb-2 object-cover rounded">
                        @endif
                        <input type="file" name="{{ $imgKey }}" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- SECTION 3: STATUS & MANUAL OVERRIDE (ADMIN CONTROL) --}}
            <div class="border-t border-gray-200 pt-6 bg-gray-50 -mx-6 px-6 pb-6 mt-6">
                <h3 class="text-md font-bold text-gray-900 mb-1">Kontrol Peminjaman (Manual Override)</h3>
                <p class="text-xs text-gray-500 mb-4">Gunakan ini jika Anda ingin menetapkan aset ke user secara manual (tanpa request).</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Status Dasar --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Aset</label>
                        <select name="status" id="statusSelect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                            <option value="available" {{ old('status', $asset->status) == 'available' ? 'selected' : '' }}>Available (Tersedia)</option>
                            <option value="deployed" {{ old('status', $asset->status) == 'deployed' ? 'selected' : '' }}>Deployed (Dipinjam)</option>
                            <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Maintenance (Perbaikan)</option>
                            <option value="broken" {{ old('status', $asset->status) == 'broken' ? 'selected' : '' }}>Broken (Rusak)</option>
                        </select>
                    </div>

                    {{-- User Holder --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dipegang Oleh (User)</label>
                        <select name="user_id" id="userSelect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                            <option value="">-- Tidak Ada (Gudang) --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $asset->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->department }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- POIN 12: INPUT STOK OVERRIDE (BARU) --}}
                    <div id="manualQtyContainer" class="{{ $asset->user_id ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-blue-700">Jumlah yang Dipinjamkan <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="manual_quantity" id="manualQtyInput" 
                                value="{{ old('manual_quantity', ($asset->user_id ? $asset->quantity : 1)) }}" 
                                min="1" 
                                max="{{ $asset->quantity }}"
                                class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border bg-blue-50">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Jika jumlah < stok total, aset akan <b>dipecah (split)</b> otomatis. Sisa stok tetap di gudang.
                        </p>
                    </div>

                    {{-- Tanggal --}}
                    <div id="dateContainer" class="{{ $asset->user_id ? '' : 'hidden' }} md:col-span-2 grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Pinjam</label>
                            <input type="datetime-local" name="assigned_date" value="{{ old('assigned_date', $asset->assigned_date ? $asset->assigned_date->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Batas Kembali</label>
                            <input type="datetime-local" name="return_date" value="{{ old('return_date', $asset->return_date ? $asset->return_date->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
                <a href="/assets" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Script Sederhana untuk Tampilan Form Dinamis
    const userSelect = document.getElementById('userSelect');
    const statusSelect = document.getElementById('statusSelect');
    const dateContainer = document.getElementById('dateContainer');
    const manualQtyContainer = document.getElementById('manualQtyContainer');
    const totalStockInput = document.getElementById('totalStock');
    const manualQtyInput = document.getElementById('manualQtyInput');

    // Update Max pada Manual Qty saat Total Stock berubah
    totalStockInput.addEventListener('input', function() {
        manualQtyInput.max = this.value;
    });

    function toggleFields() {
        // Jika ada user dipilih, tampilkan tanggal & input jumlah
        if(userSelect.value) {
            dateContainer.classList.remove('hidden');
            manualQtyContainer.classList.remove('hidden');
            statusSelect.value = 'deployed'; // Auto set status
            manualQtyInput.max = totalStockInput.value; // Ensure max limit
        } else {
            // Jika user dikosongkan (kembali ke gudang)
            dateContainer.classList.add('hidden');
            manualQtyContainer.classList.add('hidden');
            if(statusSelect.value == 'deployed') {
                statusSelect.value = 'available'; // Auto reset status
            }
        }
    }

    userSelect.addEventListener('change', toggleFields);
</script>
@endsection