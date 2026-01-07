@extends('layouts.main')

@section('container')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Daftar Aset IT</h2>
        <p class="text-sm text-gray-500">
            @if(auth()->user()->role == 'admin')
                Kelola seluruh data inventaris perusahaan.
            @else
                Pilih aset yang tersedia untuk dipinjam.
            @endif
        </p>
    </div>
    
    {{-- Action Buttons --}}
    @if(auth()->user()->role == 'admin')
    <div class="flex gap-2">
        <a href="{{ route('report.assets') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
            Cetak PDF
        </a>
        <a href="/assets/create" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
            + Tambah Aset
        </a>
    </div>
    @endif
</div>

{{-- Search Bar --}}
<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
    <form action="/assets" method="GET">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Cari nama barang atau serial number..." value="{{ request('search') }}">
        </div>
    </form>
</div>

<div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aset Info</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi / Holder</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assets as $asset)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            {{-- Icon Gambar --}}
                            <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                                @if($asset->image)
                                    <img src="{{ asset('storage/' . $asset->image) }}" class="h-10 w-10 rounded-lg object-cover">
                                @else
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $asset->name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $asset->serial_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $colors = [
                                'available' => 'bg-green-100 text-green-800',
                                'deployed' => 'bg-blue-100 text-blue-800',
                                'maintenance' => 'bg-yellow-100 text-yellow-800',
                                'broken' => 'bg-red-100 text-red-800',
                            ];
                            $statusLabel = [
                                'available' => 'Tersedia',
                                'deployed' => 'Dipakai',
                                'maintenance' => 'Maintenance',
                                'broken' => 'Rusak',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$asset->status] ?? 'bg-gray-100' }}">
                            {{ $statusLabel[$asset->status] ?? $asset->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $asset->holder->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if(auth()->user()->role == 'admin')
                            <a href="/assets/{{ $asset->id }}/edit" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="/assets/{{ $asset->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus aset ini?')">
                                @method('delete') @csrf
                                <button class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        @else
                            {{-- TOMBOL PINJAM UNTUK KARYAWAN --}}
                            <form action="/assets/{{ $asset->id }}/request" method="POST">
                                @csrf
                                <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-xs font-bold shadow">
                                    PINJAM
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">Tidak ada data aset.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
        {{ $assets->links() }}
    </div>
</div>
@endsection