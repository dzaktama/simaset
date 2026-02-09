        {{-- MODAL RACK PICKER --}}
        <div x-show="rackPickerOpen" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" 
                 x-show="rackPickerOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="rackPickerOpen = false"></div>

            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl flex flex-col overflow-hidden ring-1 ring-gray-900/5"
                     x-show="rackPickerOpen"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    {{-- MODAL HEADER: DUAL SEARCH --}}
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center sticky top-0 z-10 gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900">Pilih Lokasi</h3>
                            <p class="text-xs text-gray-400 mt-1">Cari Area (Huruf) & Rak (Angka) yang tersedia.</p>
                        </div>
                        
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            {{-- Search Area --}}
                            <div class="relative w-full md:w-32">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-xs font-bold">AREA</span>
                                </div>
                                <input type="text" x-ref="searchAreaInput" x-model="searchArea" 
                                    class="w-full pl-12 pr-3 py-2 rounded-xl border border-gray-200 bg-gray-50 text-sm font-bold uppercase focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 placeholder-gray-400 transition-all text-center" 
                                    placeholder="A, B.." maxlength="1" @keydown.enter.prevent>
                            </div>
                            
                            <span class="text-gray-300 font-bold">-</span>

                            {{-- Search Rack --}}
                            <div class="relative w-full md:w-32">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-xs font-bold">RAK</span>
                                </div>
                                <input type="number" x-model="searchRack" 
                                    class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-200 bg-gray-50 text-sm font-bold focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 placeholder-gray-400 transition-all text-center" 
                                    placeholder="1-50" @keydown.enter.prevent>
                            </div>
                        </div>

                        <button @click="rackPickerOpen = false" class="p-2 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors hidden md:block">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    {{-- BODY --}}
                    <div class="p-0 overflow-hidden flex flex-col md:flex-row h-[500px] max-h-[60vh]">
                        
                        {{-- SIDEBAR: AREA --}}
                        <div class="w-full md:w-64 bg-gray-50 border-r border-gray-200 overflow-y-auto custom-scrollbar flex-shrink-0">
                            <div class="p-4">
                                <h4 class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4 px-2">Daftar Area</h4>
                                <div class="grid grid-cols-2 md:grid-cols-1 gap-2">
                                    <template x-for="area in filteredAreas" :key="area">
                                        <button type="button" @click="selectedArea = area"
                                            class="flex items-center justify-between px-4 py-3 rounded-xl text-left transition-all duration-200 w-full group"
                                            :class="selectedArea === area ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'bg-white text-gray-600 border border-gray-200 hover:border-indigo-300 hover:text-indigo-600'">
                                            <span class="font-bold text-sm">Area <span x-text="area"></span></span>
                                            <svg x-show="selectedArea === area" class="w-4 h-4 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                        </button>
                                    </template>
                                    {{-- Empty State Area --}}
                                    <div x-show="filteredAreas.length === 0" class="col-span-2 md:col-span-1 p-4 text-center text-gray-400 text-xs">
                                        Area tidak ditemukan.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MAIN: RACK --}}
                        <div class="flex-1 overflow-y-auto bg-white p-6 custom-scrollbar relative">
                            <div class="mb-6 flex items-center justify-between">
                                <h4 class="text-lg font-bold text-gray-800">
                                    Rak di Area <span x-text="selectedArea" class="text-indigo-600 text-xl ml-1 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100"></span>
                                </h4>
                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span> Terisi</span>
                                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-200"></span> Kosong</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3 pb-20">
                                <template x-for="rack in filteredRacks" :key="rack">
                                    <button type="button" @click="selectRack(rack)"
                                        class="aspect-square rounded-2xl border flex flex-col items-center justify-center p-2 transition-all duration-200 relative group overflow-hidden"
                                        :class="{
                                            'bg-white border-gray-200 text-gray-400 hover:border-indigo-400 hover:text-indigo-600 hover:shadow-md': getRackInfo(rack).count === 0,
                                            'bg-blue-50 border-blue-200 text-blue-600 font-bold shadow-sm hover:bg-blue-100 hover:border-blue-400': getRackInfo(rack).count > 0 && !getRackInfo(rack).hasBroken,
                                            'bg-red-50 border-red-200 text-red-600 font-bold shadow-sm hover:bg-red-100 hover:border-red-400': getRackInfo(rack).count > 0 && getRackInfo(rack).hasBroken
                                        }">
                                        <svg class="w-8 h-8 opacity-80 mb-1 transition-transform group-hover:scale-110" 
                                            :class="{
                                                'text-gray-300 group-hover:text-indigo-500': getRackInfo(rack).count === 0,
                                                'text-blue-400 group-hover:text-white': getRackInfo(rack).count > 0 && !getRackInfo(rack).hasBroken,
                                                'text-red-400 group-hover:text-white': getRackInfo(rack).count > 0 && getRackInfo(rack).hasBroken
                                            }"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                        </svg>
                                        <span class="text-sm font-bold" x-text="rack"></span>
                                        <div class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[10px] font-bold min-w-[1.2rem] text-center"
                                             :class="{
                                                 'hidden': getRackInfo(rack).count === 0,
                                                 'bg-blue-200/50 text-blue-700 group-hover:bg-white/20 group-hover:text-white': getRackInfo(rack).count > 0 && !getRackInfo(rack).hasBroken,
                                                 'bg-red-200/50 text-red-700 group-hover:bg-white/20 group-hover:text-white': getRackInfo(rack).count > 0 && getRackInfo(rack).hasBroken
                                             }">
                                            <span x-text="getRackInfo(rack).count"></span>
                                        </div>
                                    </button>
                                </template>
                                {{-- Empty State Rack --}}
                                <div x-show="filteredRacks.length === 0" class="col-span-full p-8 text-center text-gray-400">
                                    <p>Rak tidak ditemukan di Area ini.</p>
                                    <button @click="searchRack = ''; searchArea = ''" class="text-indigo-600 hover:underline text-sm font-bold mt-2">Reset Pencarian</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
