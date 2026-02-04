<div class="mt-4 pt-4 border-t border-gray-100" 
     x-data="{ showModal: false }">
    <div class="px-3 mb-1 flex items-center justify-between group cursor-pointer hover:bg-gray-50 rounded-lg py-1.5 transition-colors">
        <div class="flex items-center gap-2">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Bantuan</p>
        </div>
        {{-- Info Icon & Modal Trigger --}}
        <button type="button" @click.stop="showModal = true" 
                class="p-1 rounded-full hover:bg-indigo-100 text-gray-300 hover:text-indigo-600 transition-colors focus:outline-none">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </button>

        {{-- MODAL WITH TELEPORT --}}
        <template x-teleport="body">
            <div x-show="showModal" class="fixed inset-0 z-[99] flex items-center justify-center px-4" style="display: none;">
                {{-- Backdrop --}}
                <div x-show="showModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="showModal = false"
                     class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

                {{-- Modal Content --}}
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     class="bg-white rounded-2xl shadow-xl w-full max-w-md relative overflow-hidden transform transition-all">
                    
                    {{-- Header --}}
                    <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-white font-bold text-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Bantuan & Panduan
                        </h3>
                        <button @click="showModal = false" class="text-indigo-200 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-6">
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            Butuh bantuan? Menu <strong>Panduan Sistem</strong> menyediakan dokumentasi lengkap untuk memandu Anda menggunakan SIMASET.
                        </p>
                        <div class="space-y-2">
                            <a href="#" class="block p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors group">
                                <h6 class="text-xs font-bold text-gray-800 group-hover:text-indigo-600">SOP Peminjaman</h6>
                                <p class="text-[10px] text-gray-500">Tata cara meminjam barang inventaris kantor.</p>
                            </a>
                            <a href="#" class="block p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors group">
                                <h6 class="text-xs font-bold text-gray-800 group-hover:text-indigo-600">Video Tutorial</h6>
                                <p class="text-[10px] text-gray-500">Langkah-langkah penggunaan fitur dalam format video.</p>
                            </a>
                            <a href="#" class="block p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors group">
                                <h6 class="text-xs font-bold text-gray-800 group-hover:text-indigo-600">FAQ (Tanya Jawab)</h6>
                                <p class="text-[10px] text-gray-500">Solusi untuk kendala yang sering ditemui.</p>
                            </a>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-3 flex justify-end">
                        <button @click="showModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>
    
    <nav class="space-y-0.5 ml-1">
        <a href="{{ route('guides.index') }}" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('guides.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
            <svg class="shrink-0 h-4 w-4 mr-2.5 {{ request()->routeIs('guides.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span>Panduan Sistem</span>
        </a>
    </nav>
</div>
