@extends('layouts.main')

@section('container')
<div class="container mx-auto px-4 py-8" x-data="maintenanceForm()">
    
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Input Perbaikan Aset</h1>
            <p class="text-gray-600 mt-1">Buat tiket maintenance baru untuk aset yang bermasalah.</p>
        </div>
        <a href="{{ route('maintenances.index') }}" class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Riwayat
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        {{-- LEFT COLUMN: FORM INPUT --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('maintenances.store') }}" method="POST" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                @csrf
                
                {{-- Form Header --}}
                <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Formulir Perbaikan
                    </h2>
                </div>

                <div class="p-8 space-y-6">
                {{-- 1. Pilih Aset & Filter --}}
                <div class="space-y-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        {{-- Filter Dropdown --}}
                        <div class="w-full md:w-1/3">
                            <label class="font-bold text-gray-700 mb-2 block text-sm uppercase tracking-wide">Filter Status</label>
                            <select x-model="filterStatus" @change="updateAssetList()" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm bg-white">
                                <option value="">Semua Status</option>
                                <option value="available">Available (Tersedia)</option>
                                <option value="deployed">Deployed (Dipakai)</option>
                                <option value="broken">Broken (Rusak)</option>
                                <option value="maintenance">Maintenance (Sedang Perbaikan)</option>
                            </select>
                        </div>
                        
                        {{-- Asset Select --}}
                        <div class="w-full md:w-2/3">
                            <label class="font-bold text-gray-700 mb-2 block text-sm uppercase tracking-wide">Pilih Aset yang Bermasalah <span class="text-red-500">*</span></label>
                            <select id="assetSelect" name="asset_id" class="w-full" required>
                                <option value="">-- Cari Serial Number / Nama Aset --</option>
                                {{-- Options populated by JS --}}
                            </select>
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-500 flex items-center gap-1">
                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pilih aset untuk melihat detail informasi di sebelah kanan.
                    </p>
                </div>

                {{-- 2. Detail Vendor, Tanggal & Prioritas --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <label class="font-bold text-gray-700 mb-2 block text-sm uppercase tracking-wide">Prioritas <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <select name="priority" class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm appearance-none bg-white cursor-pointer" required>
                                    <option value="normal">Normal</option>
                                    <option value="high">High (Mendesak)</option>
                                    <option value="critical">Critical (Darurat)</option>
                                </select>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="font-bold text-gray-700 mb-2 block text-sm uppercase tracking-wide">Vendor / Tempat Service <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <input type="text" name="vendor_name" class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm" placeholder="Nama Service Center / Teknisi" required>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="font-bold text-gray-700 mb-2 block text-sm uppercase tracking-wide">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm" required>
                            </div>
                        </div>
                        <div>
                            <label class="font-bold text-gray-700 mb-2 block text-sm uppercase tracking-wide">Estimasi Biaya <span class="text-gray-400 font-normal normal-case">(Opsional)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm font-bold">Rp</span>
                                </div>
                                <input type="number" name="cost" class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm" placeholder="0">
                            </div>
                        </div>
                    </div>

                    {{-- 3. Deskripsi Masalah --}}
                    <div>
                        <label class="font-bold text-gray-700 mb-2 block text-sm uppercase tracking-wide">Deskripsi Masalah <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <textarea name="problem_description" rows="5" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition shadow-sm resize-none" placeholder="Jelaskan detail kerusakan, error, atau alasan maintenance..." required></textarea>
                            <div class="absolute bottom-3 right-3 text-xs text-gray-400 bg-white px-1 rounded">Min. 10 Karakter</div>
                        </div>
                    </div>

                    {{-- Alert Warning --}}
                    <div class="flex items-start gap-4 p-4 bg-yellow-50 text-yellow-800 rounded-lg border border-yellow-200">
                        <svg class="w-6 h-6 flex-shrink-0 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div class="text-sm">
                            <span class="font-bold block mb-1">Perhatian!</span>
                            Status aset akan otomatis berubah menjadi <span class="font-bold uppercase bg-yellow-200 px-1 rounded">Maintenance</span>. Selama status ini, aset tidak dapat dipinjam atau digunakan oleh user.
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 border-t border-gray-200 flex justify-end gap-3">
                    <button type="reset" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-bold hover:bg-gray-100 transition">Reset</button>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition transform active:scale-95">
                        Simpan & Proses
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT COLUMN: STICKY ASSET CARD --}}
        <div class="lg:col-span-1 lg:sticky lg:top-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden relative min-h-[400px]">
                
                {{-- State: No Asset Selected --}}
                <div x-show="!selectedAsset" class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center bg-gray-50/50 backdrop-blur-sm z-10 transition-opacity">
                    <div class="bg-indigo-100 p-4 rounded-full mb-4">
                        <svg class="w-10 h-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Cari Aset Dulu</h3>
                    <p class="text-sm text-gray-500 mt-2">Pilih aset melalui formulir di samping untuk melihat detail lengkapnya di sini.</p>
                </div>

                {{-- State: Asset Selected --}}
                <div x-show="selectedAsset" class="bg-white transition-opacity duration-300" x-transition:enter="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="relative h-48 bg-gray-200 group">
                        <img :src="selectedAsset?.image ? '/storage/' + selectedAsset.image : 'https://placehold.co/400x300?text=No+Image'" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                             alt="Asset Image">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <div class="text-xs font-bold uppercase tracking-wider bg-indigo-600 px-2 py-0.5 rounded w-fit mb-1" x-text="selectedAsset?.category || 'General'"></div>
                            <h3 class="text-lg font-bold leading-tight" x-text="selectedAsset?.name || 'Nama Aset'"></h3>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Quick Stats --}}
                        <div class="grid grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Serial Number</p>
                                <p class="font-mono font-bold text-gray-800 text-sm mt-0.5" x-text="selectedAsset?.serial_number || '-'"></p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Status Saat Ini</p>
                                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full"
                                      :class="{
                                          'bg-green-100 text-green-700': selectedAsset?.status === 'available',
                                          'bg-blue-100 text-blue-700': selectedAsset?.status === 'deployed',
                                          'bg-yellow-100 text-yellow-700': selectedAsset?.status === 'maintenance',
                                          'bg-red-100 text-red-700': selectedAsset?.status === 'broken'
                                      }"
                                      x-text="selectedAsset?.status || '-'">
                                </span>
                            </div>
                        </div>

                        {{-- Location & Condition --}}
                        <div class="grid grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Lokasi</p>
                                </div>
                                <p class="text-sm font-medium text-gray-700" x-text="(selectedAsset?.lorong || '-') + ' / Rak ' + (selectedAsset?.rak || '-')"></p>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Kondisi</p>
                                </div>
                                <p class="text-sm font-medium text-gray-700" x-text="selectedAsset?.condition_notes || 'Kondisi Baik'"></p>
                            </div>
                        </div>

                        {{-- Holder Info (Conditional) --}}
                        <div x-show="selectedAsset?.status === 'deployed'" class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                            <p class="text-[10px] uppercase font-bold text-blue-500 tracking-wider mb-1">Sedang Digunakan Oleh</p>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center text-xs font-bold text-blue-700">U</div>
                                <p class="text-sm font-bold text-gray-800" x-text="selectedAsset?.holder?.name || 'Unknown User'"></p>
                            </div>
                        </div>

                        <div class="pt-2 text-center">
                            <p class="text-xs text-gray-400 italic">Pastikan aset ini benar sebelum melanjutkan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DEPENDENCIES --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 42px; border-radius: 0.5rem; border-color: #d1d5db; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { top: 7px; right: 10px; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="//unpkg.com/alpinejs" defer></script>

<script>
    const allAssets = @json($assets);
    const initialAssetId = "{{ $selectedAsset->id ?? '' }}";

    function maintenanceForm() {
        return {
            selectedAsset: null,
            filterStatus: '',
            
            init() {
                // Initial Load
                this.updateAssetList();

                if(initialAssetId) {
                    this.selectedAsset = allAssets.find(a => a.id == initialAssetId);
                    // Set value and trigger change for Select2
                    $('#assetSelect').val(initialAssetId).trigger('change');
                }

                // Init Select2
                $('#assetSelect').select2({
                    placeholder: "Cari Aset...",
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Aset tidak ditemukan (Cek filter status).";
                        }
                    }
                }).on('change', (e) => {
                    const id = e.target.value;
                    this.selectedAsset = id ? allAssets.find(a => a.id == id) : null;
                });
            },

            updateAssetList() {
                const $select = $('#assetSelect');
                const currentVal = $select.val(); // Keep selected if possible
                
                // Filter Logic
                const filtered = this.filterStatus 
                    ? allAssets.filter(a => a.status === this.filterStatus)
                    : allAssets;

                // Rebuild Options
                $select.empty().append('<option value="">-- Cari Serial Number / Nama Aset --</option>');
                
                filtered.forEach(asset => {
                    // Create option manually to ensure Select2 picks it up
                    const optionText = `${asset.serial_number} - ${asset.name}`;
                    const option = new Option(optionText, asset.id, false, false);
                    $select.append(option);
                });

                // Restore Value if still valid in filtered list
                if(currentVal && filtered.find(a => a.id == currentVal)) {
                    $select.val(currentVal);
                } else if(initialAssetId && !currentVal && filtered.find(a => a.id == initialAssetId)) {
                     // If we have an initial ID (e.g. from redirect) and it matches, select it
                     $select.val(initialAssetId);
                } else {
                    $select.val(null); // Clear if filtered out
                }
                
                $select.trigger('change');
            }
        }
    }
</script>
@endsection
