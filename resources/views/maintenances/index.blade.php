@extends('layouts.main')

@section('container')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Perbaikan & Pemeliharaan (Maintenance)</h1>
        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'service_center')
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
                            @if($item->status == 'on_process')
                                <button onclick="openUpdateModal({{ $item->id }}, '{{ $item->asset->name }}')" class="text-indigo-600 hover:text-indigo-900 font-bold">Update Status</button>
                            @else
                                <span class="text-gray-400 cursor-not-allowed">Selesai</span>
                            @endif
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

<!-- Modal Update Status -->
<div id="updateModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeUpdateModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="updateForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Update Status Perbaikan</h3>
                    <p class="text-sm text-gray-500 mb-4">Aset: <span id="modalAssetName" class="font-bold"></span></p>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Baru</label>
                        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" required onchange="toggleCompleteDate(this.value)">
                            <option value="on_process">Masih Proses</option>
                            <option value="completed">Selesai (Completed)</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>

                    <div id="completeDateGroup" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="completion_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya (Update Total)</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="cost" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md" placeholder="0">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Penyelesaian</label>
                        <textarea name="resolution_notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" placeholder="Apa yang diperbaiki/diganti?"></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan Update</button>
                    <button type="button" onclick="closeUpdateModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUpdateModal(id, name) {
    document.getElementById('updateModal').classList.remove('hidden');
    document.getElementById('modalAssetName').innerText = name;
    document.getElementById('updateForm').action = `/maintenances/${id}`;
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
}

function toggleCompleteDate(val) {
    const grp = document.getElementById('completeDateGroup');
    if(val === 'completed') {
        grp.classList.remove('hidden');
    } else {
        grp.classList.add('hidden');
    }
}
</script>
@endsection
