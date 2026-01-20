@extends('layouts.main')

@section('container')
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700">
        Riwayat Peminjaman Saya
    </h2>

    {{-- Stats Cards --}}
    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
            <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Total Pinjam</p>
                <p class="text-lg font-semibold text-gray-700">{{ $stats['total_borrowings'] ?? 0 }}</p>
            </div>
        </div>
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
            <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Sedang Dipinjam</p>
                <p class="text-lg font-semibold text-gray-700">{{ $stats['active_borrowings'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="w-full overflow-hidden rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">Aset</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Tgl Pinjam</th>
                        <th class="px-4 py-3">Rencana Kembali</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($borrowings as $borrowing)
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">
                            <div class="flex items-center text-sm">
                                <div>
                                    <p class="font-semibold">{{ $borrowing->asset->name ?? 'Aset dihapus' }}</p>
                                    <p class="text-xs text-gray-600">{{ $borrowing->asset->serial_number ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $borrowing->quantity }}</td>
                        <td class="px-4 py-3 text-sm">
                            {{ $borrowing->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            {{ $borrowing->return_date ? $borrowing->return_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            @if($borrowing->status == 'pending')
                                <span class="px-2 py-1 font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                            @elseif($borrowing->status == 'approved' && !$borrowing->returned_at)
                                <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Dipinjam</span>
                            @elseif($borrowing->status == 'rejected')
                                <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Ditolak</span>
                            @elseif($borrowing->returned_at)
                                <span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-200 rounded-full">Selesai</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('borrowing.show', $borrowing->id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">Belum ada riwayat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $borrowings->links() }}
        </div>
    </div>
</div>
@endsection