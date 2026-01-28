@extends('layouts.main')

@section('container')
<div class="px-6 py-6 w-full font-sans text-slate-800">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Riwayat Peminjaman Saya</h1>
            <p class="text-slate-500 mt-1 text-sm">Daftar aset yang pernah atau sedang Anda pinjam.</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center shadow-sm">
            <div class="p-3 mr-4 text-indigo-500 bg-indigo-50 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pinjam</p>
                <p class="text-lg font-bold text-slate-700">{{ $stats['total_borrowings'] ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center shadow-sm">
            <div class="p-3 mr-4 text-emerald-500 bg-emerald-50 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Dipinjam</p>
                <p class="text-lg font-bold text-slate-700">{{ $stats['active_borrowings'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Aset</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Pinjam</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Rencana Kembali</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($borrowings as $borrowing)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $borrowing->asset->name ?? 'Aset dihapus' }}</p>
                                <p class="text-xs font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded inline-block mt-0.5">{{ $borrowing->asset->serial_number ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $borrowing->quantity }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            {{ $borrowing->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            {{ $borrowing->return_date ? $borrowing->return_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($borrowing->status == 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                    Pending Approval
                                </span>
                            @elseif($borrowing->status == 'approved' && !$borrowing->returned_at)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    Sedang Dipinjam
                                </span>
                            @elseif($borrowing->status == 'rejected')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                    Ditolak
                                </span>
                            @elseif($borrowing->returned_at)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    Selesai (Dikembalikan)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ ucfirst($borrowing->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('borrowing.show', $borrowing->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-lg transition-colors shadow-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <p class="font-medium text-lg text-slate-500">Belum ada riwayat</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($borrowings->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $borrowings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection