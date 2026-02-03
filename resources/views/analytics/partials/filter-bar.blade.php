{{-- GLOBAL DATE FILTER (TradingView Style) --}}
<div class="flex flex-col sm:flex-row items-center gap-4 bg-white rounded-lg shadow-sm border border-gray-200 p-1.5">
    
    {{-- Manual Inputs (Compact) --}}
    <div class="flex items-center gap-2 px-2 border-r border-gray-100">
        <input type="date" id="global-start-date" class="border-none text-xs font-semibold text-gray-600 focus:ring-0 p-0 w-24 font-sans">
        <span class="text-gray-300 text-xs">-</span>
        <input type="date" id="global-end-date" class="border-none text-xs font-semibold text-gray-600 focus:ring-0 p-0 w-24 font-sans">
        <button onclick="applyDateFilter()" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-md p-1 ml-1 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        </button>
    </div>

    {{-- Presets --}}
    <div class="flex items-center gap-1 overflow-x-auto custom-scrollbar px-1">
        <button onclick="setPreset('1D')" title= "1 Hari" id="btn-1D" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap font-sans">1H</button>
        <button onclick="setPreset('5D')" title= "5 Hari" id="btn-5D" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap font-sans">5H</button>
        <button onclick="setPreset('1M')" title= "1 Bulan" id="btn-1M" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap font-sans">1B</button>
        <button onclick="setPreset('6M')" title= "6 Bulan" id="btn-6M" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap font-sans">6B</button>
        <button onclick="setPreset('YTD')" title= "Tahun Ini" id="btn-YTD" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap font-sans">YTD</button>
        <button onclick="setPreset('1Y')" title= "1 Tahun" id=  "btn-1Y" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap font-sans">1T</button>
        <button onclick="setPreset('5Y')" title= "5 Tahun" id="btn-5Y" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap font-sans">5T</button>
        <button onclick="setPreset('ALL')" title= "Semua" id="btn-ALL" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap font-sans">Semua</button>
    </div>
</div>
