<div class="space-y-8">
    
    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Tiket</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl text-gray-600">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" /></svg>
            </div>
        </div>

        {{-- Menunggu --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-yellow-500">Menunggu</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['maintenance'] }}</p>
            </div>
            <div class="p-3 bg-yellow-50 rounded-xl text-yellow-600">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        {{-- Sedang Proses --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-blue-500">Sedang Dikerjakan</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['deployed'] }}</p>
            </div>
            <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
            </div>
        </div>

        {{-- Selesai --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-green-500">Selesai</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['available'] }}</p>
            </div>
            <div class="p-3 bg-green-50 rounded-xl text-green-600">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>

    {{-- RECENT MAINTENANCE TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Aktivitas Perbaikan Terbaru</h3>
                <p class="text-xs text-gray-500">5 Tiket terakhir yang masuk sistem.</p>
            </div>
            <a href="{{ route('maintenances.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 transition">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4 font-semibold">Aset</th>
                        <th class="px-6 py-4 font-semibold">Pelapor</th>
                        <th class="px-6 py-4 font-semibold">Masalah</th>
                        <th class="px-6 py-4 font-semibold">Tanggal Masuk</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($listMaintenance as $maintenance)
                    <tr class="hover:bg-gray-50/50 transition duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 font-bold text-xs overflow-hidden">
                                     @if($maintenance->asset->image)
                                        <img src="{{ asset('storage/'.$maintenance->asset->image) }}" class="h-full w-full object-cover">
                                     @else
                                        IMG
                                     @endif
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $maintenance->asset->name }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ $maintenance->asset->serial_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                    {{ substr($maintenance->user->name ?? '?', 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-700">{{ $maintenance->user->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 max-w-xs truncate text-gray-600" title="{{ $maintenance->problem_description }}">
                            {{ Str::limit($maintenance->problem_description, 40) }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 font-medium">
                            {{ date('d M Y', strtotime($maintenance->start_date)) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'Menunggu',
                                    'in_progress' => 'Proses',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Batal',
                                ];
                                $st = $maintenance->status;
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $statusClasses[$st] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$st] ?? $st }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('maintenances.show', $maintenance->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 hover:text-indigo-600 transition shadow-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" /></svg>
                            <p class="text-sm font-medium">Belum ada aktivitas maintenance.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
