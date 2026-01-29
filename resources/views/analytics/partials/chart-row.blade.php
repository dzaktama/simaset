<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
    <div class="grid grid-cols-1 lg:grid-cols-4 min-h-[250px]">
        {{-- Kiri: Grafik Area --}}
        <div class="lg:col-span-3 flex flex-col border-r border-gray-100">
            {{-- Compact Header --}}
            <div class="bg-gradient-to-r from-indigo-700 to-indigo-600 px-4 py-3 flex flex-col xl:flex-row justify-between items-center text-white shadow-inner gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        {!! $icon !!}
                    </svg>
                    <div>
                        <h3 class="text-base font-bold tracking-tight leading-tight">{{ $title }}</h3>
                        <p class="text-[10px] text-indigo-200 font-medium opacity-90 hidden sm:block">{{ $desc }}</p>
                    </div>
                </div>

                {{-- Compact Controls --}}
                <div class="flex flex-col sm:flex-row items-center gap-2 w-full xl:w-auto">
                    {{-- Date Range --}}
                    <div class="flex items-center gap-1 bg-indigo-800/50 p-0.5 rounded border border-indigo-500/30">
                        <input type="date" id="start-{{ $id }}" class="bg-transparent border-0 text-white text-[10px] p-0.5 focus:ring-0 w-20 placeholder-indigo-300 h-6" placeholder="Start">
                        <span class="text-indigo-300 text-[10px]">-</span>
                        <input type="date" id="end-{{ $id }}" class="bg-transparent border-0 text-white text-[10px] p-0.5 focus:ring-0 w-20 placeholder-indigo-300 h-6" placeholder="End">
                    </div>

                    {{-- Grouping Buttons --}}
                    <div class="flex items-center bg-indigo-800/50 p-0.5 rounded border border-indigo-500/30">
                        <button onclick="updateChart('{{ $id }}', 'day')" class="px-2 py-0.5 text-[10px] font-bold rounded hover:bg-white/10 transition text-indigo-100" id="btn-day-{{ $id }}">Harian</button>
                        <button onclick="updateChart('{{ $id }}', 'month')" class="px-2 py-0.5 text-[10px] font-bold rounded hover:bg-white/10 transition text-indigo-100" id="btn-month-{{ $id }}">Bulanan</button>
                        <button onclick="updateChart('{{ $id }}', 'year')" class="px-2 py-0.5 text-[10px] font-bold rounded hover:bg-white/10 transition text-indigo-100" id="btn-year-{{ $id }}">Tahunan</button>
                    </div>
                </div>
            </div>

            {{-- Compact Canvas Container --}}
            <div class="p-3 sm:p-5 h-56 sm:h-72 relative bg-white flex-1 flex items-center justify-center overflow-hidden">
                 <canvas id="chart-{{ $id }}" class="w-full h-full"></canvas>
                 
                 {{-- Reset Zoom Button (Appears on top-right) --}}
                 <button onclick="resetChartZoom('{{ $id }}')" id="reset-zoom-{{ $id }}" 
                         class="absolute top-2 right-2 bg-white border border-gray-200 text-gray-600 px-2 py-1 rounded-md text-[10px] font-bold shadow-sm hover:bg-gray-100 hover:text-indigo-600 transition-all duration-200 hidden items-center gap-1 z-20">
                     <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                     Reset View
                 </button>
                 
                 {{-- Usage Hint (Bottom) --}}
                 <div class="absolute bottom-1 left-1/2 -translate-x-1/2 bg-black/60 text-white text-[9px] px-2 py-0.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                     Drag untuk geser • Ctrl+Scroll untuk zoom
                 </div>
            </div>
        </div>

        {{-- Kanan: Info Insight --}}
        <div class="lg:col-span-1 bg-gray-50 p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-24 h-24 rounded-full bg-indigo-100 blur-2xl opacity-50 pointer-events-none"></div>

            <div>
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                    Detail Penjelasan Grafik
                    <div class="h-px bg-gray-300 flex-1"></div>
                </h4>
                <p class="text-xs text-gray-600 leading-snug font-medium">
                    {{ $insight }}
                </p>
                
                <div class="mt-4 p-3 bg-white rounded-xl border border-gray-200 shadow-sm transition hover:scale-105 duration-300 cursor-default">
                    <span class="block text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Status Indikator</span>
                    <span class="text-base font-bold {{ $statusColor }} flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ str_replace('text-', 'bg-', $statusColor) }}"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ str_replace('text-', 'bg-', $statusColor) }}"></span>
                        </span>
                        {{ $status }}
                    </span>
                </div>
            </div>

            <button onclick="openDetail('{{ $id }}')" class="mt-4 w-full group py-2 px-3 bg-white border border-indigo-600 text-indigo-700 font-bold rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200 flex items-center justify-center gap-2 shadow-sm text-xs">
                <span class="group-hover:hidden flex items-center gap-2">
                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    Lihat Data Mentah
                </span>
                <span class="hidden group-hover:flex items-center gap-2">
                    Buka Detail
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </span>
            </button>
        </div>
    </div>
</div>
