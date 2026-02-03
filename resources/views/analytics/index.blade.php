@extends('layouts.main')

@section('container')
<div class="w-full bg-gray-50 min-h-screen px-4 py-6 font-sans">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-sans font-extrabold text-gray-800 tracking-tight">Pusat Data & Analisis  </h1>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mt-1 font-sans">Monitoring Operasional Real-time</p>
        </div>
        
        @include('analytics.partials.filter-bar')
    </div>

    @include('analytics.partials.kpi-cards')

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pb-12">
        
        {{-- ROW 2: MAJOR TRENDS (Span 2 Cols each) --}}
        
        {{-- 1. Tren Peminjaman --}}
        <div class="col-span-1 lg:col-span-2 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative group h-[320px] flex flex-col">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Grafik Peminjaman</h3>
                    <p class="text-[10px] text-gray-400">Aktivitas Peminjaman & Pengembalian</p>
                </div>
                <!-- Controls -->
                <div class="flex items-center gap-1">
                    <button onclick="reloadChart('borrowingTrend')" title="Refresh" class="p-1 text-gray-400 hover:text-gray-600 rounded hover:bg-gray-100 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></button>
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
                    <h3 class="text-sm font-bold text-gray-800">Grafik Biaya Perbaikan</h3>
                    <p class="text-[10px] text-gray-400">Realisasi Biaya Service</p>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="reloadChart('maintenanceCost')" title="Refresh" class="p-1 text-gray-400 hover:text-gray-600 rounded hover:bg-gray-100 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></button>
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
                <div class="flex items-center gap-2">
                    <button onclick="reloadChart('topUsers')" title="Refresh" class="text-gray-400 hover:text-gray-600"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></button>
                    <button onclick="openDetail('topUsers')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
                </div>
            </div>
            <div class="flex-1 w-full relative min-h-0">
                <canvas id="chart-topUsers"></canvas>
            </div>
        </div>

        {{-- 4. Return Compliance --}}
        <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Perbaikan Selesai</h3>
                 <div class="flex items-center gap-2">
                    <button onclick="reloadChart('returnCompliance')" title="Refresh" class="text-gray-400 hover:text-gray-600"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></button>
                    <button onclick="openDetail('returnCompliance')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
                </div>
            </div>
            <div class="flex-1 w-full relative min-h-0 flex items-center justify-center">
                <canvas id="chart-returnCompliance"></canvas>
            </div>
        </div>

        {{-- 5. Dept Distribution --}}
        <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Distribusi</h3>
                <div class="flex items-center gap-2">
                    <button onclick="reloadChart('departmentDist')" title="Refresh" class="text-gray-400 hover:text-gray-600"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></button>
                    <button onclick="openDetail('departmentDist')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
                </div>
            </div>
            <div class="flex-1 w-full relative min-h-0 flex items-center justify-center">
                <canvas id="chart-departmentDist"></canvas>
            </div>
        </div>

        {{-- 6. Ticket Status --}}
        <div class="col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative h-[250px] flex flex-col">
             <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-800">Status Tiket Perbaikan</h3>
                 <div class="flex items-center gap-2">
                    <button onclick="reloadChart('ticketStats')" title="Refresh" class="text-gray-400 hover:text-gray-600"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></button>
                    <button onclick="openDetail('ticketStats')" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</button>
                </div>
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

    @include('analytics.partials.detail-modal')

</div>

@include('analytics.partials.scripts')
@endsection
