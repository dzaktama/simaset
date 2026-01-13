@extends('layouts.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Peminjaman</h1>
                <p class="text-gray-600 mt-2">#{{ $borrowing->id }}</p>
            </div>
            <a href="{{ route('borrowing.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Status Badge -->
        <div>
            @if($borrowing_status === 'active')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2 animate-pulse"></span>
                    Peminjaman Aktif
                </span>
            @elseif($borrowing_status === 'returned')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Sudah Dikembalikan
                </span>
            @elseif($borrowing_status === 'rejected')
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
        <!-- Main Content (2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Peminjam Card -->
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
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Nama Lengkap</p>
                            <p class="font-semibold text-gray-900">{{ $borrowing->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium text-gray-900">{{ $borrowing->user->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Departemen</p>
                            <p class="font-medium text-gray-900">{{ $borrowing->user->department ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aset Card -->
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
                        <p class="font-semibold text-lg text-gray-900">{{ $borrowing->asset->name ?? 'N/A' }}</p>
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
                                <span class="text-gray-500">-</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jumlah</p>
                            <p class="font-medium text-gray-900">{{ $borrowing->quantity ?? 1 }} Unit</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-blue-600">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-transparent border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Timeline Peminjaman
                    </h2>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-6">
                        <!-- Diajukan -->
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="w-1 h-12 bg-gray-300 mt-2"></div>
                            </div>
                            <div class="py-2">
                                <p class="font-semibold text-gray-900">Permintaan Diajukan</p>
                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($borrowing->request_date ?? $borrowing->created_at)->format('d F Y, H:i') }}</p>
                            </div>
                        </div>

                        <!-- Disetujui/Ditolak -->
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full {{ $borrowing_status === 'rejected' ? 'bg-red-100' : 'bg-green-100' }} flex items-center justify-center">
                                    @if($borrowing_status === 'rejected')
                                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                                @if($borrowing_status !== 'rejected' && $borrowing_status !== 'pending')
                                    <div class="w-1 h-12 bg-gray-300 mt-2"></div>
                                @endif
                            </div>
                            <div class="py-2">
                                <p class="font-semibold text-gray-900">
                                    {{ $borrowing_status === 'rejected' ? 'Permintaan Ditolak' : 'Permintaan Disetujui' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ $borrowing->approved_at ? \Carbon\Carbon::parse($borrowing->approved_at)->format('d F Y, H:i') : '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Aktif/Dikembalikan -->
                        @if($borrowing_status !== 'pending' && $borrowing_status !== 'rejected')
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full {{ $borrowing_status === 'active' ? 'bg-green-100 animate-pulse' : 'bg-blue-100' }} flex items-center justify-center">
                                        @if($borrowing_status === 'active')
                                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M13 10V3L4 14h7v7l9-11h-7z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="py-2">
                                    <p class="font-semibold text-gray-900">
                                        {{ $borrowing_status === 'active' ? 'Sedang Dipinjam' : 'Dikembalikan' }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $borrowing->returned_at ? \Carbon\Carbon::parse($borrowing->returned_at)->format('d F Y, H:i') : '-' }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Alasan Peminjaman -->
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

            <!-- Catatan Pengembalian -->
            @if($borrowing->return_notes && $borrowing_status === 'returned')
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

        <!-- Sidebar (1 column) -->
        <div class="space-y-6">
            <!-- Duration Card -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow p-6 text-white">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Durasi
                </h3>
                @if($borrowing_status === 'active')
                    <div class="text-center">
                        <div class="text-3xl font-bold font-mono" id="countdown">Menghitung...</div>
                        <p class="text-orange-100 text-sm mt-2">Hari Jam Menit Detik</p>
                    </div>
                @else
                    <div class="text-center">
                        <div class="text-3xl font-bold">
                            @if($borrowing->returned_at)
                                {{ \Carbon\Carbon::parse($borrowing->returned_at)->diffInDays(\Carbon\Carbon::parse($borrowing->created_at)) }} Hari
                            @else
                                -
                            @endif
                        </div>
                        <p class="text-orange-100 text-sm mt-2">Total durasi peminjaman</p>
                    </div>
                @endif
            </div>

            <!-- ID Card -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-gray-400">
                <p class="text-sm text-gray-600 mb-1">ID Peminjaman</p>
                <p class="text-2xl font-bold text-gray-900 font-mono">#{{ $borrowing->id }}</p>
            </div>

            <!-- Quantity Card -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-400">
                <p class="text-sm text-gray-600 mb-1">Jumlah Dipinjam</p>
                <p class="text-2xl font-bold text-gray-900">{{ $borrowing->quantity ?? 1 }} Unit</p>
            </div>

            <!-- Status Badge Large -->
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-3">Status Saat Ini</p>
                @if($borrowing_status === 'active')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-100 text-green-800 font-semibold">
                        <span class="w-3 h-3 bg-green-600 rounded-full animate-pulse"></span>
                        Aktif
                    </span>
                @elseif($borrowing_status === 'returned')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-100 text-blue-800 font-semibold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Dikembalikan
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

            <!-- Action Button -->
            @if($borrowing_status === 'active')
                <button type="button" onclick="openReturnModal()" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 12l-8 8M6 20l8-8m0-8L6 4m8-8l-8 8"></path>
                    </svg>
                    Kembalikan Aset
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Return Modal -->
<div id="returnModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 12l-8 8M6 20l8-8m0-8L6 4m8-8l-8 8"></path>
                </svg>
                Kembalikan Aset
            </h3>
            <button type="button" onclick="closeReturnModal()" class="text-white hover:text-red-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="returnForm" method="POST" action="/borrowing/{{ $borrowing->id }}/return" class="p-6">
            @csrf
            @method('PUT')

            <!-- Condition Selection -->
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

            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Catatan</label>
                <textarea name="notes" rows="4" placeholder="Jelaskan kondisi aset atau kerusakan yang ditemukan..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
            </div>

            <!-- Buttons -->
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

    // Countdown Timer
    @if($borrowing_status === 'active')
        (function() {
            const startDate = new Date('{{ \Carbon\Carbon::parse($borrowing->request_date ?? $borrowing->created_at)->toIso8601String() }}');
            const countdownEl = document.getElementById('countdown');
            
            function updateCountdown() {
                const now = new Date();
                const diffMs = now - startDate;
                const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);
                
                countdownEl.textContent = days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's';
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        })();
    @endif
</script>
@endsection
