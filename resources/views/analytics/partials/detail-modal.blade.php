{{-- MODAL DETAIL (Reusable) --}}
<div id="detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeDetailModal()"></div>
        
        <div class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-5xl relative z-10 flex flex-col max-h-[85vh]">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" id="modal-title">Detail Data</h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-0 overflow-y-auto flex-1 custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white sticky top-0 z-10 shadow-sm">
                        <tr id="modal-thead-tr"></tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="modal-tbody">
                        <!-- JS Injected -->
                    </tbody>
                </table>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>
