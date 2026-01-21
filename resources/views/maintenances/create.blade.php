@extends('layouts.main')

@section('container')
<div class="max-w-2xl mx-auto p-6">
    <div class="mb-6">
        <a href="{{ route('maintenances.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-2">
            &larr; Kembali ke Riwayat
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-indigo-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Input Perbaikan Aset (Service Log)</h2>
            <p class="text-indigo-100 text-sm mt-1">Catat aset yang rusak atau perlu pemeliharaan.</p>
        </div>
        
        <form action="{{ route('maintenances.store') }}" method="POST" class="p-6">
            @csrf

            <!-- Pilihan Aset -->
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Aset</label>
                <select name="asset_id" id="assetSelect" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" required>
                    <option value="">-- Pilih Aset --</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" {{ (isset($selectedAsset) && $selectedAsset->id == $asset->id) ? 'selected' : '' }}>
                            {{ $asset->serial_number }} - {{ $asset->name }} ({{ strtoupper($asset->status) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Vendor / Tempat Service</label>
                    <input type="text" name="vendor_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" placeholder="Contoh: Asus Service Center" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" required>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Masalah / Kerusakan</label>
                <textarea name="problem_description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" placeholder="Jelaskan kerusakan atau alasan maintenance..." required></textarea>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Estimasi Biaya Awal (Opsional)</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" name="cost" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-lg" placeholder="0">
                </div>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Icon Warning -->
                        <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Status aset akan otomatis berubah menjadi <strong>MAINTENANCE</strong> dan tidak bisa dipinjam.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('maintenances.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all">
                    Simpan & Proses Service
                </button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#assetSelect').select2({
            placeholder: "-- Cari Serial Number / Nama Aset --",
            allowClear: true
        });
    });
</script>
@endsection
