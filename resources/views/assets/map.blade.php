@extends('layouts.main')

@section('container')
<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Peta Lokasi Aset (Warehouse Map)</h1>
            <p class="text-sm text-gray-500">Visualisasi penyimpanan aset berdasarkan Lorong (Area) dan Rak.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-1 text-xs">
                <span class="w-3 h-3 bg-gray-100 border border-gray-300 rounded-sm inline-block"></span> Kosong
                <span class="w-3 h-3 bg-blue-100 border border-blue-300 rounded-sm inline-block ml-2"></span> Terisi
                <span class="w-3 h-3 bg-red-100 border border-red-300 rounded-sm inline-block ml-2"></span> Ada Kerusakan
            </div>
        </div>
    </div>

    {{-- AREA SELECTION (LORONG) --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Lorong / Area:</label>
        <div class="flex flex-wrap gap-2" id="areaTabs">
            @foreach(range('A', 'Z') as $char)
                <button onclick="selectArea('Area {{ $char }}')" id="btn-Area-{{ $char }}" class="area-btn px-4 py-2 rounded-lg border text-sm font-medium transition hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200">
                    Area {{ $char }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- RACK GRID DISPLAY --}}
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 min-h-[400px]">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-800" id="currentAreaTitle">Silakan Pilih Area</h2>
            <span class="text-xs text-gray-400" id="rackCountInfo"></span>
        </div>

        {{-- Grid Container --}}
        <div id="rackGrid" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
            {{-- Rak akan di-render via JS --}}
            <div class="col-span-full text-center text-gray-400 py-10 italic">
                Pilih salah satu Area di atas untuk melihat denah Rak.
            </div>
        </div>
    </div>
</div>

{{-- MODAL 1: LIST BARANG DI RAK --}}
<div id="rackModal" class="fixed inset-0 z-40 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeRackModal()"></div>
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all w-full max-w-3xl border border-gray-100 max-h-[90vh] flex flex-col">
            
            {{-- Header --}}
            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <div>
                    <h3 class="text-lg font-bold" id="rackModalTitle">Rak R-01</h3>
                    <p class="text-xs text-indigo-200" id="rackModalSubtitle">Area A</p>
                </div>
                <button onclick="closeRackModal()" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- List Items --}}
            <div class="p-0 overflow-y-auto custom-scrollbar flex-1 bg-gray-50">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-3">Nama Aset</th>
                            <th class="px-6 py-3">SN / Kategori</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rackItemsBody" class="divide-y divide-gray-200 bg-white">
                        {{-- Items injected by JS --}}
                    </tbody>
                </table>
                
                {{-- Empty State --}}
                <div id="emptyRackState" class="hidden flex flex-col items-center justify-center py-10 text-center">
                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <p class="text-gray-500">Rak ini kosong.</p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-end">
                <button onclick="closeRackModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: DETAIL ASET (NESTED - Z-INDEX 50 LEBIH TINGGI) --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 transition-opacity backdrop-blur-sm" onclick="closeDetailModal()"></div>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-gray-100">
            
            {{-- Modal Header --}}
            <div class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Detail Informasi Aset</h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="bg-white px-6 py-6 overflow-y-auto max-h-[70vh]">
                <div class="flex flex-col md:flex-row gap-8">
                    {{-- KIRI: Foto & QR --}}
                    <div class="w-full md:w-5/12 flex flex-col gap-4">
                        <div class="relative w-full h-56 bg-gray-100 rounded-xl overflow-hidden border border-gray-200">
                            <img id="detImg" src="" class="w-full h-full object-contain p-2">
                        </div>
                        {{-- QR --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center text-center">
                            <p class="text-xs font-bold text-gray-500 uppercase mb-2">QR Code</p>
                            <img id="detQR" src="" class="w-24 h-24 object-contain bg-white p-1 rounded border">
                        </div>
                    </div>

                    {{-- KANAN: Info --}}
                    <div class="w-full md:w-7/12 space-y-4">
                        <div>
                            <h2 id="detName" class="text-2xl font-bold text-gray-900">-</h2>
                            <p id="detSN" class="font-mono text-sm text-gray-500">-</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><span class="block text-xs font-bold text-gray-400 uppercase">Kategori</span><span id="detCat" class="font-medium">-</span></div>
                            <div><span class="block text-xs font-bold text-gray-400 uppercase">Status</span><span id="detStatus" class="px-2 py-0.5 rounded text-xs font-bold uppercase">-</span></div>
                            <div><span class="block text-xs font-bold text-gray-400 uppercase">Lokasi</span><span id="detLoc" class="font-medium">-</span></div>
                            <div><span class="block text-xs font-bold text-gray-400 uppercase">Kondisi</span><span id="detCond" class="font-medium">-</span></div>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Deskripsi</span>
                            <div id="detDesc" class="p-3 bg-gray-50 rounded text-sm text-gray-600 border">-</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button onclick="closeDetailModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>

{{-- DATA ASSETS (Disimpan di JS Variable untuk Filtering Client-side yang Cepat) --}}
<script>
    // Data Aset dari Controller (Di-pass sebagai JSON)
    const allAssets = {!! json_encode($assets) !!};
    
    // Config Rak
    const totalRacks = 50; 

    // Helper functions
    function selectArea(areaName) {
        // Update UI Tabs
        document.querySelectorAll('.area-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
            btn.classList.add('border');
        });
        const activeBtn = document.getElementById('btn-' + areaName.replace(' ', '-'));
        if(activeBtn) {
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            activeBtn.classList.remove('border');
        }

        document.getElementById('currentAreaTitle').innerText = 'Denah Rak: ' + areaName;
        renderGrid(areaName);
    }

    function renderGrid(area) {
        const grid = document.getElementById('rackGrid');
        grid.innerHTML = '';

        // Filter aset berdasarkan area ini
        const areaAssets = allAssets.filter(a => a.lorong === area);
        
        let filledCount = 0;

        for (let i = 1; i <= totalRacks; i++) {
            const rackName = 'R-' + String(i).padStart(2, '0');
            
            // Cari barang di rak ini
            const itemsInRack = areaAssets.filter(a => a.rak === rackName);
            const hasItems = itemsInRack.length > 0;
            const hasBroken = itemsInRack.some(a => a.status === 'broken' || a.status === 'maintenance');

            if(hasItems) filledCount++;

            // Tentukan Warna Kotak
            let boxClass = "bg-gray-50 border-gray-200 text-gray-400 hover:border-indigo-300 hover:text-indigo-500"; // Default Kosong
            if (hasItems) {
                if(hasBroken) {
                    boxClass = "bg-red-50 border-red-200 text-red-600 font-bold shadow-sm hover:bg-red-100 hover:border-red-400"; // Ada barang rusak
                } else {
                    boxClass = "bg-blue-50 border-blue-200 text-blue-600 font-bold shadow-sm hover:bg-blue-100 hover:border-blue-400"; // Aman
                }
            }

            // Create Element
            const div = document.createElement('div');
            div.className = `cursor-pointer border-2 rounded-lg p-4 flex flex-col items-center justify-center transition transform hover:-translate-y-1 ${boxClass}`;
            div.onclick = () => openRackModal(area, rackName, itemsInRack);
            
            div.innerHTML = `
                <div class="text-sm">${rackName}</div>
                <div class="text-[10px] mt-1">${itemsInRack.length} Item</div>
            `;
            grid.appendChild(div);
        }
        
        document.getElementById('rackCountInfo').innerText = `${filledCount} Rak Terisi dari ${totalRacks}`;
    }

    // --- LOGIC MODAL RAK ---
    function openRackModal(area, rack, items) {
        document.getElementById('rackModalTitle').innerText = `Isi Rak ${rack}`;
        document.getElementById('rackModalSubtitle').innerText = `Lokasi: ${area}`;
        
        const tbody = document.getElementById('rackItemsBody');
        const emptyState = document.getElementById('emptyRackState');
        tbody.innerHTML = '';

        if (items.length === 0) {
            tbody.parentElement.classList.add('hidden');
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            tbody.parentElement.classList.remove('hidden');

            items.forEach(item => {
                // Generate Status Badge
                let statusColor = 'bg-gray-100 text-gray-800';
                if(item.status === 'available') statusColor = 'bg-green-100 text-green-800';
                if(item.status === 'deployed') statusColor = 'bg-blue-100 text-blue-800';
                if(item.status === 'broken') statusColor = 'bg-red-100 text-red-800';
                if(item.status === 'maintenance') statusColor = 'bg-yellow-100 text-yellow-800';

                // Row HTML
                const row = `
                    <tr class="hover:bg-gray-50 border-b last:border-0">
                        <td class="px-6 py-3 font-medium text-gray-900">
                            ${item.name}
                            ${item.image ? `<br><span class="text-[10px] text-blue-500 flex items-center gap-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> Ada Foto</span>` : ''}
                        </td>
                        <td class="px-6 py-3">
                            <div class="font-mono text-xs text-gray-500">${item.serial_number}</div>
                            <div class="text-xs text-gray-400">${item.category}</div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 text-xs font-bold rounded-full uppercase tracking-wider ${statusColor}">${item.status}</span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Detail --}}
                                <button onclick='openNestedDetail(${JSON.stringify(item)})' class="p-1.5 bg-indigo-50 text-indigo-600 rounded hover:bg-indigo-100 transition text-xs font-bold border border-indigo-200">
                                    Detail
                                </button>
                                
                                {{-- Tombol Edit/Pindah (Shortcut) --}}
                                <a href="/assets/${item.id}/edit" class="p-1.5 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100 transition text-xs font-bold border border-yellow-200" title="Pindah Rak / Edit">
                                    Pindah
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        }

        document.getElementById('rackModal').classList.remove('hidden');
    }

    function closeRackModal() {
        document.getElementById('rackModal').classList.add('hidden');
    }

    // --- LOGIC MODAL DETAIL (NESTED) ---
    function openNestedDetail(item) {
        // Isi Data ke Modal Detail
        document.getElementById('detName').innerText = item.name;
        document.getElementById('detSN').innerText = item.serial_number;
        document.getElementById('detCat').innerText = item.category;
        document.getElementById('detStatus').innerText = item.status;
        document.getElementById('detLoc').innerText = (item.lorong || '-') + ' / ' + (item.rak || '-');
        document.getElementById('detCond').innerText = item.condition_notes || '-';
        document.getElementById('detDesc').innerText = item.description || '-';
        
        // Foto
        const imgEl = document.getElementById('detImg');
        if(item.image) imgEl.src = `/storage/${item.image}`;
        else imgEl.src = ''; // Bisa ganti placeholder

        // QR Code (Generate on the fly with Google API or simple library url if needed, here we assume BE scan url)
        // Note: For pure JS map, rendering PHP generated QR string is hard. 
        // We will use a placeholder or assume the controller passed the QR URL if available.
        // Simplest: Use simple-qrcode controller route as image source
        document.getElementById('detQR').src = `/assets/${item.id}/scan-qr-image`; // Assuming you might create this, OR:
        // Use standard SVG data URI if passed (advanced). 
        // For now, let's use a generic QR placeholder or the scan route which returns text/html.
        // Better: Create a route that returns ONLY the QR image.
        // Temporary: Hide QR in Map Detail to avoid error, or use a static icon.
        // document.getElementById('detQR').style.display = 'none'; 
        
        // Tampilkan Modal (Tanpa menutup RackModal)
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        // Rack Modal tidak diutak-atik, jadi tetap terbuka di bawahnya
    }

    // Init
    document.addEventListener('DOMContentLoaded', () => {
        selectArea('Area A'); // Default open Area A
    });
</script>

<style>
    /* Custom Scrollbar for Modal List */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #999; }
</style>
@endsection