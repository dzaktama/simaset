@extends('layouts.main')
@section('container')
<div class="mb-6">
    <h2 class="text-2xl font-bold">Aset Saya</h2>
</div>
<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Barang</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
            <tr>
                <td class="px-6 py-4">{{ $asset->name }} <br> <span class="text-xs text-gray-500">{{ $asset->serial_number }}</span></td>
                <td class="px-6 py-4"><span class="bg-blue-100 text-blue-800 text-xs px-2 rounded-full">Dipakai</span></td>
            </tr>
            @empty
            <tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">Tidak ada aset.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection