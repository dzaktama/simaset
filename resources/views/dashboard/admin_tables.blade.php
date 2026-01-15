<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Permintaan Pending --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Permintaan Pending</h3>
            <a href="{{ route('borrowing.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Aset</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentRequests as $req)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $req->user->name }}</td>
                        <td class="px-6 py-4">{{ $req->asset->name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $req->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-bold text-yellow-700 bg-yellow-100 rounded-full">Menunggu</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Tidak ada permintaan baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Aktivitas Terbaru --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Aktivitas Terbaru</h3>
        <div class="space-y-4">
            @forelse($activities as $act)
            <div class="flex gap-3">
                <div class="mt-1">
                    <div class="w-2 h-2 rounded-full bg-indigo-500 ring-4 ring-indigo-50"></div>
                </div>
                <div>
                    <p class="text-sm text-gray-800">
                        <span class="font-bold">{{ $act->user->name ?? 'System' }}</span> 
                        {{ $act->action }} 
                        <span class="font-medium text-indigo-600">{{ $act->asset->name ?? 'Aset' }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $act->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-400 text-sm">Belum ada aktivitas.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- LOG AKTIVITAS LENGKAP --}}
@php
    $allActivities = \App\Models\AssetHistory::with(['user', 'asset'])->latest()->get();
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 mt-8">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Log Aktivitas Lengkap</h3>
        <span class="text-xs text-gray-500">Semua riwayat aktivitas sistem</span>
    </div>
    
    <div class="overflow-y-auto custom-scrollbar max-h-[500px]">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($allActivities as $log)
            <li class="px-6 py-4 hover:bg-gray-50 transition">
                <div class="flex space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold uppercase">
                            {{ substr($log->user->name ?? 'S', 0, 1) }}
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'Sistem' }}</p>
                        <p class="text-xs text-gray-600">
                            <span class="uppercase font-bold text-indigo-600">{{ str_replace('_', ' ', $log->action) }}</span> 
                            <span class="font-medium text-gray-900">{{ $log->asset->name ?? 'Unknown Asset' }}</span>
                            @if($log->notes)
                                <span class="text-gray-500 italic"> - {{ $log->notes }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <div class="text-xs text-gray-500 font-bold">
                            {{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Jakarta')->format('H:i') }} WIB
                        </div>
                        <div class="text-[10px] text-gray-400">
                            {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y') }}
                        </div>
                    </div>
                </div>
            </li>
            @empty
            <li class="px-6 py-4 text-center text-gray-500 italic">Belum ada aktivitas.</li>
            @endforelse
        </ul>
    </div>
</div>