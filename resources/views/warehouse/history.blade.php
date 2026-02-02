@extends('layouts.main')

@section('container')
<div class="w-full px-6 py-8">
    
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Riwayat Perpindahan Aset</h2>
            <p class="text-sm text-gray-500 mt-1">Audit log lengkap semua aktivitas pemindahan (mutasi) aset.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('warehouse.index') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all duration-200">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </a>
            <a href="{{ route('warehouse.createMove') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/30">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                Mutasi Baru
            </a>
        </div>
    </div>

    {{-- Main Content: Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4 font-semibold">Waktu</th>
                        <th class="px-6 py-4 font-semibold">Aset</th>
                        <th class="px-6 py-4 font-semibold">Pegawai / Eksekutor</th>
                        <th class="px-6 py-4 font-semibold">Detail Mutasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($movements as $history)
                    <tr class="hover:bg-gray-50/50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ $history->created_at->format('d M Y, H:i') }}
                            <br>
                            <span class="text-xs text-gray-400">{{ $history->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-500 font-bold text-xs">
                                     {{ substr($history->asset->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $history->asset->name }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ $history->asset->serial_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold">
                                    {{ substr($history->user->name ?? '?', 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-700">{{ $history->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            {{ $history->notes }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            <p class="text-sm font-medium">Belum ada riwayat mutasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection
