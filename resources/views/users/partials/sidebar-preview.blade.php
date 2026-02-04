<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-full flex flex-col transition-all duration-300">
    {{-- Header Preview --}}
    <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
        <div class="flex items-center">
            <div class="bg-gray-200 p-1.5 rounded-md mr-3 text-gray-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
            </div>
            <h3 class="text-base font-bold text-gray-800">Preview Sidebar</h3>
        </div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md border border-indigo-100">
            Live View
        </span>
    </div>

    {{-- Sidebar Content Mockup --}}
    <div class="p-4 space-y-2 bg-white flex-grow overflow-y-auto max-h-[600px] custom-scrollbar">
        
        {{-- Intro Text --}}
        <div class="mb-4 text-center">
            <p class="text-xs text-gray-500">Sidebar akan terlihat seperti ini oleh user:</p>
        </div>

        {{-- 1. MASTER DATA --}}
        <div x-show="selectedPermissions.some(p => ['dashboard.view', 'asset.view', 'asset.map'].includes(p))" 
             class="border border-gray-100 rounded-lg overflow-hidden mb-3">
             <div class="px-3 py-2 bg-gray-50 flex items-center gap-2 border-b border-gray-100">
                <svg class="w-3 h-3 text-gray-400 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Master</p>
             </div>
             <div class="bg-white pl-4 py-1 space-y-0.5">
                <div x-show="selectedPermissions.includes('dashboard.view')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        <span>Dashboard</span>
                    </div>
                    <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'dashboard.view')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('dashboard.stats')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        <span>Dashboard Gudang</span>
                    </div>
                    <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'dashboard.stats')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('asset.view')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        <span>Katalog Aset</span>
                    </div>
                    <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'asset.view')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('asset.map')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span>Lokasi Barang</span>
                    </div>
                    <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'asset.map')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
             </div>
        </div>

        {{-- 2. TRANSAKSI --}}
        <div x-show="selectedPermissions.some(p => ['chat.access', 'asset.create', 'maintenance.create', 'borrow.action', 'return.verify', 'asset.edit', 'maintenance.action'].includes(p))"
             class="border border-gray-100 rounded-lg overflow-hidden mb-3">
             <div class="px-3 py-2 bg-gray-50 flex items-center gap-2 border-b border-gray-100">
                <svg class="w-3 h-3 text-gray-400 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Transaksi</p>
             </div>
             <div class="bg-white pl-4 py-1 space-y-0.5">
                <div x-show="selectedPermissions.includes('chat.access')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        <span>Pesan & Diskusi</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'chat.access')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('asset.create')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Input Aset Baru</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'asset.create')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('maintenance.create')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        <span>Lapor Kerusakan</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'maintenance.create')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('borrow.action')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        <span>Approval Peminjaman</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'borrow.action')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('return.verify')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                         <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Verifikasi Pengembalian</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'return.verify')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('asset.edit')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        <span>Mutasi Aset</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'asset.edit')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('maintenance.action')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Perbaikan Barang</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'maintenance.action')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
             </div>
        </div>

        {{-- 3. LAPORAN --}}
        <div x-show="selectedPermissions.some(p => ['report.view', 'borrow.view'].includes(p))"
             class="border border-gray-100 rounded-lg overflow-hidden mb-3">
             <div class="px-3 py-2 bg-gray-50 flex items-center gap-2 border-b border-gray-100">
                <svg class="w-3 h-3 text-gray-400 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Laporan</p>
             </div>
             <div class="bg-white pl-4 py-1 space-y-0.5">
                <div x-show="currentRole === 'admin' || currentRole === 'super_admin'" class="flex items-center px-3 py-1.5 text-[13px] font-medium text-gray-600">
                    <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                    <span>Pusat Data</span>
                </div>
                <div x-show="selectedPermissions.includes('report.view')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span>Laporan & Audit</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'report.view')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('asset.edit')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Riwayat Pindah</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'asset.edit')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('borrow.view')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Riwayat Peminjaman</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'borrow.view')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
             </div>
        </div>

        {{-- 4. UTILITAS --}}
        <div x-show="selectedPermissions.some(p => ['user.view', 'user.create'].includes(p)) || true" 
             class="border border-gray-100 rounded-lg overflow-hidden mb-3">
             <div class="px-3 py-2 bg-gray-50 flex items-center gap-2 border-b border-gray-100">
                <svg class="w-3 h-3 text-gray-400 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Utilitas</p>
             </div>
             <div class="bg-white pl-4 py-1 space-y-0.5">
                <div class="flex items-center px-3 py-1.5 text-[13px] font-medium text-gray-600">
                    <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                    <span>Aset Saya</span>
                </div>
                <div x-show="selectedPermissions.includes('user.view')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <span>Manajemen User</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'user.view')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div x-show="selectedPermissions.includes('user.create')" class="group flex items-center justify-between px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors cursor-pointer relative pr-8">
                    <div class="flex items-center">
                        <svg class="shrink-0 h-4 w-4 mr-2.5 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        <span>Tambah User</span>
                    </div>
                     <button type="button" @click="selectedPermissions = selectedPermissions.filter(p => p !== 'user.create')" class="absolute right-2 opacity-0 group-hover:opacity-100 p-1 hover:bg-red-100 rounded-full text-red-500 transition-all" title="Hapus Akses">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    </button>
                </div>
             </div>
        </div>

    </div>
    
    {{-- Footer Info --}}
    <div class="bg-indigo-50 px-4 py-3 border-t border-indigo-100">
        <div class="flex items-start gap-2">
            <svg class="w-4 h-4 text-indigo-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p class="text-[10px] text-indigo-700 leading-tight">
                Preview ini menunjukkan menu apa saja yang akan muncul di sidebar user berdasarkan hak akses yang Anda centang di sebelah kanan.
            </p>
        </div>
    </div>
</div>

{{-- Define permissions helper variable in alpine context if needed, but we rely on selectedPermissions --}}
<script>
    // Just a dummy script block to satisfy syntax highlighters
</script>
