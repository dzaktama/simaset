@extends('layouts.main')

@section('container')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Perbaikan & Pemeliharaan (Maintenance)</h1>
        @if(auth()->user()->role?->slug == 'admin' || auth()->user()->role?->slug == 'super_admin' || auth()->user()->role?->slug == 'service_center')
        <a href="{{ route('maintenances.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium">
            + Input Perbaikan Baru
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masalah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Mulai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($maintenances as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ $item->asset->name }}</div>
                            <div class="text-xs text-gray-500">{{ $item->asset->serial_number }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $item->vendor_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $item->problem_description }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status == 'on_process')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">PROSES</span>
                            @elseif($item->status == 'completed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">SELESAI</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">BATAL</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                                <a href="{{ route('maintenances.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold bg-indigo-50 px-3 py-1 rounded transition hover:bg-indigo-100">
                                    Detail
                                </a>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">Belum ada riwayat perbaikan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $maintenances->links() }}
        </div>
    </div>
</div>
@endsection
