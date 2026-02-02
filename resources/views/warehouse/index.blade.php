@extends('layouts.main')

@section('container')
<div class="w-full px-6 py-8">
    
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Manajemen Gudang</h2>
            <p class="text-sm text-gray-500 mt-1">Pantau distribusi aset dan lakukan perpindahan (mutasi) antar lokasi.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('warehouse.history') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition shadow-sm">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Riwayat Perpindahan
            </a>
            <a href="{{ route('warehouse.createMove') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/30">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                Mutasi Barang (Pindah)
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kiri: Statistik Lokasi (2 Kolom) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Cards Ringkasan --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Lokasi Terdata</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $locationStats->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-50 text-indigo-600 mr-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Aset Dilacak</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $locationStats->sum('total') }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Sebaran Lokasi --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Distribusi Aset per Lokasi</h3>
                    <a href="{{ route('assets.map') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center">
                        Lihat Peta
                        <svg class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-3">Nama Lokasi</th>
                                <th class="px-6 py-3 text-center">Jumlah Aset</th>
                                <th class="px-6 py-3 text-right">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($locationStats as $stat)
                            @php
                                $percent = ($stat->total / $locationStats->sum('total')) * 100;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-3 font-medium text-gray-700">{{ $stat->location }}</td>
                                <td class="px-6 py-3 text-center font-bold text-gray-900">{{ $stat->total }}</td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-xs text-gray-500">{{ round($percent, 1) }}%</span>
                                        <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kanan: Riwayat Perpindahan (1 Kolom) --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Mutasi Terakhir</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentMovements as $history)
                    <div class="p-4 hover:bg-gray-50/80 transition group">
                        <div class="flex items-start gap-3">
                            <div class="mt-1">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800 truncate">{{ $history->asset->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $history->notes }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-medium truncate max-w-[80px]">
                                        {{ $history->user->name ?? 'System' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">
                                        {{ $history->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-400">
                        <p class="text-sm">Belum ada aktivitas mutasi.</p>
                    </div>
                    @endforelse
                </div>
                <div class="p-3 border-t border-gray-100 text-center">
                    <a href="{{ route('warehouse.history') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wide">Lihat Semua History</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
