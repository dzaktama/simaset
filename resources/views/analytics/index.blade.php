@extends('layouts.main')

@section('container')
<div class="w-full bg-gray-50 min-h-screen px-4 py-6 font-sans">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Pusat Data & Analisis  </h1>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mt-1">Monitoring Operasional Real-time</p>
        </div>
        
        {{-- GLOBAL DATE FILTER (TradingView Style) --}}
        <div class="flex flex-col sm:flex-row items-center gap-4 bg-white rounded-lg shadow-sm border border-gray-200 p-1.5">
            
            {{-- Manual Inputs (Compact) --}}
            <div class="flex items-center gap-2 px-2 border-r border-gray-100">
                <input type="date" id="global-start-date" class="border-none text-xs font-semibold text-gray-600 focus:ring-0 p-0 w-24">
                <span class="text-gray-300 text-xs">-</span>
                <input type="date" id="global-end-date" class="border-none text-xs font-semibold text-gray-600 focus:ring-0 p-0 w-24">
                <button onclick="applyDateFilter()" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-md p-1 ml-1 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
            </div>

            {{-- Presets --}}
            <div class="flex items-center gap-1 overflow-x-auto custom-scrollbar px-1">
                <button onclick="setPreset('1D')" id="btn-1D" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap">1H</button>
                <button onclick="setPreset('5D')" id="btn-5D" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap">5H</button>
                <button onclick="setPreset('1M')" id="btn-1M" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap">1B</button>
                <button onclick="setPreset('6M')" id="btn-6M" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap">6B</button>
                <button onclick="setPreset('YTD')" id="btn-YTD" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap">YTD</button>
                <button onclick="setPreset('1Y')" id="btn-1Y" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap">1T</button>
                <button onclick="setPreset('5Y')" id="btn-5Y" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap">5T</button>
                <button onclick="setPreset('ALL')" id="btn-ALL" class="preset-btn px-3 py-1.5 text-[11px] font-bold text-gray-500 hover:bg-gray-50 hover:text-indigo-600 rounded-md transition whitespace-nowrap">Semua</button>
            </div>
        </div>
    </div>

    {{-- KPI CARDS (ROW 1) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- KPI 1: Total Aset -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Total Aset</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-2xl font-extrabold text-gray-800">{{ number_format($totalAssets) }}</h3>
                    <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">Unit</span>
                </div>
            </div>
            <div class="p-2.5 bg-indigo-50 rounded-lg text-indigo-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
            </div>
        </div>

        <!-- KPI 2: Valuasi -->
        <div onclick="openDetail('valuation')" class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between cursor-pointer hover:shadow-md transition group">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider group-hover:text-indigo-600 transition">Est. Valuasi</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($totalValuation / 1000000, 1) }}M</h3>
                    <span class="text-[10px] font-bold text-gray-400">Total</span>
                </div>
            </div>
            <div class="p-2.5 bg-emerald-50 rounded-lg text-emerald-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- KPI 3: Kepatuhan -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Kepatuhan</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-2xl font-extrabold text-gray-800">{{ $complianceRate }}%</h3>
                    @if($complianceRate >= 90)
                        <span class="text-[10px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded">Baik</span>
                    @else
                        <span class="text-[10px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded">Perbaiki</span>
                    @endif
                </div>
            </div>
            <div class="p-2.5 bg-blue-50 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- KPI 4: Tiket Aktif -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Tiket Service</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-2xl font-extrabold text-gray-800">{{ $activeTickets }}</h3>
                    <span class="text-[10px] font-bold text-gray-400">Aktif</span>
                </div>
            </div>
            <div class="p-2.5 bg-orange-50 rounded-lg text-orange-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pb-12">
        
        {{-- ROW 2: MAJOR TRENDS (Span 2 Cols each) --}}
        
        {{-- 1. Tren Peminjaman --}}
        <div class="col-span-1 lg:col-span-2 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative group h-[320px] flex flex-col">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Tren Peminjaman</h3>
                    <p class="text-[10px] text-gray-400">Aktivitas Peminjaman & Pengembalian</p>
                </div>
                <!-- Controls -->
                <div class="flex items-center gap-1">
                    <button id="btn-month-borrowingTrend" onclick="updateChart('borrowingTrend', 'month')" class="px-2 py-0.5 text-[10px] font-bold rounded bg-gray-100 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition">Bulan</button>
                    <button onclick="openDetail('borrowingTrend')" class="p-1 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg></button>
                </div>
            </div>
            <div class="flex-1 w-full relative min-h-0">
                <canvas id="chart-borrowingTrend"></canvas>
            </div>
        </div>

        {{-- 2. Biaya Maintenance --}}
        <div class="col-span-1 lg:col-span-2 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative group h-[320px] flex flex-col">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Biaya Perbaikan</h3>
                    <p class="text-[10px] text-gray-400">Realisasi Biaya Service</p>
                </div>
                <div class="flex items-center gap-1">
                    <button id="btn-month-maintenanceCost" onclick="updateChart('maintenanceCost', 'month')" class="px-2 py-0.5 text-[10px] font-bold rounded bg-gray-100 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition">Bulan</button>
                    <button onclick="openDetail('maintenanceCost')" class="p-1 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg></button>
                </div>
            </div>
             <div class="flex-1 w-full relative min-h-0">
                <canvas id="chart-maintenanceCost"></canvas>
            </div>
        </div>

        {{-- ROW 3: DISTRIBUTION & PIES (Span 1 Col each) --}}

        {{-- 3. Top Users --}}
        <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Top User</h3>
                <button onclick="openDetail('topUsers')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
            </div>
            <div class="flex-1 w-full relative min-h-0">
                <canvas id="chart-topUsers"></canvas>
            </div>
        </div>

        {{-- 4. Return Compliance --}}
        <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Kepatuhan</h3>
                <button onclick="openDetail('returnCompliance')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
            </div>
            <div class="flex-1 w-full relative min-h-0 flex items-center justify-center">
                <canvas id="chart-returnCompliance"></canvas>
            </div>
        </div>

        {{-- 5. Dept Distribution --}}
        <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Distribusi</h3>
                <button onclick="openDetail('departmentDist')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
            </div>
            <div class="flex-1 w-full relative min-h-0 flex items-center justify-center">
                <canvas id="chart-departmentDist"></canvas>
            </div>
        </div>

        {{-- 6. Ticket Status --}}
        <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
             <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Status Tiket Perbaikan</h3>
                <button onclick="openDetail('ticketStats')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
            </div>
            <div class="flex-1 w-full relative min-h-0 flex items-center justify-center">
                <canvas id="chart-ticketStats"></canvas>
            </div>
        </div>

        {{-- ROW 4: BOTTOM METRICS --}}

        {{-- 7. Top Assets --}}
        <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Aset Populer</h3>
                <button onclick="openDetail('topAssets')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
            </div>
            <div class="flex-1 w-full relative min-h-0">
                <canvas id="chart-topAssets"></canvas>
            </div>
        </div>

        {{-- 8. Purchase Trend --}}
        <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
             <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Pembelian</h3>
                <button onclick="openDetail('purchaseTrend')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
            </div>
            <div class="flex-1 w-full relative min-h-0">
                <canvas id="chart-purchaseTrend"></canvas>
            </div>
        </div>

         {{-- 9. Asset Reliability --}}
        <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
             <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Reliabilitas</h3>
                <button onclick="openDetail('assetReliability')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
            </div>
            <div class="flex-1 w-full relative min-h-0">
                <canvas id="chart-assetReliability"></canvas>
            </div>
        </div>

        {{-- 10. Asset Aging --}}
         <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
             <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Umur Aset</h3>
                <button onclick="openDetail('assetAging')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
            </div>
            <div class="flex-1 w-full relative min-h-0">
                <canvas id="chart-assetAging"></canvas>
            </div>
        </div>

    </div>

    {{-- MODAL DETAIL (Reusable) --}}
    <div id="detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeDetailModal()"></div>
            
            <div class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-5xl relative z-10 flex flex-col max-h-[85vh]">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800" id="modal-title">Detail Data</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-0 overflow-y-auto flex-1 custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white sticky top-0 z-10 shadow-sm">
                            <tr id="modal-thead-tr"></tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100" id="modal-tbody">
                            <!-- JS Injected -->
                        </tbody>
                    </table>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition" onclick="closeDetailModal()">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartInstances = {};
    const chartConfig = {
        borrowingTrend: { type: 'line', multiAxis: true, smooth: true }, // Indigo
        maintenanceCost: { type: 'line', color: 'rgb(249, 115, 22)', fill: true, smooth: true }, // Orange
        topUsers: { type: 'bar', indexAxis: 'y', color: 'rgb(16, 185, 129)' }, // Emerald
        returnCompliance: { type: 'doughnut', cutout: '70%' },
        departmentDist: { type: 'pie' },
        ticketStats: { type: 'doughnut', cutout: '60%' },
        topAssets: { type: 'bar', indexAxis: 'y', color: 'rgb(236, 72, 153)' }, // Pink
        purchaseTrend: { type: 'line', color: 'rgb(59, 130, 246)', fill: false, smooth: true }, // Blue
        assetReliability: { type: 'bar', color: 'rgb(239, 68, 68)' }, // Red
        assetAging: { type: 'bar', color: 'rgb(107, 114, 128)' }, // Gray
    };

    const chartState = {};

    function initChartState(id) {
        if(!chartState[id]) {
            chartState[id] = { mode: 'month' };
        }
    }

    // --- GLOBAL DATE LOGIC ---
    function setPreset(preset) {
        const startInput = document.getElementById('global-start-date');
        const endInput = document.getElementById('global-end-date');
        const now = new Date();
        let start, end;

        // Reset times for cleaner calculations
        now.setHours(23, 59, 59, 999);
        end = new Date(now);

        switch(preset) {
            case '1D':
                start = new Date(now);
                break;
            case '5D':
                start = new Date(now);
                start.setDate(now.getDate() - 5);
                break;
            case '1M':
                start = new Date(now);
                start.setMonth(now.getMonth() - 1);
                break;
            case '6M':
                start = new Date(now);
                start.setMonth(now.getMonth() - 6);
                break;
            case 'YTD':
                start = new Date(now.getFullYear(), 0, 1);
                break;
            case '1Y':
                start = new Date(now);
                start.setFullYear(now.getFullYear() - 1);
                break;
            case '5Y':
                start = new Date(now);
                start.setFullYear(now.getFullYear() - 5);
                break;
            case 'ALL':
                start = new Date('2020-01-01'); // Assuming app start or sufficient past
                break;
            default: // month as default toggle
                start = new Date(now.getFullYear(), now.getMonth(), 1);
        }

        // Format to YYYY-MM-DD manually
        const formatDate = (date) => {
            const offset = date.getTimezoneOffset();
            date = new Date(date.getTime() - (offset*60*1000));
            return date.toISOString().split('T')[0];
        }

        if(startInput && endInput) {
            startInput.value = formatDate(start);
            endInput.value = formatDate(end);
            applyDateFilter();
            updateActiveButton(preset);
        }
    }

    function updateActiveButton(activeId) {
        const buttons = document.querySelectorAll('.preset-btn');
        buttons.forEach(btn => {
            // Reset to default style
            btn.classList.remove('bg-indigo-100', 'text-indigo-700');
            btn.classList.add('text-gray-500', 'hover:bg-gray-50', 'hover:text-indigo-600');
            
            // Check based on ID
            if (btn.id === `btn-${activeId}`) {
                 btn.classList.remove('text-gray-500', 'hover:bg-gray-50', 'hover:text-indigo-600');
                 btn.classList.add('bg-indigo-100', 'text-indigo-700');
            }
        });
    }

    function applyDateFilter() {
        const startDate = document.getElementById('global-start-date').value;
        const endDate = document.getElementById('global-end-date').value;

        // Auto-detect mode based on range
        let mode = 'month';
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            
            // Jika range < 60 hari, tampilkan per Hari. Jika lebih, per Bulan.
            if (diffDays <= 60) {
                mode = 'day';
            }
        }
        
        console.log(`Filtering: ${startDate} to ${endDate} (Auto Mode: ${mode})`);

        // Reload all charts
        Object.keys(chartConfig).forEach(key => {
            updateChart(key, mode, startDate, endDate);
        });
    }

    function updateChart(id, mode, startDate = null, endDate = null) {
        chartState[id].mode = mode;
        chartState[id].startDate = startDate;
        chartState[id].endDate = endDate;
        reloadChart(id);
    }

    async function reloadChart(key) {
        const config = chartConfig[key];
        const state = chartState[key];
        const canvas = document.getElementById(`chart-${key}`);
        
        if (!canvas) return;
        
        // Simple Loading
        if(chartInstances[key]) chartInstances[key].destroy();

        // Query params including Global Dates
        const params = new URLSearchParams({ 
            type: key, 
            mode: state.mode || 'month',
            startDate: state.startDate || '',
            endDate: state.endDate || ''
        });
        
        try {
            const res = await fetch(`{{ route('analytics.data') }}?${params.toString()}`);
            const json = await res.json();
            
            const options = {
                responsive: true,
                maintainAspectRatio: false,
                 layout: { padding: 10 },
                plugins: {
                    legend: { 
                        position: ['doughnut', 'pie'].includes(config.type) ? 'right' : 'bottom',
                        labels: { font: { size: 10, family: 'Inter', weight: '600' }, usePointStyle: true, boxWidth: 6 }
                    }
                },
                scales: {
                    x: { 
                        display: !['doughnut', 'pie'].includes(config.type),
                        grid: { display: false },
                        ticks: { font: { size: 9 }, color: '#9ca3af' }
                    },
                    y: { 
                        display: !['doughnut', 'pie'].includes(config.type),
                        grid: { borderDash: [4, 4], color: '#f3f4f6' },
                        ticks: { font: { size: 9 }, color: '#9ca3af' } 
                    }
                }
            };

            // PROPER Multi Axis Overrides
            if (config.multiAxis) {
                options.scales.y = {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { borderDash: [4, 4], color: '#f3f4f6' },
                    ticks: { font: { size: 9 }, color: '#9ca3af' }
                };
                options.scales.y1 = {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false }, 
                    ticks: { font: { size: 9 }, color: '#9ca3af' }
                };
                options.interaction = {
                    mode: 'index',
                    intersect: false,
                };
            }

            // End of options override
            const _dummy = {
            };
            
            // Dataset Construction
            let datasets;
            if (json.datasets) {
                datasets = json.datasets; // Multi series
            } else {
                 const color = config.color || '#6366f1';
                 const bgColor = config.fill ? color.replace('rgb', 'rgba').replace(')', ', 0.1)') : color;
                 
                 datasets = [{
                    label: json.label || 'Data',
                    data: json.data || [],
                    backgroundColor: ['doughnut', 'pie'].includes(config.type) ? ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'] : bgColor,
                    borderColor: ['doughnut', 'pie'].includes(config.type) ? '#ffffff' : color,
                    borderWidth: 2,
                    fill: config.fill || false,
                    tension: config.smooth ? 0.4 : 0,
                    borderRadius: 4
                }];
            }
            
            chartInstances[key] = new Chart(canvas, {
                type: config.type,
                data: { labels: json.labels || [], datasets: datasets },
                options: options
            });
            
        } catch (e) {
            console.error(e);
        }
    }

    async function openDetail(type) {
        const modal = document.getElementById('detail-modal');
        const titleEl = document.getElementById('modal-title');
        const thead = document.getElementById('modal-thead-tr');
        const tbody = document.getElementById('modal-tbody');
        const state = chartState[type] || {};
            
        modal.classList.remove('hidden');
        titleEl.innerText = 'Memuat Data...';
        tbody.innerHTML = '<tr><td colspan="100%" class="p-8 text-center text-gray-400">Loading...</td></tr>';
        
        try {
            const params = new URLSearchParams({ 
                type: type, 
                mode: state.mode || 'month', 
                startDate: state.startDate || '', 
                endDate: state.endDate || '' // Pass date filter to detail modal too
            });

            const res = await fetch(`{{ route('analytics.detail') }}?${params.toString()}`);
            const json = await res.json();
            
            titleEl.innerText = json.title;
            thead.innerHTML = json.headers.map(h => `<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">${h}</th>`).join('');
            
            if(!json.rows.length) {
                tbody.innerHTML = '<tr><td colspan="100%" class="p-8 text-center text-gray-400 italic">Tidak ada data.</td></tr>';
            } else {
                tbody.innerHTML = json.rows.map(row => `
                    <tr class="hover:bg-gray-50 transition">
                        ${row.map(c => `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${c}</td>`).join('')}
                    </tr>
                `).join('');
            }
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="100%" class="p-8 text-center text-red-500">Gagal memuat data.</td></tr>';
        }
    }

    function closeDetailModal() {
        document.getElementById('detail-modal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        Object.keys(chartConfig).forEach(key => {
            initChartState(key);
            // Don't call reloadChart here, let setPreset call it via applyDateFilter
        });
        // Default to "This Month" which will trigger load
        setPreset('month'); 
    });
</script>
@endsection
