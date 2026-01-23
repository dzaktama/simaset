@extends('layouts.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Service Center</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau status perbaikan aset dan statistik maintenance.</p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                Mode: Service Center
            </span>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- Card 1: Sedang Perbaikan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition group">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sedang Perbaikan</h3>
                    <div class="h-10 w-10 rounded-full bg-yellow-50 group-hover:bg-yellow-100 flex items-center justify-center text-yellow-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $stats['on_process'] }}</span>
                    <span class="ml-2 text-xs font-bold text-gray-400">Unit</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50">
                <a href="{{ route('maintenances.index') }}" class="text-xs font-bold text-yellow-600 hover:text-yellow-700 flex items-center">
                    Lihat Antrian &rarr;
                </a>
            </div>
        </div>

        {{-- Card 2: Selesai Bulan Ini --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition group">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Selesai (Bulan Ini)</h3>
                    <div class="h-10 w-10 rounded-full bg-green-50 group-hover:bg-green-100 flex items-center justify-center text-green-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $stats['completed_month'] }}</span>
                    <span class="ml-2 text-xs font-bold text-gray-400">Unit</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Biaya --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition group">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Biaya (Bulan Ini)</h3>
                    <div class="h-10 w-10 rounded-full bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center text-indigo-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($stats['cost_month'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        {{-- Card 4: Aset Rusak --}}
         <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition group">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aset Rusak</h3>
                    <div class="h-10 w-10 rounded-full bg-red-50 group-hover:bg-red-100 flex items-center justify-center text-red-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $stats['broken'] }}</span>
                    <span class="ml-2 text-xs font-bold text-gray-400">Unit</span>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTION BANNER --}}
    <div class="mb-8">
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl shadow-lg p-6 md:p-8 text-white flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
             
             {{-- Decorative Patterns --}}
             <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-10 blur-3xl"></div>
             <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-indigo-900 opacity-20 blur-2xl"></div>

             <div class="relative z-10 mb-4 md:mb-0 max-w-2xl">
                <h3 class="text-2xl font-bold mb-2">Input Maintenance Baru</h3>
                <p class="text-indigo-100 text-sm md:text-base">Temukan aset yang rusak atau butuh perbaikan? Segera buat tiket maintenance baru untuk memulai proses perbaikan dan pelacakan.</p>
            </div>
            <div class="relative z-10 w-full md:w-auto">
                <a href="{{ route('maintenances.create') }}" class="block w-full text-center bg-white text-indigo-700 font-bold py-3 px-8 rounded-lg shadow-md hover:bg-gray-50 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    + Buat Tiket Service
                </a>
            </div>
        </div>
    </div>

    {{-- Activity Log Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Log Aktivitas</h3>
                    <p class="text-xs text-gray-500">Memantau pergerakan dan update tiket perbaikan terkini.</p>
                </div>
            </div>
            <a href="{{ route('maintenances.index') }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800 transition bg-indigo-50 px-3 py-1.5 rounded-full">
                Lihat Semua
                <svg class="ml-1 w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-bold tracking-wider w-40">Waktu</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Aset</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Aktivitas / Keterangan</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                        <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentMaintenances as $maintain)
                    <tr class="bg-white hover:bg-gray-50/50 transition">
                        {{-- TIMESTAMP COL --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ $maintain->updated_at->format('d M Y') }}</span>
                                <span class="text-xl font-mono font-bold text-indigo-900">{{ $maintain->updated_at->format('H:i') }}</span>
                            </div>
                        </td>

                        {{-- ASSET COL --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-gray-100 flex-shrink-0 overflow-hidden border border-gray-200">
                                    <img src="{{ $maintain->asset->image ? asset('storage/' . $maintain->asset->image) : 'https://placehold.co/100?text=IMG' }}" class="h-full w-full object-cover">
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-sm">{{ $maintain->asset->name }}</div>
                                    <div class="text-[10px] text-gray-500 font-mono mt-0.5 uppercase bg-gray-100 px-1.5 py-0.5 rounded w-fit">{{ $maintain->asset->serial_number }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- ACTIVITY COL --}}
                        <td class="px-6 py-4">
                            @if($maintain->created_at->diffInMinutes($maintain->updated_at) < 5 && $maintain->status == 'on_process')
                                <div class="text-indigo-700 font-bold flex items-center gap-1.5 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                    Tiket Baru Dibuat
                                </div>
                                <div class="text-xs text-gray-500">Prioritas tiket diset ke level sistem.</div>
                            @elseif($maintain->status == 'completed')
                                <div class="text-green-700 font-bold flex items-center gap-1.5 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    Perbaikan Selesai
                                </div>
                                <div class="text-xs text-gray-500 line-clamp-1">Solusi: {{ $maintain->resolution_notes ?? '-' }}</div>
                            @elseif($maintain->status == 'cancelled')
                                <div class="text-red-700 font-bold flex items-center gap-1.5 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    Tiket Dibatalkan
                                </div>
                            @else
                                <div class="text-gray-800 font-bold flex items-center gap-1.5 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                    Proses Pengerjaan
                                </div>
                                <div class="text-xs text-gray-500">Ditangani oleh: <span class="font-bold">{{ $maintain->vendor_name }}</span></div>
                            @endif
                        </td>

                        {{-- STATUS COL --}}
                        <td class="px-6 py-4">
                            @if($maintain->status == 'on_process')
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200 uppercase tracking-wide">
                                    ON PROCESS
                                </span>
                            @elseif($maintain->status == 'completed')
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 border border-green-200 uppercase tracking-wide">
                                    COMPLETED
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200 uppercase tracking-wide">
                                    {{ strtoupper($maintain->status) }}
                                </span>
                            @endif
                        </td>

                        {{-- ACTION COL --}}
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('maintenances.show', $maintain->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-white text-gray-700 rounded-lg border border-gray-200 hover:bg-gray-50 hover:text-indigo-600 transition shadow-sm text-xs font-bold" title="Lihat Detail">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center bg-gray-50">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-medium">Belum ada aktivitas log tercatat.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
