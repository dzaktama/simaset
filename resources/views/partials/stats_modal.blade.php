<div id="{{ $id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('{{ $id }}')"></div>

        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-lg font-bold leading-6 text-gray-900">{{ $title }}</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeModal('{{ $id }}')">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="overflow-y-auto max-h-[60vh]">
                    @if($items->isEmpty())
                        <p class="text-center text-gray-500 py-4">Data kosong.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Nama Aset</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Serial Number</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Pemegang</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($items as $item)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $item->serial_number }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $item->status == 'available' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $item->status == 'deployed' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $item->status == 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $item->status == 'broken' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->holder->name ?? '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('{{ $id }}')">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>