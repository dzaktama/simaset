@extends('layouts.main')

@section('container')
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="text-3xl font-bold leading-tight text-gray-900">
                {{ $title }}
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Selamat datang, <span class="font-semibold text-indigo-600">{{ auth()->user()->name }}</span>.
            </p>
        </div>
        
        {{-- [REVISI POIN 2] Area Tanggal & Jam Digital --}}
        <div class="flex flex-col md:flex-row gap-3">
            {{-- Tanggal --}}
            <div class="flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-600 shadow-sm border border-gray-200">
                <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>

            {{-- Jam Digital (New) --}}
            <div class="flex items-center gap-2 rounded-lg bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 shadow-sm border border-indigo-100 min-w-[140px] justify-center">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="live-clock" class="font-mono text-lg tracking-wide">{{ now()->format('H:i:s') }}</span>
                <span class="text-xs font-bold ml-1">WIB</span>
            </div>
        </div>
    </div>

    @if(auth()->user()->role == 'admin')
        {{-- ======================= DASHBOARD ADMIN ======================= --}}
        
        {{-- STATS GRID --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            {{-- 1. Total Aset --}}
            <div onclick="openModal('modalTotalAssets')" class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition cursor-pointer group relative">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3 group-hover:bg-indigo-600 transition">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            </div>
                            <div class="ml-5">
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Aset</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                            </div>
                        </div>
                        <div class="text-gray-300 group-hover:text-indigo-500 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></div>
                    </div>
                </div>
            </div>
            {{-- 2. Aset Tersedia --}}
            <div onclick="openModal('modalAvailableAssets')" class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition cursor-pointer group relative">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3 group-hover:bg-green-600 transition">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div class="ml-5">
                                <dt class="text-sm font-medium text-gray-500 truncate">Tersedia</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $stats['available'] }}</dd>
                            </div>
                        </div>
                        <div class="text-gray-300 group-hover:text-green-500 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></div>
                    </div>
                </div>
            </div>
            {{-- 3. Request Masuk --}}
            <div onclick="openModal('modalPendingRequests')" class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition cursor-pointer group relative">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3 group-hover:bg-yellow-600 transition">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            </div>
                            <div class="ml-5">
                                <dt class="text-sm font-medium text-gray-500 truncate">Request Masuk</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $stats['pending_requests'] }}</dd>
                            </div>
                        </div>
                        <div class="text-gray-300 group-hover:text-yellow-500 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></div>
                    </div>
                </div>
            </div>
            {{-- 4. Service / Rusak --}}
            <div onclick="openModal('modalMaintenanceAssets')" class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition cursor-pointer group relative">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3 group-hover:bg-red-600 transition">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div class="ml-5">
                                <dt class="text-sm font-medium text-gray-500 truncate">Service / Rusak</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $stats['maintenance'] }}</dd>
                            </div>
                        </div>
                        <div class="text-gray-300 group-hover:text-red-500 transition"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL PERMINTAAN MASUK (FULL WIDTH) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h3 class="text-lg font-bold text-gray-900">Permintaan Masuk (Pending)</h3>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $recentRequests->count() }} Permintaan</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Peminjam</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Alasan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Waktu Request</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentRequests as $req)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $req->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $req->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-indigo-600">{{ $req->asset->name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $req->asset->serial_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 italic truncate max-w-xs" title="{{ $req->reason }}">
                                    "{{ Str::limit($req->reason, 40) }}"
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($req->created_at)->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($req->created_at)->setTimezone('Asia/Jakarta')->format('H:i') }} WIB
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-2">
                                    {{-- Tombol Detail --}}
                                    <button onclick="openRequestDetail({{ json_encode($req) }}, {{ json_encode($req->asset) }}, {{ json_encode($req->user) }})" class="inline-flex items-center px-3 py-1.5 border border-indigo-300 text-xs font-medium rounded text-indigo-700 bg-white hover:bg-indigo-50 focus:outline-none transition">
                                        Detail
                                    </button>

                                    {{-- Tombol Tolak (MODAL TRIGGER) --}}
                                    <button onclick="openRejectModal({{ $req->id }}, '{{ $req->user->name }}', '{{ $req->asset->name }}')" class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none transition">
                                        Tolak
                                    </button>

                                    {{-- Tombol Terima --}}
                                    <form action="/requests/{{ $req->id }}/approve" method="POST" onsubmit="return confirm('Setujui permintaan ini?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none transition shadow-sm">
                                            Terima
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500"><p class="text-sm">Tidak ada permintaan pending saat ini.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL VERIFIKASI PENGEMBALIAN (BARU) --}}
        {{-- TABEL VERIFIKASI PENGEMBALIAN (REVISI: DETAIL & KEPUTUSAN ADMIN) --}}
        @php
            $pendingReturns = \App\Models\AssetReturn::with(['user', 'asset', 'assetRequest'])->where('status', 'pending')->get();
        @endphp

        @if($pendingReturns->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-blue-200 mb-8 overflow-hidden">
            <div class="px-6 py-4 border-b border-blue-200 flex justify-between items-center bg-blue-50">
                <div class="flex items-center gap-2">
                    <div class="bg-blue-600 p-1.5 rounded-md">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-blue-900">Verifikasi Pengembalian Aset</h3>
                </div>
                <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm animate-pulse">{{ $pendingReturns->count() }} Menunggu</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">User Peminjam</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Barang & Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Laporan User</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingReturns as $ret)
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $ret->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $ret->return_date }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-indigo-600">{{ $ret->asset->name }}</div>
                                <div class="text-xs font-mono text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded inline-block mt-1">
                                    {{ $ret->assetRequest->quantity }} Unit
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full {{ $ret->condition == 'good' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $ret->condition == 'good' ? 'Kondisi Baik' : 'Ada Kerusakan' }}
                                </span>
                                <div class="text-xs text-gray-600 mt-1 italic truncate max-w-[200px]">"{{ $ret->notes ?? '-' }}"</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                {{-- Tombol Trigger Modal --}}
                                <button onclick="openVerifyModal({{ json_encode($ret) }}, {{ json_encode($ret->asset) }}, {{ json_encode($ret->user) }})" 
                                        class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md transition transform hover:-translate-y-0.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Proses
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- MODAL VERIFIKASI PENGEMBALIAN (Pop-Up Detail) --}}
        <div id="verifyModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeVerifyModal()"></div>
                
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border-t-4 border-blue-600">
                    <form id="verifyForm" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Verifikasi Pengembalian</h3>
                                    <p class="text-sm text-gray-500">Cek fisik barang sebelum menyetujui.</p>
                                </div>
                                <div class="bg-blue-50 p-2 rounded-full text-blue-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                </div>
                            </div>

                            {{-- Informasi Detail --}}
                            <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[10px] text-gray-500 uppercase font-bold">Peminjam</p>
                                        <p class="text-sm font-semibold text-gray-900" id="verifyUserName">-</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-500 uppercase font-bold">Tanggal Kembali</p>
                                        <p class="text-sm font-semibold text-gray-900" id="verifyDate">-</p>
                                    </div>
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
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Barang Oke / Layak</span>
                                            <span class="block text-xs text-gray-500">Stok akan kembali ke gudang (Available).</span>
                                        </div>
                                    </label>
                                    
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-yellow-50 transition border-yellow-200">
                                        <input type="radio" name="final_condition" value="maintenance" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500">
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Perlu Perbaikan</span>
                                            <span class="block text-xs text-gray-500">Status aset menjadi Maintenance.</span>
                                        </div>
                                    </label>

                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-red-50 transition border-red-200">
                                        <input type="radio" name="final_condition" value="broken" class="h-4 w-4 text-red-600 focus:ring-red-500">
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Rusak Berat</span>
                                            <span class="block text-xs text-gray-500">Status aset menjadi Broken (Tidak bisa dipinjam).</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-blue-600 text-sm font-bold text-white shadow-sm hover:bg-blue-700 focus:outline-none transition">
                                Konfirmasi Terima
                            </button>
                            <button type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition" onclick="closeVerifyModal()">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Script Modal Verifikasi
            function openVerifyModal(retData, assetData, userData) {
                // Populate Data
                document.getElementById('verifyUserName').innerText = userData.name;
                document.getElementById('verifyDate').innerText = retData.return_date; // Bisa diformat lagi pakai JS Date kalau mau
                document.getElementById('verifyAssetName').innerText = assetData.name;
                document.getElementById('verifyAssetSN').innerText = assetData.serial_number;
                document.getElementById('verifyUserNotes').innerText = retData.notes || 'Tidak ada catatan user.';
                
                // Badge Kondisi User
                const badge = document.getElementById('verifyUserConditionBadge');
                if (retData.condition === 'good') {
                    badge.innerText = 'USER: BAIK';
                    badge.className = 'px-2 py-0.5 text-xs font-bold uppercase rounded-full border bg-green-100 text-green-800 border-green-200';
                } else {
                    badge.innerText = 'USER: RUSAK/BERMASALAH';
                    badge.className = 'px-2 py-0.5 text-xs font-bold uppercase rounded-full border bg-red-100 text-red-800 border-red-200';
                }

                // Set Action URL Form
                document.getElementById('verifyForm').action = `/returns/${retData.id}/verify`;

                document.getElementById('verifyModal').classList.remove('hidden');
            }

            function closeVerifyModal() {
                document.getElementById('verifyModal').classList.add('hidden');
            }
        </script>

        {{-- ======================= AKTIVITAS TERBARU ======================= --}}

        {{-- LOG AKTIVITAS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <h3 class="text-lg font-bold text-gray-900">Log Aktivitas Terbaru</h3>
            </div>
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($activities as $log)
                <li class="px-6 py-4 hover:bg-gray-50 transition">
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold">
                                {{ substr($log->user->name ?? 'S', 0, 1) }}
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'Sistem' }}</p>
                            <p class="text-xs text-gray-600">
                                <span class="uppercase font-bold text-indigo-600">{{ $log->action }}</span> 
                                <span class="font-medium text-gray-900">{{ $log->asset->name }}</span>
                                @if($log->notes)
                                    <span class="text-gray-500 italic"> - {{ $log->notes }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <div class="text-xs text-gray-500 font-bold">
                                {{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Jakarta')->format('H:i') }} WIB
                            </div>
                            <div class="text-[10px] text-gray-400">
                                {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    </div>
                </li>
                @empty
                <li class="px-6 py-4 text-center text-gray-500 italic">Belum ada aktivitas.</li>
                @endforelse
            </ul>
        </div>

        {{-- ========= MODALS INFORMASI STATISTIK ========= --}}
        @include('partials.stats_modal', ['id' => 'modalTotalAssets', 'title' => 'Daftar Seluruh Aset', 'items' => $listTotal])
        @include('partials.stats_modal', ['id' => 'modalAvailableAssets', 'title' => 'Aset Tersedia', 'items' => $listAvailable])
        @include('partials.stats_modal', ['id' => 'modalPendingRequests', 'title' => 'Daftar Request', 'items' => $listPending])
        @include('partials.stats_modal', ['id' => 'modalMaintenanceAssets', 'title' => 'Aset Rusak', 'items' => $listMaintenance])

        {{-- MODAL KHUSUS PENDING REQUEST --}}
        <div id="modalPendingRequests" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('modalPendingRequests')"></div>
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-bold leading-6 text-gray-900">Daftar Permintaan Masuk</h3>
                            <button onclick="closeModal('modalPendingRequests')" class="text-gray-400 hover:text-gray-500">&times;</button>
                        </div>
                        <div class="overflow-y-auto max-h-[60vh]">
                            @if($listPending->isEmpty())
                                <p class="text-center text-gray-500 py-4">Tidak ada data.</p>
                            @else
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr><th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Peminjam</th><th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Barang</th><th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Alasan</th><th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Waktu</th></tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($listPending as $req)
                                        <tr>
                                            <td class="px-4 py-2 text-sm font-medium">{{ $req->user->name }}</td>
                                            <td class="px-4 py-2 text-sm font-semibold text-indigo-600">{{ $req->asset->name }}</td>
                                            <td class="px-4 py-2 text-sm italic">"{{ Str::limit($req->reason, 50) }}"</td>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $req->created_at->translatedFormat('d M Y, H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button onclick="closeModal('modalPendingRequests')" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MODAL PENOLAKAN (ADMIN) ================= --}}
        <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRejectModal()"></div>
                
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form id="rejectForm" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tolak Permintaan?</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-4">
                                            Anda akan menolak permintaan dari <span id="rejectUserName" class="font-bold"></span> untuk barang <span id="rejectAssetName" class="font-bold"></span>.
                                        </p>
                                        <label for="admin_note" class="block text-sm font-medium text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea name="admin_note" id="admin_note" rows="3" required class="shadow-sm focus:ring-red-500 focus:border-red-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-2" placeholder="Contoh: Barang sedang rusak / Tidak memenuhi syarat"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Kirim Penolakan
                            </button>
                            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeRejectModal()">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ========= MODAL DETAIL SINGLE REQUEST (POP-UP) ========= --}}
        <div id="requestModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRequestModal()"></div>
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                    <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white">Detail Permintaan</h3>
                        <button onclick="closeRequestModal()" class="text-indigo-200 hover:text-white">&times;</button>
                    </div>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="space-y-4">
                            {{-- Info User --}}
                            <div class="flex items-center gap-3 border-b pb-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold" id="modalUserInitials">-</div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900" id="modalUserName">-</p>
                                    <p class="text-xs text-gray-500" id="modalUserEmail">-</p>
                                </div>
                            </div>
                            {{-- Info Aset --}}
                            <div class="grid grid-cols-2 gap-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <div><p class="text-xs text-gray-500 uppercase">Nama Barang</p><p class="font-semibold text-gray-900" id="modalAssetName">-</p></div>
                                <div><p class="text-xs text-gray-500 uppercase">Serial Number</p><p class="font-mono text-gray-900" id="modalAssetSN">-</p></div>
                                <div><p class="text-xs text-gray-500 uppercase">Status</p><p class="text-sm text-gray-900" id="modalAssetStatus">-</p></div>
                                <div><p class="text-xs text-gray-500 uppercase">Kondisi Fisik</p><p class="text-sm text-gray-900 italic" id="modalAssetCondition">-</p></div>
                            </div>
                            {{-- Info Request --}}
                            <div>
                                <p class="text-sm font-bold text-gray-900 mb-1">Alasan Peminjaman:</p>
                                <div class="p-3 bg-yellow-50 text-yellow-900 rounded-md text-sm border border-yellow-200" id="modalReason">-</div>
                            </div>
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

    @else
        {{-- ======================= DASHBOARD USER / KARYAWAN ======================= --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Aset Saya</h3>
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 text-indigo-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </span>
                    </div>
                    <div class="flex items-baseline">
                        <p class="text-4xl font-bold text-gray-900">{{ $activeAssetsCount }}</p>
                        <p class="ml-2 text-sm text-gray-500">Barang Aktif</p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="/my-assets" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 flex items-center">
                        Lihat Detail <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Status Request</h3>
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span>
                    </div>
                    <div class="flex items-baseline">
                        <p class="text-4xl font-bold text-gray-900">{{ $pendingRequestsCount }}</p>
                        <p class="ml-2 text-sm text-gray-500">Menunggu Persetujuan</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white flex flex-col justify-center items-center text-center">
                <svg class="h-12 w-12 text-indigo-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <h3 class="text-lg font-bold">Butuh Alat Kerja?</h3>
                <p class="text-indigo-100 text-sm mt-1 mb-4">Ajukan peminjaman aset kantor dengan mudah.</p>
                <a href="/assets" class="w-full bg-white text-indigo-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-50 transition shadow-sm">
                    Cari & Pinjam Aset &rarr;
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Permintaan Anda</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Request</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($myRequests as $req)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $req->asset->name }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-[200px]" title="{{ $req->reason }}">
                                    {{ $req->reason }}
                                </div>
                            </td>
                            
                            {{-- [REVISI POIN 3] Tambah Timestamp Lengkap --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($req->created_at)->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500 font-mono mt-0.5">
                                    {{-- Format H:i:s sesuai request --}}
                                    {{ \Carbon\Carbon::parse($req->created_at)->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'returned' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $labels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'returned' => 'Dikembalikan',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$req->status] ?? 'bg-gray-100' }}">
                                    {{ $labels[$req->status] ?? ucfirst($req->status) }}
                                </span>

                                @if($req->status == 'rejected' && $req->admin_note)
                                    <div class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded border border-red-100 max-w-xs whitespace-normal">
                                        <strong>Alasan:</strong> {{ $req->admin_note }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500 italic">
                                Belum ada riwayat permintaan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <script>
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        // Modal Detail Request (Admin)
        function openRequestDetail(req, asset, user) {
            document.getElementById('modalAssetName').innerText = asset.name;
            document.getElementById('modalAssetSN').innerText = asset.serial_number;
            document.getElementById('modalAssetStatus').innerText = asset.status.toUpperCase();
            document.getElementById('modalAssetCondition').innerText = asset.condition_notes || 'Tidak ada catatan kondisi.';
            document.getElementById('modalUserName').innerText = user.name;
            document.getElementById('modalUserEmail').innerText = user.email;
            document.getElementById('modalUserInitials').innerText = user.name.charAt(0);
            document.getElementById('modalReason').innerText = req.reason;
            
            const dateOpts = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
            const reqDate = new Date(req.created_at).toLocaleDateString('id-ID', dateOpts) + ' WIB';
            const retDate = req.return_date ? new Date(req.return_date).toLocaleDateString('id-ID', dateOpts) : 'Tidak ditentukan';
            document.getElementById('modalReqDate').innerText = reqDate;
            document.getElementById('modalReturnDate').innerText = retDate;

            document.getElementById('requestModal').classList.remove('hidden');
        }
        function closeRequestModal() { document.getElementById('requestModal').classList.add('hidden'); }

        // Modal Tolak (Reject)
        function openRejectModal(id, userName, assetName) {
            document.getElementById('rejectUserName').innerText = userName;
            document.getElementById('rejectAssetName').innerText = assetName;
            document.getElementById('rejectForm').action = `/requests/${id}/reject`; // Update URL Form
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }

        // [REVISI POIN 2] SCRIPT JAM DIGITAL (HH:MM:SS)
        function updateClock() {
            const now = new Date();
            // Gunakan locale id-ID untuk format Indonesia
            const timeString = now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false 
            }).replace(/\./g, ':'); // Pastikan pemisah titik dua (standar ISO/Digital)

            const clockEl = document.getElementById('live-clock');
            if(clockEl) {
                clockEl.innerText = timeString;
            }
        }
        
        // Jalankan clock setiap 1 detik
        setInterval(updateClock, 1000);
        updateClock(); // Jalankan sekali saat load agar tidak kosong
    </script>
@endsection