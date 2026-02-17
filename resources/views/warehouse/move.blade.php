@extends('layouts.main')

@section('container')
<div class="w-full px-6 py-8">
    
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Mutasi Aset</h2>
            <p class="text-sm text-gray-500 mt-1">Pindahkan aset dari satu lokasi ke lokasi lain.</p>
        </div>
        <a href="{{ route('warehouse.index') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all duration-200">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    {{-- DATA PREPARATION MOVED TO AJAX --}}
    @php
        // Only load initial racks/areas to keep page light
        $racksArray = [];
        for ($i = 1; $i <= 50; $i++) {
            $racksArray[] = 'R-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        $areasArray = range('A', 'Z');
        
        // Prepare selected asset data for JSON if exists
        $initialAssetData = null;
        if($selectedAsset) {
             $borrowerName = '-';
             $returnDate = '-';
             if ($selectedAsset->status === 'deployed' && $selectedAsset->activeRequest) {
                 $borrowerName = $selectedAsset->activeRequest->user->name ?? 'User Terhapus';
                 $returnDate = $selectedAsset->activeRequest->return_date ? $selectedAsset->activeRequest->return_date->translatedFormat('d F Y, H:i') . ' WIB' : 'Tidak ditentukan';
             }
             
             // Check restricted
             $isRestricted = in_array($selectedAsset->status, ['deployed', 'maintenance', 'broken']);

             $initialAssetData = [
                'id' => $selectedAsset->id,
                'name' => $selectedAsset->name,
                'serial_number' => $selectedAsset->serial_number,
                'text' => $selectedAsset->serial_number . ' - ' . $selectedAsset->name,
                'location' => $selectedAsset->location ?? 'Belum ada lokasi',
                'image' => $selectedAsset->image ? asset('storage/' . $selectedAsset->image) : null,
                'category' => $selectedAsset->category,
                'brand' => $selectedAsset->brand,
                'status' => $selectedAsset->status,
                'lorong' => $selectedAsset->lorong,
                'rak' => $selectedAsset->rak,
                'borrower_name' => $borrowerName,
                'return_date' => $returnDate,
                'is_restricted' => $isRestricted
             ];
        }
    @endphp

    <script>
        window.movePageData = {
            racks: {!! json_encode($racksArray) !!},
            areas: {!! json_encode($areasArray) !!},
            initialAsset: {!! json_encode($initialAssetData) !!}
        };
    </script>

    {{-- MAIN ALPINE SCOPE --}}
    <div class="w-full" x-data="{
        selectedAssetId: window.movePageData.initialAsset ? window.movePageData.initialAsset.id : '',
        targetLocation: '',
        
        // Asset Data (Initially Empty/Only Selected)
        currentAsset: window.movePageData.initialAsset,
        
        // Rack Picker State
        rackPickerOpen: false,
        selectedArea: 'A',
        racks: window.movePageData.racks,
        areas: window.movePageData.areas,
        
        // Specific Search Filters
        searchArea: '',
        searchRack: '',

        // Asset Search State
        assetSearch: '',
        searchResults: [],
        isLoading: false,
        assetDropdownOpen: false,
        
        get isRestricted() {
            return this.currentAsset && this.currentAsset.is_restricted;
        },
        
        get statusLabel() {
            if (!this.currentAsset) return '';
            const map = {
                'deployed': 'Sedang Dipinjam',
                'maintenance': 'Sedang Perbaikan',
                'broken': 'Rusak (Broken)',
                'available': 'Available'
            };
            return map[this.currentAsset.status] || this.currentAsset.status;
        },

        // Filtered Lists (Rack Picker)
        get filteredAreas() {
            if (!this.searchArea) return this.areas;
            const q = this.searchArea.toUpperCase();
            return this.areas.filter(a => a.includes(q));
        },

        get filteredRacks() {
            if (!this.searchRack) return this.racks;
            const q = this.searchRack.toString().replace(/^0+/, ''); // Remove leading zeros for flexible search
            const qFull = this.searchRack.toString().padStart(2, '0'); // Also match '05' if typed '5'
            
            return this.racks.filter(r => {
                const num = r.replace('R-', '');
                return num.includes(q) || num.includes(qFull);
            });
        },
        
        getRackInfo(rackName) {
            // Deprecated: Count info removed for performance (requires fetching all assets)
            return { count: 0, hasBroken: false };
        },
        
        selectRack(rackName) {
            if(this.isRestricted) return; // Prevent selection if restricted status

            this.targetLocation = 'Area ' + this.selectedArea + ' - Rak ' + rackName;
            this.rackPickerOpen = false;
        },

        // AJAX SEARCH FUNCTION
        async fetchAssets(query) {
            if (!query || query.length < 1) {
                this.searchResults = [];
                return;
            }
            
            this.isLoading = true;
            try {
                const response = await fetch(`/ajax/assets/search?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                this.searchResults = data.results.map(item => ({
                    id: item.id,
                    name: item.name,
                    serial_number: item.serial_number,
                    text: item.text,
                    status: item.status,
                    image: item.image,
                    category: item.category,
                    location: item.location,
                    lorong: item.lorong,
                    rak: item.rak,
                    
                    // Extra info might need separate fetch if not in searchJson
                    borrower_name: item.holder_name || '-',
                    return_date: '-', // Search API might not return date yet
                    is_restricted: item.is_restricted
                }));
            } catch (error) {
                console.error('Search error:', error);
                this.searchResults = [];
            } finally {
                this.isLoading = false;
            }
        },

        selectAsset(asset) {
            this.selectedAssetId = asset.id;
            this.currentAsset = asset;
            this.assetSearch = asset.text;
            this.assetDropdownOpen = false;
            this.targetLocation = ''; // Reset location
        },

        init() {
            // Watch search input
            this.$watch('assetSearch', (value) => {
                if (this.selectedAssetId && value !== this.currentAsset?.text) {
                     // If user types while asset selected, fetch new results (optional: clear selection)
                     this.assetDropdownOpen = true;
                }
                if (value.length > 0) {
                    this.fetchAssets(value);
                } else {
                    this.searchResults = [];
                }
            });

            this.$watch('rackPickerOpen', value => {
                if (value) {
                    setTimeout(() => {
                        this.$refs.searchAreaInput.focus();
                    }, 100);
                }
            });
            
            // Auto-select Area if exact match found
            this.$watch('searchArea', value => {
                const upper = value.toUpperCase();
                if (this.areas.includes(upper)) {
                    this.selectedArea = upper;
                }
            });

            // If initial asset exists
            if (this.currentAsset) {
                this.assetSearch = this.currentAsset.text;
                this.selectedAssetId = this.currentAsset.id;
            }
        }
    }">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            {{-- FORM --}}
            <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden order-last lg:order-first">
                <div class="p-6">
                    <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-4 mb-4">Form Mutasi</h3>
                    <form action="{{ route('warehouse.storeMove') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div class="group relative" @click.outside="assetDropdownOpen = false">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Aset <span class="text-red-500">*</span></label>
                                
                                {{-- Hidden Input for actual value --}}
                                <input type="hidden" name="asset_id" x-model="selectedAssetId">
                                
                                {{-- Search Input --}}
                                <div class="relative">
                                    <input type="text" x-model="assetSearch" 
                                        @focus="assetDropdownOpen = true"
                                        @input="assetDropdownOpen = true; selectedAssetId = ''" 
                                        placeholder="Ketik Nama atau No. Seri Aset..."
                                        class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 pr-10 text-sm text-gray-900 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all font-medium"
                                        required>
                                    
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    </div>
                                </div>

                                {{-- Dropdown List --}}
                                <div x-show="assetDropdownOpen && (searchResults.length > 0 || isLoading)" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute z-20 mt-1 w-full bg-white rounded-xl shadow-xl max-h-60 overflow-auto border border-gray-100 custom-scrollbar">
                                    
                                    <ul class="py-1 text-sm text-gray-700">
                                        <template x-for="asset in searchResults" :key="asset.id">
                                            <li @click="selectAsset(asset)"
                                                class="px-4 py-3 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer transition-colors border-b border-gray-50 last:border-0">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <div class="font-bold" x-text="asset.name"></div>
                                                        <div class="text-xs text-gray-400" x-text="asset.serial_number"></div>
                                                    </div>
                                                    <span x-show="asset.status === 'deployed'" class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-bold uppercase">Dipinjam</span>
                                                    <span x-show="asset.status === 'maintenance'" class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-bold uppercase">Maintenance</span>
                                                    <span x-show="asset.status === 'broken'" class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded font-bold uppercase">Broken</span>
                                                </div>
                                            </li>
                                        </template>
                                        
                                        <div x-show="isLoading" class="px-4 py-3 text-center text-gray-400 flex justify-center items-center gap-2">
                                            <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <span class="text-xs">Mencari...</span>
                                        </div>

                                        <div x-show="searchResults.length === 0 && !isLoading" class="px-4 py-3 text-center text-gray-400 text-xs">
                                            Aset tidak ditemukan.
                                        </div>
                                    </ul>
                                </div>
                                <p class="text-xs text-gray-400 mt-2">Ketik nama untuk mencari aset secara realtime.</p>
                            </div>

                            {{-- Alert Restricted Status (Form Blocker) --}}
                            <div x-show="isRestricted" x-transition class="rounded-lg border p-4"
                                :class="{
                                    'bg-blue-50 border-blue-200': currentAsset?.status === 'deployed',
                                    'bg-yellow-50 border-yellow-200': currentAsset?.status === 'maintenance',
                                    'bg-red-50 border-red-200': currentAsset?.status === 'broken'
                                }">
                                <div class="flex">
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"
                                        :class="{
                                            'text-blue-400': currentAsset?.status === 'deployed',
                                            'text-yellow-400': currentAsset?.status === 'maintenance',
                                            'text-red-400': currentAsset?.status === 'broken'
                                        }">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-bold"
                                            :class="{
                                                'text-blue-800': currentAsset?.status === 'deployed',
                                                'text-yellow-800': currentAsset?.status === 'maintenance',
                                                'text-red-800': currentAsset?.status === 'broken'
                                            }" x-text="'Aset ' + statusLabel"></h3>
                                        <div class="mt-2 text-sm">
                                            <p class="text-gray-700">Aset ini sedang dalam status <strong x-text="currentAsset?.status"></strong>. Anda tidak dapat melakukan mutasi lokasi sampai statusnya Available.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="group" :class="{'opacity-50 pointer-events-none': isRestricted}">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Tujuan <span class="text-red-500">*</span></label>
                                <input type="hidden" name="target_location" x-model="targetLocation" :required="!isRestricted">
                                <button type="button" @click="rackPickerOpen = true"
                                    class="w-full rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-4 text-sm text-left font-medium transition-all hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-700 focus:border-indigo-600 focus:ring-0 flex items-center justify-between group-hover:border-indigo-400"
                                    :class="targetLocation ? 'bg-indigo-50 border-indigo-200 border-solid' : ''">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="p-2 rounded-lg" :class="targetLocation ? 'bg-indigo-200 text-indigo-700' : 'bg-gray-200 text-gray-500'">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="truncate font-bold" :class="targetLocation ? 'text-indigo-900' : 'text-gray-500'" x-text="targetLocation || 'Pilih Rak dari Gudang'"></span>
                                            <span class="text-[10px] text-gray-400" x-text="targetLocation ? 'Klik untuk ubah lokasi' : 'Klik tombol ini untuk membuka peta'"></span>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </div>
                            <div class="group" :class="{'opacity-50 pointer-events-none': isRestricted}">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Mutasi</label>
                                <textarea name="notes" rows="3" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all font-medium placeholder-gray-400" placeholder="Contoh: Pemindahan ke gudang baru..."></textarea>
                            </div>
                        </div>
                        <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col-reverse gap-3">
                             <a href="{{ route('warehouse.index') }}" class="w-full py-3 text-center rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition">Batal</a>
                             <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 text-white text-sm font-bold shadow-lg hover:bg-indigo-700 hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5" 
                                :disabled="!selectedAssetId || !targetLocation || isRestricted"
                                :class="{'opacity-50 cursor-not-allowed': !selectedAssetId || !targetLocation || isRestricted}">
                                 Proses Mutasi
                             </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- PREVIEW --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden sticky top-6 min-h-[500px] flex flex-col">
                    <div x-show="!currentAsset" class="flex-1 flex flex-col items-center justify-center p-12 text-center" x-transition.opacity>
                        <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mb-6 animate-pulse">
                            <svg class="w-10 h-10 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Cari Aset Terlebih Dahulu</h3>
                        <p class="text-gray-500 mt-2 max-w-sm">Pilih aset dari menu sebelah kiri untuk melihat detail sebelum memindahkannya.</p>
                    </div>
                    <div x-show="currentAsset" class="flex flex-col md:flex-row h-full" style="display: none;" x-transition.opacity>
                        <div class="md:w-5/12 bg-gray-100 relative group overflow-hidden border-b md:border-b-0 md:border-r border-gray-200">
                            <template x-if="currentAsset?.image">
                                <img :src="currentAsset.image" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                            </template>
                            <template x-if="!currentAsset?.image">
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-50 p-12">
                                    <svg class="w-16 h-16 mb-4 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="text-xs font-bold uppercase tracking-wide opacity-60">No Image</span>
                                </div>
                            </template>
                            <div class="absolute top-4 left-4">
                                <span class="bg-green-500/90 backdrop-blur text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm uppercase tracking-wider"
                                    :class="currentAsset?.status === 'deployed' ? 'bg-indigo-600/90' : (currentAsset?.status === 'maintenance' ? 'bg-orange-500/90' : 'bg-green-500/90')"
                                    x-text="currentAsset?.status === 'deployed' ? 'DEPLOYED' : (currentAsset?.status === 'maintenance' ? 'MAINTENANCE' : 'READY')">
                                </span>
                            </div>
                        </div>
                        <div class="md:w-7/12 p-8 flex flex-col justify-center bg-white">
                            {{-- DEPLOYED INFO --}}
                            <div x-show="isDeployed" class="mb-6 bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-indigo-500 tracking-wider">Sedang Dipinjam Oleh</p>
                                        <p class="text-sm font-bold text-gray-900" x-text="currentAsset?.borrower_name || '-'"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-indigo-700 bg-indigo-100/50 px-3 py-2 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="font-medium">Jadwal Kembali: <span class="font-bold" x-text="currentAsset?.return_date || '-'"></span></span>
                                </div>
                            </div>

                            <div class="mb-8">
                                <span class="inline-block px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-[10px] font-extrabold uppercase tracking-widest mb-3 border border-indigo-100">Informasi Aset</span>
                                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight" x-text="currentAsset?.name"></h2>
                                <p class="text-base text-gray-500 font-mono mt-1" x-text="currentAsset?.serial_number"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-y-6 gap-x-4 mb-8">
                                <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Kategori</p><p class="text-sm font-bold text-gray-800" x-text="currentAsset?.category || '-'"></p></div>
                                <div><p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Merek</p><p class="text-sm font-bold text-gray-800" x-text="currentAsset?.brand || '-'"></p></div>
                            </div>
                            <div class="bg-gradient-to-br from-indigo-50 to-white rounded-xl p-5 border border-indigo-100 shadow-sm relative overflow-hidden">
                                <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-indigo-100 rounded-full opacity-20 blur-xl"></div>
                                <p class="text-[10px] text-indigo-500 uppercase font-bold tracking-wider mb-2 z-10 relative" x-text="isRestricted ? 'Lokasi Penyimpanan' : 'Lokasi Saat Ini'"></p>
                                <div class="flex items-center gap-3 z-10 relative">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <p class="text-lg font-bold text-gray-900" x-text="currentAsset?.location || 'Belum diatur'"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Holder Info (Conditional) --}}
                        <div x-show="currentAsset?.status === 'deployed'" class="bg-blue-50 rounded-lg p-3 border border-blue-100 mt-4">
                            <p class="text-[10px] uppercase font-bold text-blue-500 tracking-wider mb-1">Sedang Digunakan Oleh</p>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center text-xs font-bold text-blue-700">U</div>
                                <p class="text-sm font-bold text-gray-800" x-text="currentAsset?.borrower_name || '-'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL --}}
        @include('warehouse.partials.location_modal')

    </div>

</div>
@endsection
