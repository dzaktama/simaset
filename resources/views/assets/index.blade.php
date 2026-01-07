@extends('layouts.main')

@section('container')
<div class="sm:flex sm:items-center sm:justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Inventaris Aset IT</h1>
        <p class="mt-2 text-sm text-gray-700">Daftar lengkap seluruh perangkat keras dan peralatan inventaris perusahaan.</p>
    </div>
    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <a href="/assets/create" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
            + Tambah Aset
        </a>
    </div>
</div>

<div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
    <form action="/assets" method="GET">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="search" name="search" value="{{ request('search') }}" class="block w-full p-3 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cari berdasarkan nama barang atau serial number...">
            <button type="submit" class="absolute right-2.5 bottom-2.5 bg-indigo-700 text-white font-medium rounded-lg text-sm px-4 py-1 hover:bg-indigo-800">Cari</button>
        </div>
    </form>
</div>

<div class="overflow-hidden bg-white shadow sm:rounded-lg border border-gray-200">
    <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nama Barang</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Serial Number</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Pemegang</th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Actions</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @foreach ($assets as $asset)
            <tr>
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                    <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0">
                            @if($asset->image)
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $asset->image) }}" alt="">
                            @else
                                <img class="h-10 w-10 rounded-full bg-gray-100" src="https://ui-avatars.com/api/?name={{ urlencode($asset->name) }}&background=random" alt="">
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="font-medium text-gray-900">{{ $asset->name }}</div>
                            <div class="text-gray-500 text-xs">{{ $asset->purchase_date ? $asset->purchase_date->format('Y') : '-' }}</div>
                        </div>
                    </div>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-mono">{{ $asset->serial_number }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                    @if($asset->status == 'available')
                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Tersedia</span>
                    @elseif($asset->status == 'deployed')
                        <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">Digunakan</span>
                    @elseif($asset->status == 'maintenance')
                        <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Servis</span>
                    @else
                        <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">Rusak</span>
                    @endif
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ $asset->holder ? $asset->holder->name : '-' }}
                </td>
                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                    <a href="/assets/{{ $asset->id }}/edit" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                    <form action="/assets/{{ $asset->id }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus aset ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-200">
        {{ $assets->links() }}
    </div>
</div>
@endsection