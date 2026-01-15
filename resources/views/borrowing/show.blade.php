@extends('layouts.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Detail Peminjaman</h1>
                <p class="text-gray-600 mt-1 sm:mt-2">#{{ $borrowing->id }}</p>
            </div>
            
            {{-- Tombol Kembali --}}
            <a href="{{ route('borrowing.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2 self-start sm:self-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>

        <div>
            @if($borrowing->status === 'active' || ($borrowing->status === 'approved' && !$borrowing->returned_at))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2 animate-pulse"></span>
                    Peminjaman Aktif (Disetujui)
                </span>
            @elseif($borrowing->status === 'returned' || $borrowing->returned_at)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Sudah Dikembalikan
                </span>
            @elseif($borrowing->status === 'rejected')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    Ditolak
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 9a1 1 0 100-2 1 1 0 000 2zm5-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                    </svg>
                    Menunggu Persetujuan
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-indigo-600">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-transparent border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Data Peminjam
                    </h2>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
                        <img class="h-16 w-16 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($borrowing->user->name) }}&background=EBF4FF&color=7F9CF5" alt="Avatar">
                        <div>
                            <p class="font-bold text-lg text-gray-900">{{ $borrowing->user->name ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $borrowing->user->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <p class="text-sm text-gray-500">NIP / NIK</p>
                            <p class="font-semibold text-gray-900">{{ $borrowing->user->employee_id ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jabatan</p>
                            <p class="font-semibold text-gray-900">{{ $borrowing->user->position ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Departemen</p>
                            <p class="font-semibold text-gray-900">{{ $borrowing->user->department ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Telepon</p>
                            <p class="font-semibold text-gray-900">{{ $borrowing->user->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-green-600">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-transparent border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10"></path>
                        </svg>
                        Data Aset
                    </h2>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Nama Aset</p>
                        <p class="font-semibold text-lg text-gray-900">{{ $borrowing->asset->name ?? '-' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Kategori</p>
                            <p class="font-medium text-gray-900">{{ $borrowing->asset->category ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Serial Number</p>
                            <p class="font-medium text-gray-900 font-mono text-sm">{{ $borrowing->asset->serial_number ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Kondisi Sekarang</p>
                            @if($borrowing->condition)
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium
                                    @if($borrowing->condition === 'good') bg-green-100 text-green-800
                                    @elseif($borrowing->condition === 'minor_damage') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $borrowing->condition)) }}
                                </span>
                            @else
                                <span class="text-gray-500">Belum ada data kondisi</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jumlah</p>
                            <p class="font-medium text-gray-900">{{ $borrowing->quantity ?? '-' }} Unit</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TIMELINE BARU --}}
            <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-blue-600">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-transparent border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Lacak Status
                    </h2>
                </div>
                <div class="px-6 py-6 relative">
                    {{-- Garis Vertikal Utama --}}
                    <div class="absolute left-[2.2rem] top-8 bottom-8 w-0.5 bg-gray-200 -z-10"></div>

                    {{-- 1. PENGAJUAN --}}
                    <div class="flex gap-4 mb-8">
                        <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white shadow flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Permintaan Diajukan</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($borrowing->created_at)->translatedFormat('d F Y, H:i') }}</p>
                        </div>
                    </div>

                    {{-- 2. PERSETUJUAN --}}
                    <div class="flex gap-4 mb-8">
                        @php $isApproved = $borrowing->status == 'approved' || $borrowing->returned_at; @endphp
                        <div class="w-8 h-8 rounded-full {{ $isApproved ? 'bg-blue-500' : ($borrowing->status == 'rejected' ? 'bg-red-500' : 'bg-gray-300') }} border-2 border-white shadow flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="w-full">
                            <p class="font-bold {{ $isApproved ? 'text-gray-900' : 'text-gray-500' }}">Disetujui Admin</p>
                            @if($borrowing->status == 'pending')
                                <p class="text-xs text-gray-400 mb-2">Menunggu persetujuan...</p>
                                {{-- TOMBOL SHORTCUT (FIXED ROUTE) --}}
                                @if(auth()->user()->role == 'admin')
                                    <form action="{{ route('borrowing.approve', $borrowing->id) }}" method="POST" onsubmit="return confirm('Setujui permintaan ini?')">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 text-white text-xs px-3 py-1 rounded hover:bg-blue-700 transition">Setujui Sekarang</button>
                                    </form>
                                @endif
                            @elseif($isApproved)
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($borrowing->updated_at)->translatedFormat('d F Y, H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- 3. BARANG DIGUNAKAN --}}
                    <div class="flex gap-4 mb-8">
                        @php $isActive = $borrowing->status == 'approved'; @endphp
                        <div class="w-8 h-8 rounded-full {{ $isActive ? 'bg-blue-500' : 'bg-gray-300' }} border-2 border-white shadow flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold {{ $isActive ? 'text-gray-900' : 'text-gray-500' }}">Barang Digunakan</p>
                            @if($isActive)
                                <p class="text-sm text-gray-500">Estimasi: {{ $totalDurasi ?? '-' }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- 4. SELESAI --}}
                    <div class="flex gap-4">
                        @php $isDone = $borrowing->returned_at; @endphp
                        <div class="w-8 h-8 rounded-full {{ $isDone ? 'bg-green-500' : 'bg-gray-300' }} border-2 border-white shadow flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold {{ $isDone ? 'text-gray-900' : 'text-gray-500' }}">Dikembalikan</p>
                            @if($isDone) <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($borrowing->returned_at)->translatedFormat('d F Y, H:i') }}</p> @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($borrowing->reason)
                <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-purple-600">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-transparent border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            Alasan Peminjaman
                        </h2>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-gray-700">{{ $borrowing->reason }}</p>
                    </div>
                </div>
            @endif

            @if($borrowing->return_notes && ($borrowing->status === 'returned' || $borrowing->returned_at))
                <div class="bg-blue-50 rounded-lg shadow overflow-hidden border-l-4 border-blue-600">
                    <div class="px-6 py-4 border-b border-blue-200">
                        <h2 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 9a1 1 0 100-2 1 1 0 000 2zm5-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                            </svg>
                            Catatan Pengembalian
                        </h2>
                    </div>
                    <div class="px-6 py-4 text-blue-900">
                        <p>{{ $borrowing->return_notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @php 
                // Cek status terlambat
                $isOverdue = false;
                if ($borrowing->status == 'approved' && !$borrowing->returned_at && $borrowing->return_date) {
                    $isOverdue = now()->greaterThan($borrowing->return_date);
                }
            @endphp
            <div class="bg-gradient-to-br {{ $isOverdue ? 'from-red-500 to-red-600' : 'from-blue-500 to-blue-600' }} rounded-lg shadow p-6 text-white">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $borrowing->status == 'approved' && !$borrowing->returned_at ? ($isOverdue ? 'Terlambat' : 'Sisa Waktu') : 'Total Durasi' }}
                </h3>
                
                <div class="text-center">
                    <div class="text-2xl font-bold font-mono">
                        {{ $borrowing->status == 'approved' && !$borrowing->returned_at ? ($sisaWaktu ?? '-') : ($totalDurasi ?? '-') }}
                    </div>
                    <p class="text-white text-opacity-80 text-sm mt-2">
                        @if($borrowing->status == 'approved' && !$borrowing->returned_at)
                             Batas Kembali: {{ $borrowing->return_date ? \Carbon\Carbon::parse($borrowing->return_date)->translatedFormat('d M Y') : 'Tidak ditentukan' }}
                        @else
                             Durasi Peminjaman
                        @endif
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-gray-400">
                <p class="text-sm text-gray-600 mb-1">ID Peminjaman</p>
                <p class="text-2xl font-bold text-gray-900 font-mono">#{{ $borrowing->id ?? '-' }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-400">
                <p class="text-sm text-gray-600 mb-1">Jumlah Dipinjam</p>
                <p class="text-2xl font-bold text-gray-900">{{ $borrowing->quantity ?? '-' }} Unit</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-3">Status Saat Ini</p>
                @if($borrowing->status === 'active' || ($borrowing->status === 'approved' && !$borrowing->returned_at))
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-100 text-green-800 font-semibold">
                        <span class="w-3 h-3 bg-green-600 rounded-full animate-pulse"></span>
                        Aktif
                    </span>
                @elseif($borrowing->status === 'returned' || $borrowing->returned_at)
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-100 text-blue-800 font-semibold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Dikembalikan
                    </span>
                @elseif($borrowing->status === 'rejected')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-800 font-semibold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Ditolak
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-yellow-100 text-yellow-800 font-semibold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2 1 1 0 100 2 2 2 0 01-2 2V7a2 2 0 01-2-2zm11-1a1 1 0 100 2 1 1 0 000-2zM8 8a1 1 0 100 2h4a1 1 0 100-2H8z" clip-rule="evenodd"></path>
                        </svg>
                        Tertunda
                    </span>
                @endif
            </div>

            @if(($borrowing->status === 'active' || $borrowing->status === 'approved') && !$borrowing->returned_at)
                @if(auth()->user()->role === 'admin' || auth()->id() === $borrowing->user_id)
                    <button type="button" onclick="openReturnModal()" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"></path>
                        </svg>
                        Kembalikan Aset
                    </button>
                @endif
            @endif
        </div>
    </div>
</div>

<div id="returnModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"></path>
                </svg>
                Kembalikan Aset
            </h3>
            <button type="button" onclick="closeReturnModal()" class="text-white hover:text-red-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="returnForm" method="POST" action="{{ route('borrowing.return', $borrowing->id) }}" class="p-6">
            @csrf
            {{-- Note: Jika route menggunakan POST, biarkan method POST. Jika di route pakai PUT/PATCH, tambahkan @method('PUT') --}}
            {{-- Berdasarkan route list: Route::post('/borrowing/{id}/return'...) jadi method POST --}}
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-3">Kondisi Aset</label>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50 transition">
                        <input type="radio" name="condition" value="good" class="h-4 w-4 text-green-600" required>
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">Baik</span>
                            <span class="text-xs text-gray-500">Tidak ada kerusakan</span>
                        </span>
                    </label>
                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-yellow-50 transition">
                        <input type="radio" name="condition" value="minor_damage" class="h-4 w-4 text-yellow-600" required>
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">Kerusakan Ringan</span>
                            <span class="text-xs text-gray-500">Fungsi masih normal</span>
                        </span>
                    </label>
                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-red-50 transition">
                        <input type="radio" name="condition" value="major_damage" class="h-4 w-4 text-red-600" required>
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">Kerusakan Berat</span>
                            <span class="text-xs text-gray-500">Perlu perbaikan</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Catatan</label>
                <textarea name="notes" rows="4" placeholder="Jelaskan kondisi aset atau kerusakan yang ditemukan..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeReturnModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
                    Konfirmasi Kembalikan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReturnModal() {
        document.getElementById('returnModal').classList.remove('hidden');
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('returnModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeReturnModal();
        }
    });
</script>
@endsection