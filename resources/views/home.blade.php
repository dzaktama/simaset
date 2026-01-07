@extends('layouts.main')

@section('container')
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Dashboard Overview
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Halo, <span class="font-semibold text-indigo-600">{{ auth()->user()->name }}</span>! Berikut ringkasan aset hari ini.
            </p>
        </div>
        <div class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200 shadow-sm">
            📅 {{ now()->isoFormat('dddd, D MMMM Y') }}
        </div>
    </div>

    @if(auth()->user()->role == 'admin' && isset($stats['pending_requests']) && $stats['pending_requests'] > 0)
    <div class="rounded-md bg-yellow-50 p-4 mb-8 border-l-4 border-yellow-400 shadow-sm animate-pulse">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                </svg>
            </div>
            <div class="ml-3 flex-1 md:flex md:justify-between">
                <p class="text-sm text-yellow-700 font-medium">
                    Perhatian! Ada <span class="font-bold text-yellow-800">{{ $stats['pending_requests'] }} permintaan peminjaman aset</span> yang menunggu persetujuan Anda.
                </p>
                <p class="mt-3 text-sm md:ml-6 md:mt-0">
                    <a href="#request-section" class="whitespace-nowrap font-bold text-yellow-700 hover:text-yellow-600 hover:underline">
                        Lihat Detail &rarr;
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        
        <div class="bg-white overflow-hidden shadow rounded-lg border-b-4 border-indigo-500 hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Aset Fisik</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-b-4 border-green-500 hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tersedia (Gudang)</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['available'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-b-4 border-blue-500 hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Sedang Dipinjam</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['deployed'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-b-4 border-red-500 hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Rusak / Servis</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['maintenance'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role == 'admin')
    <div id="request-section" class="bg-white shadow-lg rounded-lg mb-8 overflow-hidden border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg leading-6 font-bold text-gray-900 flex items-center gap-2">
                    📥 Permintaan Peminjaman Masuk
                </h3>
                <p class="text-xs text-gray-500 mt-1">Daftar permintaan aset yang diajukan karyawan.</p>
            </div>
            @if(isset($stats['pending_requests']) && $stats['pending_requests'] > 0)
                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800 animate-pulse">
                    {{ $stats['pending_requests'] }} Perlu Tindakan
                </span>
            @endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Barang Diminta</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Request</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Konfirmasi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentRequests as $req)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                    {{ substr($req->user->name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $req->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $req->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $req->asset->name }}</div>
                            <div class="text-xs text-gray-500 font-mono bg-gray-100 px-1 rounded inline-block">SN: {{ $req->asset->serial_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($req->request_date)->format('d M Y') }}
                            <br>
                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($req->request_date)->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-800 border border-yellow-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                Menunggu Approval
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <form action="/requests/{{ $req->id }}/approve" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 bg-green-50 text-green-700 hover:bg-green-100 hover:text-green-800 px-3 py-1.5 rounded-md text-xs font-bold border border-green-200 transition-colors" title="Setujui Peminjaman">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Terima
                                    </button>
                                </form>

                                <form action="/requests/{{ $req->id }}/reject" method="POST" onsubmit="return confirm('Yakin ingin menolak permintaan ini?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 bg-red-50 text-red-700 hover:bg-red-100 hover:text-red-800 px-3 py-1.5 rounded-md text-xs font-bold border border-red-200 transition-colors" title="Tolak Peminjaman">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic bg-gray-50">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="block mt-2 font-medium">Tidak ada permintaan peminjaman baru.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($recentRequests->count() > 0)
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 text-right">
            <span class="text-xs text-gray-500">Menampilkan 5 permintaan terbaru</span>
        </div>
        @endif
    </div>
    @endif

@endsection