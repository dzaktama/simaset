{{-- KPI CARDS (ROW 1) --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <!-- KPI 1: Total Aset -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider font-sans">Total Aset</p>
            <div class="flex items-baseline gap-2 mt-1">
                <h3 class="text-2xl font-extrabold text-gray-800 font-sans">{{ number_format($totalAssets) }}</h3>
                <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded font-sans">Unit</span>
            </div>
        </div>
        <div class="p-2.5 bg-indigo-50 rounded-lg text-indigo-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
        </div>
    </div>

    <!-- KPI 2: Valuasi -->
    <div onclick="openDetail('valuation')" class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between cursor-pointer hover:shadow-md transition group">
        <div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider group-hover:text-indigo-600 transition font-sans">Est. Valuasi</p>
            <div class="flex items-baseline gap-2 mt-1">
                <h3 class="text-2xl font-extrabold text-gray-800 font-sans">Rp {{ number_format($totalValuation / 1000000, 1) }}M</h3>
                <span class="text-[10px] font-bold text-gray-400 font-sans">Total</span>
            </div>
        </div>
        <div class="p-2.5 bg-emerald-50 rounded-lg text-emerald-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
    </div>

    <!-- KPI 3: Kepatuhan -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider font-sans">Perbaikan Selesai</p>
            <div class="flex items-baseline gap-2 mt-1">
                <h3 class="text-2xl font-extrabold text-gray-800 font-sans">{{ $complianceRate }}%</h3>
                @if($complianceRate >= 90)
                    <span class="text-[10px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded font-sans">Baik</span>
                @else
                    <span class="text-[10px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded font-sans">Perbaiki</span>
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
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider font-sans">Tiket Service</p>
            <div class="flex items-baseline gap-2 mt-1">
                <h3 class="text-2xl font-extrabold text-gray-800 font-sans">{{ $activeTickets }}</h3>
                <span class="text-[10px] font-bold text-gray-400 font-sans">Aktif</span>
            </div>
        </div>
        <div class="p-2.5 bg-orange-50 rounded-lg text-orange-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 012 2v3a2 2 0 000 4v3a2 2 0 01-2 2H5a2 2 0 01-2-2v-3a2 2 0 000-4V7a2 2 0 012-2z" />
            </svg>
        </div>
    </div>
</div>
