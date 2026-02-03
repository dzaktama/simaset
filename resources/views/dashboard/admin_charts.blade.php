<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Carousel Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                        <span id="chartTitle">Tren Peminjaman Aset</span>
                    </h3>
                    <p class="text-sm text-indigo-100 mt-1 pl-8" id="chartDescription">Menampilkan jumlah aset yang diminta per periode</p>
                </div>
                <!-- Controls -->
                <div class="flex items-center gap-2">
                    {{-- Refresh Button --}}
                    <button type="button" onclick="window.dashboardLoadCharts && window.dashboardLoadCharts()" title="Refresh Data" class="p-2 rounded-lg bg-white/20 hover:bg-white/30 transition transform hover:scale-105 text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </button>
                    <div class="h-6 w-px bg-white/30 mx-1"></div>
                    
                    <button id="prevChart" class="p-2 rounded-lg bg-white/20 hover:bg-white/30 transition transform hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="flex gap-1.5">
                        <div id="dot0" class="w-2 h-2 rounded-full bg-white transition-all cursor-pointer hover:bg-white/80"></div>
                        <div id="dot1" class="w-2 h-2 rounded-full bg-white/40 transition-all cursor-pointer hover:bg-white/80"></div>
                    </div>
                    <button id="nextChart" class="p-2 rounded-lg bg-white/20 hover:bg-white/30 transition transform hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Range Buttons --}}
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2 flex-wrap">
                <button data-range="daily" class="range-btn px-3 py-2 text-xs font-semibold rounded transition border border-transparent bg-white border-gray-300 hover:bg-gray-100 text-gray-700">Harian</button>
                <button data-range="monthly" class="range-btn px-3 py-2 text-xs font-semibold rounded transition border border-transparent bg-indigo-100 border-indigo-300 text-indigo-700 font-bold">Bulanan</button>
                <button data-range="yearly" class="range-btn px-3 py-2 text-xs font-semibold rounded transition border border-transparent bg-white border-gray-300 hover:bg-gray-100 text-gray-700">Tahunan</button>
            </div>
        </div>

        {{-- Charts Container --}}
        <div class="p-6 relative overflow-hidden" style="height: 380px;">
            {{-- Loading Overlay --}}
            <div id="chartLoading" class="absolute inset-0 bg-white z-20 flex flex-col items-center justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-indigo-600 mb-3"></div>
                <p class="text-sm font-bold text-gray-600 animate-pulse">Memuat Data...</p>
            </div>
            {{-- Chart 1: Tren Peminjaman --}}
            <div id="chartSlide0" class="absolute inset-0 p-6 transition-all duration-500 ease-out" style="opacity: 1; transform: translateX(0);">
                <div class="relative w-full h-full">
                    <canvas id="borrowTrendChart"></canvas>
                </div>
            </div>

            {{-- Chart 2: Tren Penambahan Aset --}}
            <div id="chartSlide1" class="absolute inset-0 p-6 transition-all duration-500 ease-out" style="opacity: 0; transform: translateX(100%);">
                <div class="relative w-full h-full">
                    <canvas id="assetAdditionChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Info Footer --}}
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-sm text-gray-600 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span id="chartInfo">Geser atau klik tombol navigasi untuk melihat grafik lainnya</span>
        </div>
    </div>

    <div class="col-span-1 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Status Aset Saat Ini</h3>
            <p class="text-sm text-gray-500 mb-4">Distribusi status aset keseluruhan</p>
        </div>
        <div class="relative w-full" style="height: 280px;">
            <canvas id="assetsStatusPie"></canvas>
        </div>
    </div>
</div>