{{-- LOG AKTIVITAS SISTEM LENGKAP (SEARCHABLE & SCROLLABLE) --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8" id="activity-log">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Log Aktivitas Sistem
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Riwayat lengkap semua transaksi aset (Transparan).</p>
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
    
    {{-- Scrollable Table Container --}}
    <div class="overflow-x-auto max-h-[500px] overflow-y-auto" id="activity-log-scroll">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
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
                                <p class="font-medium text-gray-900">{{ $log->user?->name ?? 'Sistem' }}</p>
                                <p class="text-[10px] text-gray-500">{{ $log->user?->role?->name ?? '-' }}</p>
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
                                // Status Tambahan
                                'status_change' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Ubah Status'],
                                'check_out'     => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Barang Keluar'],
                                'check_in'      => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700', 'label' => 'Barang Masuk'],
                                'moved'         => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-700', 'label' => 'Dipindahkan'],
                                'maintenance'   => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Perbaikan'],
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
                        Tidak ada aktivitas yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer Info (Scrollable Indicator) --}}
    <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl flex justify-between items-center">
        <span class="text-xs text-gray-500">Menampilkan semua data (Auto-scroll) • Total {{ is_countable($activities) ? count($activities) : 0 }} Aktivitas</span>
        <button onclick="document.getElementById('activity-log-scroll').scrollTo({top: 0, behavior: 'smooth'})" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
            Ke Atas
        </button>
    </div>
</div>

{{-- SCRIPT: Real-time Search (AJAX) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search_log"]');
        const listContainer = document.getElementById('activity-log-scroll');
        let debounceTimer;

        if(searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value;

                debounceTimer = setTimeout(() => {
                    // Update URL tanpa reload
                    const url = new URL(window.location.href);
                    url.searchParams.set('search_log', query);
                    url.searchParams.set('scrollTo', 'activity-log');
                    window.history.pushState({}, '', url);

                    // Fetch Data Baru
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        // Ambil table body kalau ada ID, atau ambil content dalam wrapper
                        // Sesuai struktur di atas kita ambil isi dari activity-log-scroll
                        const newTableContent = doc.getElementById('activity-log-scroll')?.innerHTML;
                        
                        if(listContainer && newTableContent) {
                            listContainer.innerHTML = newTableContent;
                        }
                    })
                    .catch(error => console.error('Error fetching search results:', error));
                }, 300);
            });
        }
    });
</script>
