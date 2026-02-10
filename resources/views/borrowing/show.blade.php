@extends('layouts.main')

@section('container')
<div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 text-green-700 bg-green-100 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 text-red-700 bg-red-100 rounded-lg border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Detail Peminjaman</h1>
                <p class="text-gray-600 mt-1 sm:mt-2">#{{ $borrowing->id }}</p>
            </div>
            
            {{-- Tombol Kembali --}}
            <a href="{{ auth()->user()->role === 'admin' ? route('borrowing.index') : route('borrowing.history') }}" 
               class="text-blue-600 hover:text-blue-900 flex items-center gap-2 self-start sm:self-center transition-colors duration-200">
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
                    <div class="border-b border-gray-100 pb-4">
                        <p class="text-sm text-gray-600">Nama Aset</p>
                        <p class="font-bold text-lg text-gray-900">{{ $borrowing->asset->name ?? '-' }}</p>
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

            {{-- [FITUR BARU] HASIL PEMERIKSAAN PENGEMBALIAN (Ada Denda/Kerusakan) --}}
            @if($borrowing->assetReturn && $borrowing->assetReturn->status == 'approved')
                <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-red-500">
                    <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-transparent border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Hasil Pemeriksaan Pengembalian
                        </h2>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        {{-- Denda Information --}}
                        @if($borrowing->assetReturn->fine > 0)
                            <div class="flex items-center justify-between bg-red-50 p-4 rounded-lg border border-red-200">
                                <div>
                                    <p class="text-sm font-bold text-red-700 uppercase tracking-wider">Denda / Biaya Perbaikan</p>
                                    <p class="text-xs text-red-600 mt-1">Harap segera selesaikan pembayaran denda ini.</p>
                                </div>
                                <div class="text-xl font-bold text-red-800 font-mono">
                                    Rp {{ number_format($borrowing->assetReturn->fine, 0, ',', '.') }}
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Kondisi Akhir Aset</p>
                                @php $cond = $borrowing->assetReturn->condition; @endphp
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-bold mt-1
                                    @if($cond === 'good') bg-green-100 text-green-800
                                    @elseif($cond === 'maintenance') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($cond) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Diverifikasi Oleh</p>
                                <p class="font-medium text-gray-900 mt-1">{{ $borrowing->assetReturn->admin->name ?? 'Admin' }}</p>
                            </div>
                        </div>

                        {{-- Display Photos --}}
                        @if($borrowing->assetReturn->photo_proof_1 || $borrowing->assetReturn->photo_proof_2 || $borrowing->assetReturn->photo_proof_3)
                            <div>
                                <p class="text-sm text-gray-600 mb-2">Bukti Foto Kondisi:</p>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach(['photo_proof_1', 'photo_proof_2', 'photo_proof_3'] as $photoField)
                                        @if($borrowing->assetReturn->$photoField)
                                            <a href="{{ asset('storage/' . $borrowing->assetReturn->$photoField) }}" target="_blank" class="block w-full h-24 rounded-lg overflow-hidden border border-gray-200 hover:opacity-75 transition">
                                                <img src="{{ asset('storage/' . $borrowing->assetReturn->$photoField) }}" class="w-full h-full object-cover">
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- TIMELINE BARU --}}
            <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-blue-600">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-transparent border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Lacak Status
                    </h2>
                </div>
                <div class="px-6 py-6">
                    @php
                        $isApproved = $borrowing->status == 'approved' || $borrowing->returned_at;
                        $isActive = $borrowing->status == 'approved';
                        // Step 3 (Digunakan) dianggap aktif jika approved, tapi garis ke-4 (Selesai) butuh returned_at
                        // Wait, logic check:
                        // S1 -> S2: Blue if Approved.
                        // S2 -> S3: Blue if Active (Usually same as Approved? Or is there a separate trigger? In logic above isActive is just status == approved).
                        // S3 -> S4: Blue if Returned.
                        
                        $isRejected = $borrowing->status == 'rejected';
                        $isDone = $borrowing->returned_at;
                    @endphp

                    {{-- 1. PENGAJUAN --}}
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white shadow flex items-center justify-center shrink-0 z-10">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            {{-- Garis ke Step 2 --}}
                            <div class="w-0.5 flex-grow {{ $isApproved ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                        </div>
                        <div class="pb-8 pt-1">
                            <p class="font-bold text-gray-900">Permintaan Diajukan</p>
                            <p class="text-sm text-gray-500">{{ $borrowing->created_at->translatedFormat('d F Y, H:i') }}</p>
                        </div>
                    </div>

                    {{-- 2. PERSETUJUAN --}}
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full {{ $isApproved ? 'bg-blue-500' : ($isRejected ? 'bg-red-500' : 'bg-gray-200') }} border-2 border-white shadow flex items-center justify-center shrink-0 z-10">
                                @if($isRejected)
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                @elseif($isApproved)
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                @endif
                            </div>
                            {{-- Garis ke Step 3 --}}
                            <div class="w-0.5 flex-grow {{ $isActive ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                        </div>
                        <div class="pb-8 pt-1 w-full">
                            <p class="font-bold {{ $isApproved || $isRejected ? 'text-gray-900' : 'text-gray-500' }}">
                                {{ $isRejected ? 'Ditolak Admin' : 'Disetujui Admin' }}
                            </p>
                            @if($borrowing->status == 'pending')
                                <p class="text-xs text-gray-400 mb-2">Menunggu persetujuan...</p>
                            @elseif($isApproved)
                                <p class="text-sm text-gray-500">{{ ($borrowing->approved_at ?? $borrowing->updated_at)->translatedFormat('d F Y, H:i') }}</p>
                            @elseif($isRejected)
                                <p class="text-sm text-red-500 italic mt-1">"{{ $borrowing->admin_note }}"</p>
                            @endif
                        </div>
                    </div>

                    {{-- 3. BARANG DIGUNAKAN --}}
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full {{ $isActive ? 'bg-blue-500' : 'bg-gray-200' }} border-2 border-white shadow flex items-center justify-center shrink-0 z-10">
                                @if($isActive)
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @else
                                    <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                @endif
                            </div>
                            {{-- Garis ke Step 4 --}}
                            <div class="w-0.5 flex-grow {{ $isDone ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                        </div>
                        <div class="pb-8 pt-1">
                            <p class="font-bold {{ $isActive ? 'text-gray-900' : 'text-gray-500' }}">Barang Digunakan</p>
                            @if($isActive)
                                <p class="text-sm text-gray-500">{{ ($borrowing->approved_at ?? $borrowing->updated_at)->translatedFormat('d F Y, H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- 4. SELESAI --}}
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full {{ $isDone ? 'bg-green-500' : 'bg-gray-200' }} border-2 border-white shadow flex items-center justify-center shrink-0 z-10">
                                @if($isDone)
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @else
                                    <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                @endif
                            </div>
                        </div>
                        <div class="pt-1">
                            <p class="font-bold {{ $isDone ? 'text-gray-900' : 'text-gray-500' }}">Dikembalikan</p>
                            @if($isDone) <p class="text-sm text-gray-500">{{ $borrowing->returned_at->translatedFormat('d F Y, H:i') }}</p> @endif
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
            {{-- FOTO ASET (ATAS TOTAL DURASI) --}}
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="relative w-full h-56 bg-gray-100 flex items-center justify-center group">
                    @if($borrowing->asset->image)
                        <img src="{{ asset('storage/' . $borrowing->asset->image) }}" class="w-full h-full object-contain p-2 hover:scale-105 transition-transform duration-300" alt="{{ $borrowing->asset->name }}">
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg class="h-12 w-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs">Tidak ada foto</span>
                        </div>
                    @endif
                </div>
            </div>

            @php 
                // Cek status terlambat
                $isOverdue = false;
                if ($borrowing->status == 'approved' && !$borrowing->returned_at && $borrowing->return_date) {
                    $isOverdue = now()->greaterThan($borrowing->return_date);
                }
            @endphp
            <div class="bg-gradient-to-br {{ $isOverdue ? 'from-red-500 to-red-600' : 'from-blue-500 to-blue-600' }} rounded-lg shadow p-6 text-white">
                <h3 class="text-lg font-bold mb-4 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $borrowing->status == 'approved' && !$borrowing->returned_at ? ($isOverdue ? 'Terlambat' : 'Sisa Waktu') : 'Tanggal Pengembalian' }}
                    </div>
                    
                    {{-- TOMBOL EDIT DURASI (ADMIN ONLY) --}}
                    @if(in_array(auth()->user()->role?->slug, ['admin', 'super_admin']) && $borrowing->status === 'approved' && !$borrowing->returned_at)
                        <button onclick="openExtendModal()" class="text-white hover:text-gray-200 transition p-1 bg-white/20 rounded-full" title="Ubah Durasi / Perpanjang">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                    @endif
                </h3>
                
                <div class="text-center">
                    <div class="text-2xl font-bold font-mono">
                        {{ $borrowing->status == 'approved' && !$borrowing->returned_at ? ($sisaWaktu ?? '-') : ($borrowing->return_date ? $borrowing->return_date->translatedFormat('d F Y, H:i') : '-') }}
                    </div>
                    <p class="text-white text-opacity-80 text-sm mt-2">
                        @if($borrowing->status == 'approved' && !$borrowing->returned_at)
                             Batas Kembali: {{ $borrowing->return_date ? $borrowing->return_date->translatedFormat('d F Y, H:i') : 'Tidak ditentukan' }}
                        @else
                             Batas Waktu
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tertunda
                    </span>
                @endif
            </div>

            {{-- [FITUR BARU] MENU TINDAKAN ADMIN --}}
            @if(in_array(auth()->user()->role?->slug, ['admin', 'super_admin']) && $borrowing->status === 'pending')
                <div class="bg-white rounded-lg shadow p-6 border-t-4 border-yellow-500">
                    <h3 class="text-gray-900 font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Tindakan Admin
                    </h3>
                    <div class="flex flex-col gap-3">
                        <form action="{{ route('borrowing.approve', $borrowing->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-green-700 transition flex items-center justify-center gap-2" onclick="return confirm('Setujui peminjaman ini? Stok akan berkurang.')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Setujui Permintaan
                            </button>
                        </form>
                        
                        <button type="button" onclick="openRejectModal()" class="w-full bg-red-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-red-700 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Tolak Permintaan
                        </button>
                    </div>
                </div>
            @endif

            {{-- TOMBOL RETURN UNTUK ADMIN / USER --}}
            @if(($borrowing->status === 'active' || $borrowing->status === 'approved') && !$borrowing->returned_at)
                @if(in_array(auth()->user()->role?->slug, ['admin', 'super_admin']) || auth()->id() === $borrowing->user_id)
                    <button type="button" onclick="openReturnModal()" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"></path>
                        </svg>
                        Kembalikan Aset
                    </button>
                @endif
            @endif

            {{-- [FITUR BARU] MENU BATALKAN PENGAJUAN (USER OWN) --}}
            @if($borrowing->status === 'pending' && auth()->id() === $borrowing->user_id)
                <div class="bg-white rounded-lg shadow p-6 border-t-4 border-gray-400">
                    <h3 class="text-gray-900 font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Batalkan Pengajuan
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Jika Anda berubah pikiran atau tidak jadi meminjam aset ini, Anda dapat membatalkan pengajuan ini.</p>
                    
                    <form action="{{ route('borrowing.cancel', $borrowing->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-red-50 hover:text-red-600 hover:border-red-200 border border-gray-200 transition flex items-center justify-center gap-2" onclick="return confirm('Yakin ingin membatalkan pengajuan ini? Data akan dihapus.')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Batalkan Pengajuan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL RETURN (PENGEMBALIAN) --}}
<div id="returnModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeReturnModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="returnForm" method="POST" action="{{ in_array(auth()->user()->role?->slug, ['admin', 'super_admin']) ? route('borrowing.return', $borrowing->id) : route('returns.store') }}" enctype="multipart/form-data"
                  x-data="{
                      previews: { img1: null, img2: null, img3: null },
                      handleFile(e, key) {
                          const file = e.target.files[0];
                          if(file) {
                              const reader = new FileReader();
                              reader.onload = (e) => this.previews[key] = e.target.result;
                              reader.readAsDataURL(file);
                          }
                      },
                      removeFile(key, inputId) {
                          this.previews[key] = null;
                          const input = document.getElementById(inputId);
                          if (input) input.value = '';
                      }
                  }">
                @csrf
                {{-- [NEW] Hidden ID for AssetReturnController --}}
                @if(!in_array(auth()->user()->role?->slug, ['admin', 'super_admin']))
                    <input type="hidden" name="asset_request_id" value="{{ $borrowing->id }}">
                @endif
                
                {{-- Header --}}
                <div class="bg-white px-6 pt-6 pb-2">
                    <h3 class="text-xl font-extrabold text-gray-900" id="modal-title">Form Pengembalian Aset</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ in_array(auth()->user()->role?->slug, ['admin', 'super_admin']) ? 'Proses pengembalian langsung.' : 'Ajukan pengembalian untuk diverifikasi Admin.' }}
                    </p>
                </div>

                <div class="px-6 py-4 space-y-6">
                    {{-- Asset Preview Card --}}
                    <div class="flex items-center gap-4 bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                        <div class="h-16 w-16 rounded-lg bg-white border border-indigo-200 flex-shrink-0 overflow-hidden flex items-center justify-center relative">
                            @if($borrowing->asset->image)
                                <img src="{{ asset('storage/' . $borrowing->asset->image) }}" class="w-full h-full object-cover" alt="{{ $borrowing->asset->name }}">
                            @else
                                <svg class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider mb-0.5">Barang yang dikembalikan</p>
                            <h4 class="text-base font-bold text-gray-900 truncate leading-tight">{{ $borrowing->asset->name }}</h4>
                            <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $borrowing->asset->serial_number }}</p>
                        </div>
                    </div>

                    {{-- [NEW] Upload Bukti Foto (Grid 3 Slot) --}}
                    @if(!in_array(auth()->user()->role?->slug, ['admin', 'super_admin']))
                        <div x-data="{
                            dragover: false,
                            handleDrop(e) {
                                this.dragover = false;
                                const files = e.dataTransfer.files;
                                this.processFiles(files);
                            },
                            handleMultiSelect(e) {
                                const files = e.target.files;
                                this.processFiles(files);
                            },
                            processFiles(files) {
                                if (files.length > 3) {
                                    alert('Maksimal 3 foto sekaligus!');
                                    return;
                                }
                                
                                const inputs = ['photo_proof_1Input', 'photo_proof_2Input', 'photo_proof_3Input'];
                                const keys = ['img1', 'img2', 'img3'];

                                for (let i = 0; i < files.length; i++) {
                                    if (i >= 3) break;
                                    
                                    const file = files[i];
                                    const inputId = inputs[i];
                                    const key = keys[i];

                                    // Update Input File
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(file);
                                    document.getElementById(inputId).files = dataTransfer.files;

                                    // Update Preview
                                    const reader = new FileReader();
                                    reader.onload = (e) => this.previews[key] = e.target.result;
                                    reader.readAsDataURL(file);
                                }
                            }
                        }">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-900">Bukti Foto Aset <span class="text-red-500">*</span></label>
                                <button type="button" @click="document.getElementById('multiFileInput').click()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 transition-colors">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Pilih 3 Foto Sekaligus
                                </button>
                                <input type="file" id="multiFileInput" multiple accept="image/*" class="hidden" @change="handleMultiSelect($event)">
                            </div>
                            
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(['photo_proof_1' => 'Foto Utama*', 'photo_proof_2' => 'Samping', 'photo_proof_3' => 'Belakang'] as $key => $label)
                                    <div class="relative group">
                                        <div class="w-full h-24 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center overflow-hidden relative cursor-pointer hover:bg-white hover:border-indigo-500 transition-all"
                                             :class="{'border-indigo-500 bg-white ring-2 ring-indigo-100': previews.img{{ $loop->iteration }}}"
                                             @click="document.getElementById('{{ $key }}Input').click()">
                                            
                                            <template x-if="!previews.img{{ $loop->iteration }}">
                                                <div class="text-center p-2">
                                                    <svg class="h-6 w-6 text-gray-400 mx-auto mb-1 group-hover:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span class="text-[10px] text-gray-500 font-bold group-hover:text-indigo-600 block leading-tight">{{ $label }}</span>
                                                </div>
                                            </template>
                                            
                                            <template x-if="previews.img{{ $loop->iteration }}">
                                                <div class="relative w-full h-full group">
                                                    <img :src="previews.img{{ $loop->iteration }}" class="w-full h-full object-cover">
                                                    {{-- Remove Button --}}
                                                    <button type="button" 
                                                        @click.stop="removeFile('img{{ $loop->iteration }}', '{{ $key }}Input')"
                                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-0.5 shadow-sm hover:bg-red-600 transition-colors focus:outline-none">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </template>

                                            <input id="{{ $key }}Input" type="file" name="{{ $key }}" class="hidden" 
                                                accept="image/*"
                                                {{ $loop->first ? 'required' : '' }}
                                                @change="handleFile($event, 'img{{ $loop->iteration }}')">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2">Unggah foto kondisi terakhir aset. Gunakan tombol diatas untuk upload massal.</p>
                        </div>
                    @endif

                    {{-- Kondisi --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Kondisi Barang Saat Ini</label>
                        <div class="relative">
                            <select name="condition" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all appearance-none pl-3 pr-10 font-medium" required>
                                <option value="" disabled {{ old('condition') ? '' : 'selected' }}>-- Pilih Kondisi --</option>
                                <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Baik (Layak Pakai)</option>
                                <option value="maintenance" {{ old('condition') == 'maintenance' ? 'selected' : '' }}>Rusak Ringan (Maintenance)</option>
                                <option value="broken" {{ old('condition') == 'broken' ? 'selected' : '' }}>Rusak Berat (Broken)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Catatan Tambahan</label>
                        <div class="relative">
                            <textarea name="notes" rows="3" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:ring-0 transition-all font-medium" placeholder="Contoh: Ada lecet sedikit di bagian bawah...">{{ old('notes') }}</textarea>
                            <div class="absolute bottom-2 right-2 text-gray-400 pointer-events-none">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-2xl">
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent px-5 py-2.5 bg-indigo-600 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        {{ in_array(auth()->user()->role?->slug, ['admin', 'super_admin']) ? 'Kembalikan Sekarang' : 'Ajukan Pengembalian' }}
                    </button>
                    <button type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-5 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all" onclick="closeReturnModal()">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL REJECT (PENOLAKAN) --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full animate-fade-in-down">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex items-center justify-between rounded-t-lg">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Tolak Peminjaman
            </h3>
            <button type="button" onclick="closeRejectModal()" class="text-white hover:text-red-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="rejectForm" method="POST" action="{{ route('borrowing.reject', $borrowing->id) }}" class="p-6">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="admin_note" rows="4" placeholder="Contoh: Stok sedang menipis, atau keperluan kurang jelas..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none" required></textarea>
                <p class="text-xs text-gray-500 mt-1">Alasan ini akan dapat dilihat oleh peminjam.</p>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
                    Konfirmasi Tolak
                </button>
            </div>
        </form>
    </div>
    </div>
</div>

{{-- MODAL EXTEND (PERPANJANG DURASI) --}}
<div id="extendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full animate-fade-in-down">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between rounded-t-lg">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Ubah Durasi Peminjaman
            </h3>
            <button type="button" onclick="closeExtendModal()" class="text-white hover:text-blue-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('borrowing.extend', $borrowing->id) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Tanggal Baru <span class="text-red-500">*</span></label>
                    <input type="date" name="new_return_date" 
                        value="{{ $borrowing->return_date ? \Carbon\Carbon::parse($borrowing->return_date)->format('Y-m-d') : '' }}" 
                        min="{{ now()->format('Y-m-d') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Jam Baru <span class="text-red-500">*</span></label>
                    <input type="time" name="new_return_time" 
                        value="{{ $borrowing->return_date ? \Carbon\Carbon::parse($borrowing->return_date)->format('H:i') : '' }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
            </div>
            <p class="text-xs text-gray-500 -mt-2 mb-4">
                Waktu baru harus setelah waktu saat ini.
            </p>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Alasan Perubahan (Opsional)</label>
                <textarea name="reason_extend" rows="2" placeholder="Contoh: User meminta perpanjangan..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeExtendModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // === RETURN MODAL ===
    function openReturnModal() {
        document.getElementById('returnModal').classList.remove('hidden');
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
    }

    // [New] Auto-open return modal if validation errors exist
    @if($errors->any() && !request()->is('borrowing/*')) 
        document.addEventListener('DOMContentLoaded', function() {
            openReturnModal();
        });
    @endif

    // [New] Auto-open return modal if validation errors exist
    @if($errors->any() && !request()->is('borrowing/*')) 
        // Note: Ensure this doesn't conflict with other forms if any. 
        // Given this page is specific to one borrowing, it's likely safe.
        document.addEventListener('DOMContentLoaded', function() {
            openReturnModal();
        });
    @endif

    // === REJECT MODAL ===
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        let returnModal = document.getElementById('returnModal');
        if (event.target == returnModal) {
            closeReturnModal();
        }
        if (event.target == rejectModal) {
            closeRejectModal();
        }
        if (event.target == document.getElementById('extendModal')) {
            closeExtendModal();
        }
    }

    // === EXTEND MODAL ===
    function openExtendModal() {
        document.getElementById('extendModal').classList.remove('hidden');
    }

    function closeExtendModal() {
        document.getElementById('extendModal').classList.add('hidden');
    }
</script>
@endsection