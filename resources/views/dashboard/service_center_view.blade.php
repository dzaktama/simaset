@extends('layouts.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Service Center</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau status perbaikan aset dan statistik maintenance.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- Card 1: Sedang Perbaikan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Sedang Perbaikan</h3>
                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $stats['on_process'] }}</span>
                    <span class="ml-2 text-sm text-gray-400">Unit</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50">
                <a href="{{ route('maintenances.index') }}" class="text-sm font-medium text-yellow-600 hover:text-yellow-700 flex items-center">
                    Lihat antrian
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        {{-- Card 2: Selesai Bulan Ini --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Selesai (Bulan Ini)</h3>
                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $stats['completed_month'] }}</span>
                    <span class="ml-2 text-sm text-gray-400">Unit</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Biaya (Bulan Ini) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Biaya (Bulan Ini)</h3>
                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($stats['cost_month'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        {{-- Card 4: Aset Butuh Perhatian --}}
         <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Aset Rusak</h3>
                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="text-3xl font-extrabold text-gray-900">{{ $stats['broken'] }}</span>
                    <span class="ml-2 text-sm text-gray-400">Unit</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Recent Activities Table --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">🛠️ Aktivitas Perbaikan Terkini</h3>
                <a href="{{ route('maintenances.index') }}" class="text-sm text-indigo-600 font-medium hover:text-indigo-800">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Aset</th>
                            <th class="px-6 py-3">Vendor</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Update Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentMaintenances as $maintain)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $maintain->asset->name }}
                                <div class="text-xs text-gray-500">{{ $maintain->asset->serial_number }}</div>
                            </td>
                             <td class="px-6 py-4">
                                {{ $maintain->vendor_name }}
                            </td>
                            <td class="px-6 py-4">
                                @if($maintain->status == 'on_process')
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800">PROSES</span>
                                @elseif($maintain->status == 'completed')
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800">SELESAI</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800">{{ strtoupper($maintain->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $maintain->updated_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center">Belum ada aktivitas maintenance.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Side Panel / Quick Links --}}
        <div class="space-y-6">
            <div class="bg-indigo-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                 <div class="relative z-10">
                    <h3 class="text-lg font-bold mb-2">Input Maintenance</h3>
                    <p class="text-indigo-100 text-sm mb-4">Ada aset rusak baru masuk? Segera input tiketnya.</p>
                    <a href="{{ route('maintenances.create') }}" class="inline-block bg-white text-indigo-600 font-bold py-2 px-4 rounded-lg hover:bg-indigo-50 transition shadow-md">
                        + Buat Tiket Baru
                    </a>
                </div>
                 <!-- Decorative pattern -->
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 rounded-full bg-white opacity-10 blur-xl"></div>
                <div class="absolute bottom-0 left-0 -ml-4 -mb-4 w-20 h-20 rounded-full bg-indigo-400 opacity-20 blur-lg"></div>
            </div>
        </div>
    </div>
</div>
@endsection
