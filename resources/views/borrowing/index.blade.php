@extends('layouts.main')

@section('container')
<div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Peminjaman</h1>
        <p class="mt-2 text-gray-600">Kelola semua permintaan peminjaman aset</p>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        {{-- 1. Total Peminjaman --}}
        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition group relative">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3 group-hover:bg-blue-600 transition">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Peminjaman</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $statistics['total'] ?? 0 }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Peminjaman Aktif --}}
        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition group relative">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3 group-hover:bg-green-600 transition">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <dt class="text-sm font-medium text-gray-500 truncate">Peminjaman Aktif</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $statistics['active'] ?? 0 }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Tertunda --}}
        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition group relative">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3 group-hover:bg-yellow-600 transition">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <dt class="text-sm font-medium text-gray-500 truncate">Tertunda</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $statistics['pending'] ?? 0 }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Dikembalikan --}}
        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition group relative">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-md p-3 group-hover:bg-red-600 transition">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <dt class="text-sm font-medium text-gray-500 truncate">Dikembalikan</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $statistics['returned'] ?? 0 }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <form method="GET" action="{{ route('borrowing.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Peminjam / Aset</label>
                <div class="relative">
                    <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" placeholder="Nama / Aset" class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ request('search') }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="borrowing_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('borrowing_status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="returned" {{ request('borrowing_status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="rejected" {{ request('borrowing_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('borrowing.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aset</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Peminjaman</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($borrowings as $borrowing)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- Kolom Peminjam --}}
                        <td class="px-4 py-4 align-top">
                            <div class="flex items-start">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mt-1">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $borrowing->user->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500 break-words">{{ $borrowing->user->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Kolom Aset --}}
                        <td class="px-4 py-4 align-top">
                            <div class="flex items-start gap-2">
                                <div class="h-8 w-8 rounded bg-indigo-100 flex items-center justify-center shrink-0 mt-1">
                                    <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900 block">{{ $borrowing->asset->name ?? 'N/A' }}</span>
                            </div>
                        </td>

                        {{-- Kolom Tanggal Peminjaman (created_at = Waktu Input Asli) --}}
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $borrowing->created_at->format('d M Y H:i') }} WIB
                        </td>

                        {{-- Kolom Durasi (FIX MINUS & FORMAT) --}}
                        <td class="px-4 py-4 align-top">
                            @if($borrowing->borrowing_status === 'active')
                                <div class="text-sm">
                                    <span class="font-medium text-gray-900" id="duration-{{ $borrowing->id }}">Menghitung...</span>
                                </div>
                                <script>
                                    (function() {
                                        // Gunakan Batas Kembali (return_date) sebagai target
                                        const returnDateStr = '{{ $borrowing->return_date ? $borrowing->return_date->toIso8601String() : '' }}';
                                        const durationEl = document.getElementById('duration-{{ $borrowing->id }}');
                                        
                                        if (!returnDateStr) {
                                            durationEl.textContent = 'Tidak ada batas waktu';
                                            return;
                                        }

                                        const targetDate = new Date(returnDateStr);

                                        function updateDuration() {
                                            const now = new Date();
                                            const diffMs = targetDate - now; // Positif = Sisa Waktu, Negatif = Terlambat
                                            
                                            const isOverdue = diffMs < 0;
                                            const absDiff = Math.abs(diffMs);
                                            
                                            const days = Math.floor(absDiff / (1000 * 60 * 60 * 24));
                                            const hours = Math.floor((absDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                            const minutes = Math.floor((absDiff % (1000 * 60 * 60)) / (1000 * 60));
                                            
                                            if (isOverdue) {
                                                durationEl.innerHTML = `<span class="text-red-600 font-bold">Terlambat ${days} Hari ${hours} Jam</span>`;
                                            } else {
                                                durationEl.innerHTML = `<span class="text-blue-600 font-medium">Sisa ${days} Hari ${hours} Jam ${minutes} Menit</span>`;
                                            }
                                        }
                                        updateDuration();
                                        setInterval(updateDuration, 60000);
                                    })();
                                </script>
                            @elseif($borrowing->borrowing_status === 'returned' && $borrowing->returned_at)
                                {{-- Hitung durasi total untuk yang sudah kembali --}}
                                @php
                                    $start = \Carbon\Carbon::parse($borrowing->borrowed_at ?? $borrowing->created_at);
                                    $end = \Carbon\Carbon::parse($borrowing->returned_at);
                                    $diff = $start->diff($end);
                                @endphp
                                <span class="text-sm text-gray-900">{{ $diff->d }} Hari {{ $diff->h }} Jam {{ $diff->i }} Menit</span>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>

                        {{-- Kolom Status --}}
                        <td class="px-4 py-4 text-center align-top">
                            @if($borrowing->borrowing_status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2 animate-pulse"></span>
                                    Aktif
                                </span>
                            @elseif($borrowing->borrowing_status === 'returned')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Dikembalikan
                                </span>
                            @elseif($borrowing->borrowing_status === 'rejected')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 9a1 1 0 100-2 1 1 0 000 2zm5-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Tertunda
                                </span>
                            @endif
                        </td>

                        {{-- Kolom Aksi --}}
                        <td class="px-4 py-4 text-sm text-center align-top">
                            <div class="flex justify-center gap-2 flex-col sm:flex-row">
                                <a href="{{ route('borrowing.show', $borrowing->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition border border-blue-200" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                @if($borrowing->borrowing_status === 'active')
                                    <button type="button" onclick="openReturnModal({{ $borrowing->id }})" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition border border-red-200" title="Kembalikan Aset">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="mt-2 text-gray-500">Tidak ada data peminjaman</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $borrowings->links() }}
    </div>
</div>

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

        <form id="returnForm" method="POST" class="p-6">
            @csrf
            @csrf
            {{-- @method('PUT') removed because route is POST --}}

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
    let currentBorrowingId = null;

    function openReturnModal(borrowingId) {
        currentBorrowingId = borrowingId;
        const form = document.getElementById('returnForm');
        // Fix rute agar sesuai web.php
        const baseUrl = window.location.origin;
        form.action = `${baseUrl}/borrowing/${borrowingId}/return`;
        form.reset();
        document.getElementById('returnModal').classList.remove('hidden');
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.add('hidden');
        currentBorrowingId = null;
    }

    // Close modal when clicking outside
    document.getElementById('returnModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeReturnModal();
        }
    });
</script>
@endsection