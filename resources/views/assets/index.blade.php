@extends('layouts.main')

@section('container')
{{-- 1. HEADER --}}
<div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div>
        <h2 class="text-3xl font-bold leading-tight text-gray-900">Katalog Aset IT</h2>
        <p class="mt-2 text-sm text-gray-600">
            @if(auth()->user()->role == 'admin') Kelola inventaris & stok. @else Cari dan ajukan peminjaman aset. @endif
        </p>
    </div>
    @if(auth()->user()->role == 'admin')
    <a href="/assets/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Tambah Aset
    </a>
    @endif
</div>

{{-- 2. FILTER BAR --}}
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
    <form action="/assets" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="w-full md:w-1/3">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pencarian</label>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, SN, atau deskripsi..." class="block w-full rounded-lg border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg></div>
            </div>
        </div>
        <div class="w-full md:w-1/4">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
            <select name="status" class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="all">Semua Status</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="deployed" {{ request('status') == 'deployed' ? 'selected' : '' }}>Deployed</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="broken" {{ request('status') == 'broken' ? 'selected' : '' }}>Broken</option>
            </select>
        </div>
        <div class="w-full md:w-1/4">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Urutkan</label>
            <select name="sort" class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="latest">Terbaru</option>
                <option value="stock_low">Stok Sedikit</option>
                <option value="stock_high">Stok Banyak</option>
                <option value="name_asc">Nama (A-Z)</option>
            </select>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-900 transition">Filter</button>
            @if(auth()->user()->role == 'admin')
            <a href="{{ route('report.assets', ['status' => request('status') ?? 'all']) }}" target="_blank" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cetak</a>
            @endif
        </div>
    </form>
</div>

{{-- 3. TABEL ASET --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aset Info</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kondisi</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assets as $asset)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 rounded bg-gray-100 border border-gray-200 overflow-hidden relative">
                                @if($asset->image) <img class="h-full w-full object-cover" src="{{ asset('storage/' . $asset->image) }}">
                                @else <div class="h-full w-full flex items-center justify-center text-gray-400"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div> @endif
                                @if($asset->image2 || $asset->image3) <div class="absolute bottom-0 right-0 bg-black/60 text-white text-[9px] px-1.5 py-0.5 rounded-tl">+Foto</div> @endif
                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-gray-900">{{ $asset->name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $asset->serial_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center"><span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $asset->quantity > 0 ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800' }}">{{ $asset->quantity }} Unit</span></td>
                    <td class="px-6 py-4"><span class="text-sm text-gray-700">{{ $asset->condition_notes ?? 'Baik' }}</span></td>
                    <td class="px-6 py-4">
                        @if($asset->quantity == 0) <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Habis</span>
                        @elseif($asset->status == 'available') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                        @elseif($asset->status == 'deployed') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Deployed</span>
                        @else <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($asset->status) }}</span> @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openDetailModal({{ json_encode($asset) }}, {{ json_encode($asset->holder) }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded border border-indigo-200 hover:bg-indigo-100 transition">Detail</button>
                            @if(auth()->user()->role == 'admin')
                                <a href="/assets/{{ $asset->id }}/edit" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-3 py-1 rounded border border-yellow-200 hover:bg-yellow-100 transition">Edit</a>
                                <form action="/assets/{{ $asset->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus aset ini?');"> @method('delete') @csrf <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded border border-red-200 hover:bg-red-100 transition">Hapus</button></form>
                            @else
                                @if($asset->quantity > 0 && $asset->status == 'available')
                                    <button onclick="openLoanModal({{ json_encode($asset) }})" class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1 rounded border border-transparent shadow-sm transition">Pinjam</button>
                                @else
                                    <button disabled class="text-gray-400 bg-gray-100 px-3 py-1 rounded border border-gray-200 cursor-not-allowed">Pinjam</button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Data aset tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">{{ $assets->links() }}</div>
</div>

{{-- 4. MODAL DETAIL --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
            <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Detail Aset</h3>
                <button onclick="closeDetailModal()" class="text-indigo-200 hover:text-white">&times;</button>
            </div>
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <div class="flex flex-col md:flex-row gap-6 mb-6">
                    <div class="w-full md:w-5/12">
                        <div class="relative w-full h-64 bg-gray-100 rounded-lg overflow-hidden border shadow-sm group">
                            <div id="carouselSlides" class="flex transition-transform duration-500 ease-out h-full w-full"></div>
                            <button id="prevBtn" onclick="prevSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 text-white p-1.5 rounded-full hidden"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>
                            <button id="nextBtn" onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 text-white p-1.5 rounded-full hidden"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>
                            <div id="carouselIndicators" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5"></div>
                        </div>
                    </div>
                    <div class="w-full md:w-7/12 space-y-3">
                        <div class="border-b pb-3">
                            <h2 id="modalName" class="text-2xl font-bold text-gray-900 leading-tight">-</h2>
                            <div class="flex items-center gap-2 mt-1">
                                <span id="modalSN" class="text-sm font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded">-</span>
                                <span id="modalStatus" class="px-2 py-0.5 text-xs font-bold rounded-full bg-gray-200 text-gray-800">-</span>
                                <span id="modalQuantity" class="px-2 py-0.5 text-xs font-bold rounded-full bg-gray-100 text-gray-600 border border-gray-300">-</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><p class="text-xs text-gray-500 uppercase font-bold">Kondisi</p><p id="modalCondition" class="font-medium">-</p></div>
                            <div><p class="text-xs text-gray-500 uppercase font-bold">Terdaftar</p><p id="modalCreatedAt" class="font-medium">-</p></div>
                        </div>
                        <div><p class="text-xs text-gray-500 uppercase font-bold">Deskripsi</p><p id="modalDescription" class="text-sm text-gray-700 bg-gray-50 p-2 rounded mt-1">-</p></div>
                    </div>
                </div>
                {{-- Status Container --}}
                <div id="statusContainer" class="border-t pt-4"></div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end">
                <button onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:w-auto sm:text-sm">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- 5. MODAL FORM PINJAM (REVISI: LEBIH DETAIL & INFORMATIF) --}}
<div id="loanModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeLoanModal()"></div>
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <form action="/requests" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Form Pengajuan Peminjaman</h3>
                        <button type="button" onclick="closeLoanModal()" class="text-gray-400 hover:text-gray-500">&times;</button>
                    </div>

                    {{-- [REVISI] KARTU RINGKASAN ASET DI FORM --}}
                    <div class="flex items-start gap-4 p-3 bg-indigo-50 border border-indigo-100 rounded-lg mb-6">
                        <div class="h-16 w-16 flex-shrink-0 bg-white rounded-md border border-indigo-200 overflow-hidden flex items-center justify-center">
                            <img id="loanAssetImg" src="" class="h-full w-full object-cover hidden">
                            <svg id="loanAssetIcon" class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900" id="loanAssetNameDisplay">-</h4>
                            <p class="text-xs text-indigo-600 font-mono" id="loanAssetSNDisplay">-</p>
                            <p class="text-xs text-gray-500 mt-1" id="loanAssetConditionDisplay">Kondisi: Baik</p>
                        </div>
                    </div>

                    <input type="hidden" name="asset_id" id="loanAssetId">
                    
                    <div class="space-y-4">
                        {{-- Jumlah Unit --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah Unit</label>
                            <div class="flex items-center gap-2 mt-1">
                                <input type="number" name="quantity" id="loanQuantity" min="1" value="1" required class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                <span class="text-xs text-gray-500" id="loanMaxStockText"></span>
                            </div>
                        </div>

                        {{-- Rencana Kembali --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rencana Kembali <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <div><label class="text-[10px] text-gray-500 font-bold uppercase">Tanggal</label><input type="date" name="return_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"></div>
                                <div><label class="text-[10px] text-gray-500 font-bold uppercase">Jam (WIB)</label><input type="time" name="return_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"></div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">*Kosongkan jika peminjaman jangka panjang / belum ditentukan.</p>
                        </div>

                        {{-- Alasan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Keperluan / Alasan <span class="text-red-500">*</span></label>
                            <textarea name="reason" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Contoh: Untuk setup event di ruang meeting utama"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Kirim Pengajuan</button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeLoanModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getImg(path) { return path ? `/storage/${path}` : 'https://via.placeholder.com/600x400?text=No+Image'; }
    function formatDateID(dateStr) { if(!dateStr) return '-'; const d = new Date(dateStr); return d.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'})+' '+d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})+' WIB'; }

    let currentSlide=0, totalSlides=0;
    function updateCarousel() {
        document.getElementById('carouselSlides').style.transform = `translateX(-${currentSlide * 100}%)`;
        const dots = document.getElementById('carouselIndicators').children;
        for(let i=0; i<dots.length; i++) { dots[i].classList.toggle('bg-white', i===currentSlide); dots[i].classList.toggle('bg-white/50', i!==currentSlide); }
    }
    function nextSlide(){ if(totalSlides>1) { currentSlide=(currentSlide+1)%totalSlides; updateCarousel(); } }
    function prevSlide(){ if(totalSlides>1) { currentSlide=(currentSlide-1+totalSlides)%totalSlides; updateCarousel(); } }
    function goToSlide(i){ currentSlide=i; updateCarousel(); }

    // Helper Global
    let currentAssetData = null;
    const authRole = "{{ auth()->user()->role }}";

    function openDetailModal(asset, holder) {
        currentAssetData = asset;
        
        // Populate Basic
        document.getElementById('modalName').innerText = asset.name;
        document.getElementById('modalSN').innerText = asset.serial_number;
        document.getElementById('modalDescription').innerText = asset.description || '-';
        document.getElementById('modalCondition').innerText = asset.condition_notes || 'Kondisi Baik';
        document.getElementById('modalQuantity').innerText = 'Stok: ' + asset.quantity + ' Unit';
        
        const cDate = new Date(asset.created_at);
        document.getElementById('modalCreatedAt').innerText = cDate.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});

        // Populate Status & Buttons
        const st = document.getElementById('modalStatus');
        st.innerText = asset.status.toUpperCase();
        st.className = "px-2 py-0.5 text-xs font-bold rounded-full";
        
        const cont = document.getElementById('statusContainer');
        cont.innerHTML = ''; cont.className="border-t pt-4";

        if(asset.status === 'deployed') {
            st.classList.add('bg-blue-100', 'text-blue-800');
            const assignTime = asset.assigned_date ? formatDateID(asset.assigned_date) : '-';
            // [REVISI POIN 6] Ubah teks "Tidak ada batas" jadi lebih proper
            const retTime = asset.return_date ? formatDateID(asset.return_date) : 'Jangka Panjang / Permanen';
            
            cont.innerHTML = `
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-8 w-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg></div>
                        <div><p class="text-xs text-blue-600 font-bold uppercase">Peminjam</p><p class="text-sm font-bold text-gray-900">${holder?holder.name:'Unknown'}</p></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-gray-500 text-xs">Waktu Pinjam</span><br><span class="font-medium text-gray-900">${assignTime}</span></div>
                        <div><span class="text-gray-500 text-xs">Batas Kembali</span><br><span class="font-medium text-gray-900">${retTime}</span></div>
                    </div>
                </div>
            `;
        } else if(asset.status === 'available') {
            st.classList.add('bg-green-100', 'text-green-800');
            cont.innerHTML = `
                <div class="flex items-center justify-between bg-green-50 p-4 rounded-lg border border-green-100">
                    <div class="flex items-center gap-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div><p class="text-green-800 font-bold text-sm">Tersedia</p><p class="text-xs text-green-600">Siap dipinjamkan.</p></div>
                    </div>
                    ${authRole!='admin' && asset.quantity>0 ? `<button onclick="closeDetailModal(); openLoanModal(currentAssetData)" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-green-700 shadow-sm">Ajukan Pinjam</button>` : ''}
                </div>
            `;
        } else {
            st.classList.add('bg-red-100', 'text-red-800');
            cont.innerHTML = `<div class="bg-red-50 p-4 rounded text-center border border-red-100"><p class="text-red-800 font-bold text-sm">Sedang Maintenance</p><p class="text-xs text-red-600">Aset tidak dapat digunakan.</p></div>`;
        }

        // Carousel Images
        let imgs=[]; if(asset.image) imgs.push(asset.image); if(asset.image2) imgs.push(asset.image2); if(asset.image3) imgs.push(asset.image3); if(imgs.length==0) imgs.push(null);
        
        let slides='', dots='';
        totalSlides=imgs.length; currentSlide=0;
        
        imgs.forEach((im,i) => {
            slides += `<div class="min-w-full h-full flex items-center justify-center bg-gray-200"><img src="${getImg(im)}" class="w-full h-full object-cover"></div>`;
            dots += `<button onclick="goToSlide(${i})" class="w-2 h-2 rounded-full transition-all bg-white/50"></button>`;
        });
        document.getElementById('carouselSlides').innerHTML=slides;
        document.getElementById('carouselIndicators').innerHTML=dots;
        const navs = [document.getElementById('prevBtn'),document.getElementById('nextBtn'),document.getElementById('carouselIndicators')];
        navs.forEach(e => totalSlides>1 ? e.classList.remove('hidden') : e.classList.add('hidden'));
        updateCarousel();

        document.getElementById('detailModal').classList.remove('hidden');
    }
    function closeDetailModal(){ document.getElementById('detailModal').classList.add('hidden'); }

    function openLoanModal(asset) {
        document.getElementById('loanAssetId').value = asset.id;
        
        // [REVISI] Populate Kartu Ringkasan Aset
        document.getElementById('loanAssetNameDisplay').innerText = asset.name;
        document.getElementById('loanAssetSNDisplay').innerText = asset.serial_number;
        document.getElementById('loanAssetConditionDisplay').innerText = "Kondisi: " + (asset.condition_notes || 'Baik');
        
        const imgEl = document.getElementById('loanAssetImg');
        const iconEl = document.getElementById('loanAssetIcon');
        
        if(asset.image) {
            imgEl.src = `/storage/${asset.image}`;
            imgEl.classList.remove('hidden');
            iconEl.classList.add('hidden');
        } else {
            imgEl.classList.add('hidden');
            iconEl.classList.remove('hidden');
        }

        const qtyInput = document.getElementById('loanQuantity');
        qtyInput.max = asset.quantity;
        qtyInput.value = 1;
        document.getElementById('loanMaxStockText').innerText = `(Tersedia: ${asset.quantity} unit)`;

        document.getElementById('loanModal').classList.remove('hidden');
    }
    function closeLoanModal(){ document.getElementById('loanModal').classList.add('hidden'); }
</script>
@endsection