{{-- 1. TABEL PERMINTAAN PENDING (FULL WIDTH) --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
    {{-- Header Tabel --}}
    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-xl">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Permintaan Pending</h3>
            <p class="text-xs text-gray-500 mt-1">Daftar permintaan aset yang menunggu persetujuan Anda.</p>
        </div>
        <a href="{{ route('borrowing.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition">
            Lihat Semua
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 font-semibold">Peminjam</th>
                    <th class="px-6 py-3 font-semibold">Aset</th>
                    <th class="px-6 py-3 font-semibold">Alasan & Durasi</th>
                    <th class="px-6 py-3 font-semibold">Waktu Request</th>
                    <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($recentRequests as $req)
                <tr class="hover:bg-gray-50 transition group">
                    {{-- Kolom Peminjam --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs shrink-0">
                                {{ substr($req->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $req->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $req->user->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Kolom Aset --}}
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-semibold text-indigo-600">{{ $req->asset->name }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs font-mono text-gray-500 bg-gray-100 px-1.5 rounded border border-gray-200">
                                    {{ $req->asset->serial_number }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $req->quantity }} Unit</span>
                            </div>
                        </div>
                    </td>

                    {{-- Kolom Alasan & Durasi --}}
                    <td class="px-6 py-4">
                        <div class="max-w-xs">
                            <div class="flex items-start gap-1.5 mb-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                <p class="text-xs text-gray-600 italic leading-relaxed">
                                    "{{ Str::limit($req->reason, 60) }}"
                                </p>
                            </div>
                            @if($req->return_date)
                                <div class="inline-flex items-center gap-1.5 text-[10px] font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded border border-blue-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Kembali: {{ \Carbon\Carbon::parse($req->return_date)->translatedFormat('d M Y') }}
                                </div>
                            @endif
                        </div>
                    </td>

                    {{-- Kolom Waktu (Updated Format) --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900">
                                {{ $req->created_at->translatedFormat('d M Y') }}
                            </span>
                            <span class="text-xs text-gray-500 font-mono flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $req->created_at->format('H:i') }} WIB
                            </span>
                        </div>
                    </td>

                    {{-- Kolom Aksi --}}
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2 opacity-100 sm:opacity-60 sm:group-hover:opacity-100 transition-opacity duration-200">
                            {{-- Tombol Detail (Redirect ke Show) --}}
                            <a href="{{ route('borrowing.show', $req->id) }}" 
                               class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition" 
                               title="Lihat Detail Lengkap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>

                            {{-- Tombol Approve --}}
                            <form action="{{ route('borrowing.approve', $req->id) }}" method="POST" onsubmit="return confirm('Yakin setujui permintaan ini? Stok aset akan berkurang.')">
                                @csrf
                                <button type="submit" 
                                        class="p-1.5 rounded-lg border border-green-200 text-green-600 hover:text-white hover:bg-green-600 hover:border-green-600 transition shadow-sm" 
                                        title="Setujui Permintaan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                            </form>

                            {{-- Tombol Reject (Pake Form Langsung kalau gapake Modal JS) --}}
                            {{-- Tapi sebaiknya pakai Modal Reject di `modals.blade.php` --}}
                            <button onclick="openRejectModal({{ $req->id }}, '{{ $req->user->name }}', '{{ $req->asset->name }}')" 
                                    class="p-1.5 rounded-lg border border-red-200 text-red-600 hover:text-white hover:bg-red-600 hover:border-red-600 transition shadow-sm" 
                                    title="Tolak Permintaan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center bg-gray-50/30">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-gray-900 font-medium">Tidak ada permintaan baru</p>
                            <p class="text-xs text-gray-500 mt-1">Semua permintaan sudah diproses.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 2. LOG AKTIVITAS SISTEM LENGKAP (SEARCHABLE & PAGINATED) --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8" id="activity-log">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Log Aktivitas Sistem
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Riwayat lengkap semua transaksi aset.</p>
        </div>

        {{-- Form Pencarian Log --}}
        <form action="{{ route('dashboard') }}" method="GET" class="relative w-full sm:w-64">
            <input type="text" name="search_log" value="{{ request('search_log') }}" placeholder="Cari aktivitas..." 
                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            {{-- Hidden input agar tetap di anchor log --}}
            <input type="hidden" name="scrollTo" value="activity-log"> 
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 font-semibold">User / Aktor</th>
                    <th class="px-6 py-3 font-semibold">Aksi</th>
                    <th class="px-6 py-3 font-semibold">Detail / Catatan</th>
                    <th class="px-6 py-3 font-semibold text-right">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($activities as $log)
                <tr class="hover:bg-gray-50 transition">
                    {{-- User --}}
                    <td class="px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold text-gray-600 bg-gray-200">
                                {{ substr($log->user->name ?? 'S', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $log->user->name ?? 'Sistem' }}</p>
                                <p class="text-[10px] text-gray-500">{{ $log->user->role ?? '-' }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Aksi (Badge) --}}
                    <td class="px-6 py-3 whitespace-nowrap">
                        @php
                            $badges = [
                                'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Disetujui'],
                                'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Ditolak'],
                                'returned' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Dikembalikan'],
                                'created'  => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'label' => 'Dibuat'],
                                'updated'  => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Diupdate'],
                                'deleted'  => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Dihapus'],
                            ];
                            $type = $badges[$log->action] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'label' => $log->action];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $type['bg'] }} {{ $type['text'] }}">
                            {{ $type['label'] }}
                        </span>
                    </td>

                    {{-- Detail --}}
                    <td class="px-6 py-3">
                        <p class="text-indigo-600 font-medium text-xs mb-0.5">{{ $log->asset->name ?? 'Aset Tidak Dikenal' }}</p>
                        <p class="text-xs text-gray-500 italic truncate max-w-xs">
                            "{{ $log->notes ?? '-' }}"
                        </p>
                    </td>

                    {{-- Waktu (Format Baru) --}}
                    <td class="px-6 py-3 text-right whitespace-nowrap">
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-bold text-gray-900">
                                {{ $log->created_at->format('d M Y') }}
                            </span>
                            <span class="text-xs text-gray-500 font-mono">
                                {{ $log->created_at->format('H:i') }} WIB
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                        Tidak ada aktivitas yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $activities->links() }}
    </div>
</div>