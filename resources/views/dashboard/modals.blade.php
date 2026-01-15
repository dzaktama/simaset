{{-- ========= MODALS INFORMASI STATISTIK (Reusable) ========= --}}
@include('partials.stats_modal', ['id' => 'modalTotalAssets', 'title' => 'Daftar Seluruh Aset', 'items' => $listTotal])
@include('partials.stats_modal', ['id' => 'modalAvailableAssets', 'title' => 'Aset Tersedia', 'items' => $listAvailable])

@php
    $deployedList = \App\Models\Asset::with('holder')->where('status', 'deployed')->latest()->get();
@endphp
@include('partials.stats_modal', ['id' => 'modalDeployedAssets', 'title' => 'Aset Sedang Dipinjam', 'items' => $deployedList])

@include('partials.stats_modal', ['id' => 'modalPendingRequests', 'title' => 'Daftar Request', 'items' => $listPending])
@include('partials.stats_modal', ['id' => 'modalMaintenanceAssets', 'title' => 'Aset Rusak', 'items' => $listMaintenance])

{{-- MODAL VERIFIKASI PENGEMBALIAN --}}
<div id="verifyModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeVerifyModal()"></div>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border-t-4 border-blue-600">
            <form id="verifyForm" method="POST">
                @csrf
                <div class="bg-white px-6 pt-6 pb-4">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Verifikasi Pengembalian</h3>
                    
                    {{-- Informasi Detail --}}
                    <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div class="grid grid-cols-2 gap-4">
                            <div><p class="text-[10px] text-gray-500 uppercase font-bold">Peminjam</p><p class="text-sm font-semibold text-gray-900" id="verifyUserName">-</p></div>
                            <div><p class="text-[10px] text-gray-500 uppercase font-bold">Tanggal Kembali</p><p class="text-sm font-semibold text-gray-900" id="verifyDate">-</p></div>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Barang</p>
                            <div class="flex justify-between items-center">
                                <p class="text-sm font-bold text-indigo-700" id="verifyAssetName">-</p>
                                <span class="text-xs bg-gray-200 px-2 py-0.5 rounded font-mono" id="verifyAssetSN">-</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Laporan Kondisi User</p>
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-0.5 text-xs font-bold rounded border" id="verifyUserConditionBadge">-</span>
                                <p class="text-xs text-gray-600 italic bg-white p-1.5 rounded border border-gray-200 w-full" id="verifyUserNotes">-</p>
                            </div>
                        </div>
                    </div>

                    {{-- Keputusan Admin --}}
                    <div class="mt-6">
                        <label class="block text-sm font-bold text-gray-800 mb-2">Keputusan Admin (Kondisi Fisik)</label>
                        <div class="grid grid-cols-1 gap-2">
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-green-50 transition border-green-200">
                                <input type="radio" name="final_condition" value="available" class="h-4 w-4 text-green-600 focus:ring-green-500" checked>
                                <div class="ml-3"><span class="block text-sm font-medium text-gray-900">Barang Oke / Layak</span></div>
                            </label>
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-yellow-50 transition border-yellow-200">
                                <input type="radio" name="final_condition" value="maintenance" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500">
                                <div class="ml-3"><span class="block text-sm font-medium text-gray-900">Perlu Perbaikan</span></div>
                            </label>
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-red-50 transition border-red-200">
                                <input type="radio" name="final_condition" value="broken" class="h-4 w-4 text-red-600 focus:ring-red-500">
                                <div class="ml-3"><span class="block text-sm font-medium text-gray-900">Rusak Berat</span></div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-blue-600 text-sm font-bold text-white shadow-sm hover:bg-blue-700">Konfirmasi Terima</button>
                    <button type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" onclick="closeVerifyModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PENOLAKAN --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRejectModal()"></div>
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tolak Permintaan?</h3>
                    <p class="text-sm text-gray-500 mb-4">Anda akan menolak permintaan dari <span id="rejectUserName" class="font-bold"></span> untuk barang <span id="rejectAssetName" class="font-bold"></span>.</p>
                    <label for="admin_note" class="block text-sm font-medium text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="admin_note" id="admin_note" rows="3" required class="shadow-sm mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-2"></textarea>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Kirim Penolakan</button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeRejectModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL SINGLE REQUEST --}}
<div id="requestModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRequestModal()"></div>
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
            <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Detail Permintaan</h3>
                <button onclick="closeRequestModal()" class="text-indigo-200 hover:text-white">&times;</button>
            </div>
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 border-b pb-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold" id="modalUserInitials">-</div>
                        <div><p class="text-sm font-bold text-gray-900" id="modalUserName">-</p><p class="text-xs text-gray-500" id="modalUserEmail">-</p></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <div><p class="text-xs text-gray-500 uppercase">Nama Barang</p><p class="font-semibold text-gray-900" id="modalAssetName">-</p></div>
                        <div><p class="text-xs text-gray-500 uppercase">Serial Number</p><p class="font-mono text-gray-900" id="modalAssetSN">-</p></div>
                        <div><p class="text-xs text-gray-500 uppercase">Status</p><p class="text-sm text-gray-900" id="modalAssetStatus">-</p></div>
                        <div><p class="text-xs text-gray-500 uppercase">Kondisi Fisik</p><p class="text-sm text-gray-900 italic" id="modalAssetCondition">-</p></div>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900 mb-1">Alasan Peminjaman:</p><div class="p-3 bg-yellow-50 text-yellow-900 rounded-md text-sm border border-yellow-200" id="modalReason">-</div></div>
                    <div class="flex justify-between text-sm">
                        <div><span class="text-gray-500">Waktu Request:</span><span class="font-medium text-gray-900" id="modalReqDate">-</span></div>
                        <div><span class="text-gray-500">Rencana Kembali:</span><span class="font-medium text-gray-900" id="modalReturnDate">-</span></div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end">
                <button onclick="closeRequestModal()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:w-auto sm:text-sm">Tutup</button>
            </div>
        </div>
    </div>
</div>