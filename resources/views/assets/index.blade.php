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
        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
            
            @if(auth()->user()->role == 'admin')
                <a href="/assets/{{ $asset->id }}/edit" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                <form action="/assets/{{ $asset->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus aset?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                </form>
            
            @else
                @if($asset->status == 'available')
                    <form action="/assets/{{ $asset->id }}/request" method="POST" class="inline-block" onsubmit="return confirm('Ajukan peminjaman untuk barang ini?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none">
                            Pinjam Aset Ini
                        </button>
                    </form>
                @else
                    <span class="text-gray-400 italic text-xs">Tidak tersedia</span>
                @endif
            @endif

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