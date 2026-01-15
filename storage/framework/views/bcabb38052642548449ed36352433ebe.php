<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold" id="chartTitle">📊 Tren Peminjaman Aset</h3>
                    <p class="text-sm text-indigo-100 mt-1" id="chartDescription">Menampilkan jumlah aset yang diminta per periode</p>
                </div>
                <div class="flex items-center gap-2">
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

        
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2 flex-wrap">
                <button data-range="hourly" class="range-btn px-3 py-2 text-xs font-semibold rounded transition border border-transparent bg-white border-gray-300 hover:bg-gray-100 text-gray-700">Per Jam</button>
                <button data-range="daily" class="range-btn px-3 py-2 text-xs font-semibold rounded transition border border-transparent bg-white border-gray-300 hover:bg-gray-100 text-gray-700">Harian</button>
                <button data-range="monthly" class="range-btn px-3 py-2 text-xs font-semibold rounded transition border border-transparent bg-indigo-100 border-indigo-300 text-indigo-700 font-bold">Bulanan</button>
                <button data-range="yearly" class="range-btn px-3 py-2 text-xs font-semibold rounded transition border border-transparent bg-white border-gray-300 hover:bg-gray-100 text-gray-700">Tahunan</button>
            </div>
        </div>

        
        <div class="p-6 relative overflow-hidden" style="height: 380px;">
            
            <div id="chartSlide0" class="absolute inset-0 p-6 transition-all duration-500 ease-out" style="opacity: 1; transform: translateX(0);">
                <div class="relative w-full h-full">
                    <canvas id="borrowTrendChart"></canvas>
                </div>
            </div>

            
            <div id="chartSlide1" class="absolute inset-0 p-6 transition-all duration-500 ease-out" style="opacity: 0; transform: translateX(100%);">
                <div class="relative w-full h-full">
                    <canvas id="assetAdditionChart"></canvas>
                </div>
            </div>
        </div>

        
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-sm text-gray-600">
            <span id="chartInfo">📌 Geser atau klik tombol navigasi untuk melihat grafik lainnya</span>
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
</div><?php /**PATH C:\laragon\www\simaset_fix\resources\views/dashboard/admin_charts.blade.php ENDPATH**/ ?>