{{-- HERO SECTION: BUTUH ALAT KERJA --}}
<div class="mt-4 mb-8">
    <div class="relative bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden">
        {{-- Pattern Background --}}
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg width="100%" height="100%">
                <defs>
                    <pattern id="hero-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1" fill="#fff"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#hero-pattern)"/>
            </svg>
        </div>
        
        <div class="relative z-10 px-8 py-10 md:py-12 md:px-12 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left text-white space-y-3">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Butuh Alat Kerja Baru?</h2>
                <p class="text-indigo-100 text-lg max-w-2xl">Ajukan peminjaman aset IT dengan mudah dan cepat untuk menunjang produktivitas kerja Anda.</p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('assets.index') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-bold rounded-full text-indigo-700 bg-white hover:bg-gray-50 hover:scale-105 transition-transform duration-200 shadow-lg group">
                    <svg class="w-6 h-6 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Pinjam Aset Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

{{-- STATS CARDS --}}
<div class="grid gap-6 mb-8 md:grid-cols-3">
    <a href="{{ route('assets.my') }}" class="group flex items-center justify-between p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-indigo-200 cursor-pointer">
        <div class="flex items-center">
            <div class="p-3 mr-4 text-indigo-500 bg-indigo-50 rounded-full group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-500">Aset Saya Saat Ini</p>
                {{-- Gunakan $myAssetsCount (variabel baru) --}}
                <p class="text-2xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $myAssetsCount ?? 0 }} Unit</p>
            </div>
        </div>
        <div class="flex items-center text-xs font-semibold text-gray-400 group-hover:text-indigo-500">
            Detail <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </div>
    </a>

    <a href="{{ route('borrowing.history') }}" class="group flex items-center justify-between p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-yellow-200 cursor-pointer">
        <div class="flex items-center">
            <div class="p-3 mr-4 text-yellow-500 bg-yellow-50 rounded-full group-hover:bg-yellow-500 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-500">Menunggu Persetujuan</p>
                <p class="text-2xl font-bold text-gray-800 group-hover:text-yellow-600 transition-colors">{{ $pendingRequests ?? 0 }} Pengajuan</p>
            </div>
        </div>
        <div class="text-xs font-bold text-yellow-600 bg-yellow-100 px-2 py-1 rounded group-hover:bg-yellow-200">
            Cek Status
        </div>
    </a>

    <a href="{{ route('borrowing.history') }}" class="group flex items-center justify-between p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-blue-200 cursor-pointer">
        <div class="flex items-center">
            <div class="p-3 mr-4 text-blue-500 bg-blue-50 rounded-full group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <p class="mb-1 text-sm font-medium text-gray-500">Total Riwayat Pinjam</p>
                <p class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">Lihat Semua</p>
            </div>
        </div>
        <svg class="w-5 h-5 text-gray-300 group-hover:text-blue-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </a>
</div>

{{-- TABEL RIWAYAT FULL WIDTH --}}
<div class="w-full mb-8 overflow-hidden rounded-xl shadow-sm border border-gray-100 bg-white">
    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div>
            <h3 class="font-bold text-gray-800 text-lg">Riwayat Permintaan Terkini</h3>
            <p class="text-sm text-gray-500">Pantau status pengajuan aset Anda di sini.</p>
        </div>
        <a href="{{ route('borrowing.history') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center group">
            Lihat Selengkapnya
            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
        </a>
    </div>
    
    <div class="w-full overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-6 py-4">Aset yang Diminta</th>
                    <th class="px-6 py-4">Tanggal Pengajuan</th>
                    <th class="px-6 py-4">Durasi / Rencana Kembali</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($recentActivities ?? [] as $activity)
                <tr class="text-gray-700 hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center text-sm">
                            <div class="relative hidden w-10 h-10 mr-3 rounded bg-indigo-50 md:block shrink-0">
                                @if(isset($activity->asset->image) && $activity->asset->image)
                                    <img class="object-cover w-full h-full rounded" src="{{ asset('storage/' . $activity->asset->image) }}" alt="" loading="lazy" />
                                @else
                                    <div class="flex items-center justify-center w-full h-full text-indigo-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $activity->asset->name ?? 'Aset Tidak Ditemukan' }}</p>
                                <p class="text-xs text-gray-500">{{ $activity->asset->serial_number ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        {{ \Carbon\Carbon::parse($activity->created_at)->translatedFormat('d M Y') }}
                        <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($activity->created_at)->format('H:i') }} WIB</p>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($activity->return_date)
                            <span class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($activity->return_date)->translatedFormat('d M Y') }}</span>
                            <p class="text-xs text-gray-500">
                                ({{ \Carbon\Carbon::parse($activity->created_at)->diff(\Carbon\Carbon::parse($activity->return_date))->format('%d Hari %h Jam %i Menit') }})
                            </p>
                        @else
                            <span class="text-gray-400 italic">Tidak ditentukan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($activity->status == 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Menunggu</span>
                        @elseif($activity->status == 'approved' && !$activity->returned_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Dipinjam</span>
                        @elseif($activity->status == 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Ditolak</span>
                        @elseif($activity->returned_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">Selesai</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('borrowing.show', $activity->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm hover:underline">Detail &rarr;</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada riwayat permintaan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 2. LOG AKTIVITAS SISTEM --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Log Aktivitas Sistem
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Riwayat aktivitas publik terkini (Transparan).</p>
        </div>
        
        {{-- Search Form --}}
        <form action="{{ route('dashboard') }}" method="GET" class="w-full md:w-auto">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full md:w-64 pl-9 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" 
                    placeholder="Cari aktivitas...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                @if(request('search'))
                    <a href="{{ route('dashboard') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    {{-- Scrollable Table Container --}}
    <div class="overflow-x-auto max-h-[500px] overflow-y-auto" id="user-activity-log-scroll">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
                <tr>
                    <th class="px-6 py-3 font-semibold">User</th>
                    <th class="px-6 py-3 font-semibold">Aksi</th>
                    <th class="px-6 py-3 font-semibold">Detail</th>
                    <th class="px-6 py-3 font-semibold text-right">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($activities ?? [] as $log)
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

                    {{-- Waktu (Format Lengkap) --}}
                    <td class="px-6 py-3 text-right whitespace-nowrap">
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-bold text-gray-900">
                                {{ $log->created_at->translatedFormat('d M Y') }}
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
                        Tidak ada aktivitas publik.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Footer Info (No Pagination - All Data Displayed) --}}
    <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl flex justify-between items-center">
        <span class="text-xs text-gray-500">Menampilkan semua data (Auto-scroll) • Total {{ is_countable($activities) ? count($activities) : 0 }} Aktivitas</span>
        <button onclick="document.getElementById('user-activity-log-scroll').scrollTo({top: 0, behavior: 'smooth'})" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
            Ke Atas
        </button>
    </div>
</div>