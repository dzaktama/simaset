@extends('layouts.main')

@section('container')
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                {{ $title }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Selamat datang, <span class="font-semibold text-indigo-600">{{ auth()->user()->name }}</span>.
                @if(auth()->user()->role == 'admin')
                    Pantau kondisi aset perusahaan hari ini.
                @else
                    Kelola aset dan permintaan Anda di sini.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600 shadow-sm">
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
        </div>
    </div>

    {{-- TAMPILAN KHUSUS ADMIN --}}
    @if(auth()->user()->role == 'admin')
    
        {{-- Alert Request Pending --}}
        @if(isset($stats['pending_requests']) && $stats['pending_requests'] > 0)
        <div class="mb-8 border-l-4 border-yellow-400 bg-yellow-50 p-4 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <span class="font-bold">Perhatian!</span> Ada {{ $stats['pending_requests'] }} permintaan aset menunggu persetujuan.
                        <a href="#request-section" class="font-medium underline hover:text-yellow-600">Lihat detail</a>
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Stats Grid --}}
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            
            {{-- Card Total --}}
            <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-md">
                <dt>
                    <div class="absolute rounded-md bg-indigo-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Aset</p>
                </dt>
                <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">Unit</p>
                </dd>
            </div>

            {{-- Card Tersedia --}}
            <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-md">
                <dt>
                    <div class="absolute rounded-md bg-green-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Tersedia (Gudang)</p>
                </dt>
                <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['available'] }}</p>
                    <p class="ml-2 text-sm text-gray-500">Siap pakai</p>
                </dd>
            </div>

            {{-- Card Dipinjam --}}
            <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-md">
                <dt>
                    <div class="absolute rounded-md bg-blue-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Sedang Dipinjam</p>
                </dt>
                <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['deployed'] }}</p>
                    <p class="ml-2 text-sm text-gray-500">Oleh karyawan</p>
                </dd>
            </div>

            {{-- Card Maintenance --}}
            <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-md">
                <dt>
                    <div class="absolute rounded-md bg-red-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Maintenance / Rusak</p>
                </dt>
                <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['maintenance'] }}</p>
                    <p class="ml-2 text-sm text-gray-500">Perlu perbaikan</p>
                </dd>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            {{-- Section Request Approval --}}
            <div id="request-section" class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Permintaan Masuk</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500">Karyawan</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500">Barang</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($recentRequests as $req)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $req->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($req->request_date)->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $req->asset->name }}
                                    <span class="block text-xs text-gray-400">{{ $req->asset->serial_number }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <form action="/requests/{{ $req->id }}/approve" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded p-1 text-green-600 hover:bg-green-50 hover:text-green-900" title="Terima">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                        <form action="/requests/{{ $req->id }}/reject" method="POST" onsubmit="return confirm('Tolak permintaan ini?')">
                                            @csrf
                                            <button type="submit" class="rounded p-1 text-red-600 hover:bg-red-50 hover:text-red-900" title="Tolak">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">Tidak ada permintaan baru.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Section Recent Activities (Log History) --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Aktivitas Terbaru</h3>
                </div>
                <div class="px-6 py-4">
                    <ul role="list" class="-mb-8">
                        @forelse($activities as $log)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 ring-8 ring-white">
                                            <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                        <div>
                                            <p class="text-sm text-gray-500">
                                                <span class="font-medium text-gray-900">{{ $log->user->name ?? 'Sistem' }}</span> 
                                                {{ $log->action }} 
                                                <span class="font-medium text-gray-900">{{ $log->asset->name }}</span>
                                            </p>
                                        </div>
                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                            {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="text-sm text-gray-500 italic">Belum ada riwayat aktivitas.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

    {{-- TAMPILAN KHUSUS KARYAWAN (USER) --}}
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            {{-- Card Aset Saya --}}
            <div class="overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Aset yang Anda Pegang</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $myAssetsCount }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="/my-assets" class="font-medium text-indigo-600 hover:text-indigo-500">Lihat semua aset &rarr;</a>
                    </div>
                </div>
            </div>

            {{-- Card Shortcut Pinjam --}}
            <div class="overflow-hidden rounded-lg bg-indigo-600 shadow hover:bg-indigo-700 transition">
                <div class="px-4 py-5 sm:p-6 flex flex-col items-center justify-center text-center h-full">
                    <svg class="h-10 w-10 text-indigo-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <h3 class="text-lg font-medium text-white">Pinjam Aset Baru</h3>
                    <p class="mt-1 text-sm text-indigo-100">Butuh peralatan kerja?</p>
                </div>
                <a href="/assets" class="block bg-indigo-800 px-4 py-4 sm:px-6 text-center text-sm font-medium text-white hover:bg-indigo-900">
                    Cari Aset Tersedia &rarr;
                </a>
            </div>
        </div>

        {{-- Status Request User --}}
        <div class="mt-8">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Status Permintaan Anda</h3>
            <div class="overflow-hidden bg-white shadow sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    @forelse($myRequests as $req)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <p class="truncate text-sm font-medium text-indigo-600">{{ $req->asset->name }}</p>
                                <div class="ml-2 flex flex-shrink-0">
                                    @php
                                        $colors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'returned' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $color = $colors[$req->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <p class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $color }}">
                                        {{ ucfirst($req->status) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                                        </svg>
                                        Diajukan pada {{ \Carbon\Carbon::parse($req->request_date)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="px-4 py-4 text-center text-sm text-gray-500">Belum ada permintaan yang diajukan.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif

@endsection