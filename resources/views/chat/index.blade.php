@extends('layouts.main')

@section('container')
<div class="h-[calc(100vh-100px)] flex flex-col md:flex-row bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" 
     x-data="chatHandler()">
    
    {{-- LEFT SIDEBAR: USER LIST --}}
    <div class="w-full md:w-1/3 border-r border-gray-100 flex flex-col bg-gray-50/30">
        {{-- Header --}}
        <div class="p-4 border-b border-gray-100 bg-white">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Pesan & Diskusi</h2>
            <div class="relative">
                <input type="text" x-model="searchQuery" placeholder="Cari teman..." class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        {{-- User List --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <template x-for="user in filteredUsers" :key="user.id">
                <button @click="selectUser(user)" 
                        class="w-full flex items-center p-4 hover:bg-white hover:shadow-sm transition border-b border-transparent hover:border-gray-50"
                        :class="{'bg-white shadow-sm border-l-4 border-l-indigo-500': activeUser && activeUser.id === user.id}">
                    
                    {{-- Avatar --}}
                    <div class="relative">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                            <span x-text="getInitials(user.name)"></span>
                        </div>
                        {{-- Online Indicator (Simulation) --}}
                        <div class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-green-500 border-2 border-white"></div>
                    </div>

                    <div class="ml-3 text-left">
                        <p class="text-sm font-bold text-gray-900" x-text="user.name"></p>
                        <p class="text-xs text-gray-500 truncate w-40" x-text="user.role_label">Role</p>
                    </div>
                </button>
            </template>
        </div>
    </div>

    {{-- RIGHT MAIN: CHAT WINDOW --}}
    <div class="flex-1 flex flex-col bg-white relative">
        
        {{-- Empty State --}}
        <div x-show="!activeUser" class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 bg-slate-50 z-0">
            <div class="h-20 w-20 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Mulai Percakapan</h3>
            <p class="text-gray-500 max-w-sm">Pilih rekan kerja Anda dari daftar di sebelah kiri untuk memulai diskusi real-time.</p>
        </div>

        {{-- Chat Content --}}
        <div x-show="activeUser" class="flex-1 flex flex-col h-full z-10" style="display: none;">
            
            {{-- Chat Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white shadow-sm z-20">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                        <span x-text="activeUser ? getInitials(activeUser.name) : ''"></span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-base font-bold text-gray-900" x-text="activeUser ? activeUser.name : ''"></h3>
                        <p class="text-xs text-green-600 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Online
                        </p>
                    </div>
                </div>
                {{-- Actions --}}
                <div class="flex items-center gap-2">
                     <button class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                    </button>
                </div>
            </div>

            {{-- Messages Area --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50 relative" id="messageContainer">
                
                {{-- Loading Spinner --}}
                <div x-show="isLoading" class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm z-10">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <template x-for="msg in messages" :key="msg.id">
                    <div class="flex w-full mb-4" :class="msg.sender_id == {{ auth()->id() }} ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[75%] flex flex-col gap-1" :class="msg.sender_id == {{ auth()->id() }} ? 'items-end' : 'items-start'">
                            
                            {{-- Asset Card Attachment --}}
                            <template x-if="msg.asset">
                                <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-200 w-64 md:w-72 transition hover:shadow-md relative overflow-hidden group">
                                    {{-- Image Header --}}
                                    <div class="h-40 w-full rounded-xl bg-gray-100 relative overflow-hidden mb-3">
                                        <img :src="msg.asset.image ? '{{ asset('storage') }}/' + msg.asset.image : 'https://placehold.co/400x300'" 
                                             class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <div class="absolute bottom-2 left-3 text-white">
                                            <p class="text-[10px] font-bold uppercase tracking-wider opacity-90">Bagikan Aset</p>
                                        </div>
                                    </div>
                                    
                                    {{-- Content --}}
                                    <div class="mb-3">
                                        <h4 class="font-bold text-gray-900 leading-tight mb-0.5" x-text="msg.asset.name"></h4>
                                        <p class="text-xs text-gray-500 font-mono" x-text="msg.asset.serial_number"></p>
                                    </div>

                                    {{-- Action --}}
                                    <button type="button" @click="viewAssetDetail(msg.asset)" 
                                        class="w-full py-2 rounded-lg bg-indigo-50 text-indigo-600 font-bold text-xs flex items-center justify-center gap-2 hover:bg-indigo-100 transition">
                                        Lihat Detail
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                    </button>

                                    {{-- Timestamp outside bubble if only asset --}}
                                    <template x-if="!msg.body || msg.body === 'Membagikan Aset'">
                                        <div class="text-[10px] text-gray-400 text-right mt-1.5" x-text="formatTime(msg.created_at)"></div>
                                    </template>
                                </div>
                            </template>

                            {{-- Text Bubble --}}
                            {{-- Show only if body is NOT empty AND body is NOT just the default 'Membagikan Aset' --}}
                            <template x-if="msg.body && msg.body !== 'Membagikan Aset'">
                                <div class="rounded-2xl px-4 py-2 shadow-sm text-sm max-w-fit break-words"
                                     :class="msg.sender_id == {{ auth()->id() }} ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none'">
                                    <p x-text="msg.body" class="leading-relaxed whitespace-pre-line"></p>
                                    <div class="text-[10px] mt-1 text-right" 
                                         :class="msg.sender_id == {{ auth()->id() }} ? 'text-indigo-200' : 'text-gray-400'">
                                        <span x-text="formatTime(msg.created_at)"></span>
                                        <span x-show="msg.sender_id == {{ auth()->id() }}">
                                            <template x-if="msg.is_read">
                                                <span class="ml-1 text-white font-bold">✓✓</span>
                                            </template>
                                            <template x-if="!msg.is_read">
                                                <span class="ml-1">✓</span>
                                            </template>
                                        </span>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                </template>
                
                <div x-show="messages.length === 0 && !isLoading" class="text-center text-gray-400 text-sm mt-10">
                    Belum ada pesan. Sapa sekarang! 👋
                </div>
            </div>

            {{-- Input Area --}}
            <div class="p-4 bg-white border-t border-gray-100">
                <form @submit.prevent="sendMessage" class="flex items-end gap-3 z-10 relative">
                    {{-- Plus Menu Dropdown --}}
                    <div class="relative">
                        <button type="button" @click="isMenuOpen = !isMenuOpen" @click.outside="isMenuOpen = false" 
                                class="p-3 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-xl transition bg-gray-50 border border-transparent focus:border-indigo-300" 
                                :class="{'bg-indigo-50 text-indigo-600': isMenuOpen}">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                        
                        {{-- Menu Items --}}
                        <div x-show="isMenuOpen" style="display: none;" 
                             class="absolute bottom-full left-0 mb-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50 transform origin-bottom-left transition-all duration-200"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-2">
                            
                             <div class="p-2 space-y-1">
                                <button type="button" @click="showAssetModal = true; isMenuOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-indigo-50 rounded-lg text-left transition group">
                                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg group-hover:bg-indigo-200 transition">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-700">Bagikan Aset</p>
                                        <p class="text-[10px] text-gray-500">Kirim data barang</p>
                                    </div>
                                </button>
                                {{-- Placeholder for Future Features --}}
                                {{-- Bagikan Peminjaman --}}
                                <button type="button" @click="showLoanModal = true; isMenuOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-indigo-50 rounded-lg text-left transition group">
                                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg group-hover:bg-blue-200 transition">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-700">Peminjaman</p>
                                        <p class="text-[10px] text-gray-500">Data history pinjam</p>
                                    </div>
                                </button>
                                
                                {{-- Bagikan Laporan --}}
                                <button type="button" @click="showReportModal = true; isMenuOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-indigo-50 rounded-lg text-left transition group">
                                    <div class="p-2 bg-red-100 text-red-600 rounded-lg group-hover:bg-red-200 transition">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-700">Laporan</p>
                                        <p class="text-[10px] text-gray-500">Tiket perbaikan</p>
                                    </div>
                                </button>
                             </div>
                        </div>
                    </div>

                    <div class="flex-1 bg-gray-50 rounded-xl border border-gray-200 focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-400 transition">
                        <input type="text" x-model="newMessage" 
                               class="w-full bg-transparent border-none focus:ring-0 text-sm px-4 py-3 text-gray-900 placeholder-gray-400" 
                               placeholder="Ketik pesan Anda...">
                    </div>
                    <button type="submit" 
                            :disabled="(!newMessage.trim() && !selectedAsset) || isSending"
                            class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl p-3 shadow-md transition-all duration-200 flex-shrink-0">
                        <svg x-show="!isSending" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <svg x-show="isSending" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- LOAN PICKER MODAL (Wide & Grid) --}}
    <div x-show="showLoanModal" style="display: none;" 
         class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl flex flex-col max-h-[85vh] overflow-hidden animate-fade-in-up" @click.outside="showLoanModal = false">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-blue-50">
                <div>
                    <h3 class="text-xl font-bold text-blue-900">Pilih Data Peminjaman</h3>
                    <p class="text-sm text-blue-600">Klik untuk membagikan ke chat</p>
                </div>
                <button @click="showLoanModal = false" class="text-gray-400 hover:text-red-500 transition"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-4 border-b border-gray-100 bg-white">
                <input type="text" x-model="loanSearch" placeholder="Cari nama peminjam, aset, atau serial number..." class="w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-base transition">
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="loan in filteredLoans" :key="loan.id">
                        <button @click="shareLoan(loan)" class="flex flex-col text-left p-4 rounded-xl border border-gray-200 hover:border-blue-400 hover:shadow-md hover:bg-blue-50 transition group h-full">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shrink-0">
                                    <span x-text="getInitials(loan.user ? loan.user.name : '?')"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate" x-text="loan.user ? loan.user.name : '-'"></p>
                                    <p class="text-xs text-gray-500" x-text="loan.formatted_date"></p>
                                </div>
                            </div>
                            <div class="mt-auto bg-white p-2 rounded-lg border border-gray-100 group-hover:border-blue-200">
                                <p class="text-xs text-gray-500 mb-1">Aset dipinjam:</p>
                                <p class="text-sm font-bold text-gray-800 truncate" x-text="loan.asset ? loan.asset.name : 'Unknown'"></p>
                                <p class="text-xs text-mono text-gray-400 truncate" x-text="loan.asset ? loan.asset.serial_number : '-'"></p>
                            </div>
                            <div class="mt-2 text-right">
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider"
                                        :class="{
                                            'bg-yellow-100 text-yellow-700': loan.borrowing_status === 'pending',
                                            'bg-green-100 text-green-700': loan.borrowing_status === 'active',
                                            'bg-gray-100 text-gray-700': loan.borrowing_status === 'returned',
                                            'bg-red-100 text-red-700': loan.borrowing_status === 'rejected'
                                        }" 
                                        x-text="loan.status_label"></span>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- REPORT PICKER MODAL (Wide & Grid) --}}
    <div x-show="showReportModal" style="display: none;" 
            class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl flex flex-col max-h-[85vh] overflow-hidden animate-fade-in-up" @click.outside="showReportModal = false">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-red-50">
                <div>
                    <h3 class="text-xl font-bold text-red-900">Pilih Tiket Laporan</h3>
                        <p class="text-sm text-red-600">Klik untuk membagikan ke chat</p>
                </div>
                <button @click="showReportModal = false" class="text-gray-400 hover:text-red-500 transition"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-4 border-b border-gray-100 bg-white">
                <input type="text" x-model="reportSearch" placeholder="Cari masalah, aset, atau vendor..." class="w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-red-500 text-base transition">
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="report in filteredReports" :key="report.id">
                        <button @click="shareReport(report)" class="flex flex-col text-left p-4 rounded-xl border border-gray-200 hover:border-red-400 hover:shadow-md hover:bg-red-50 transition group h-full">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate" x-text="report.formatted_date"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="report.vendor_name || 'Internal'"></p>
                                </div>
                            </div>
                            <div class="mt-auto bg-white p-2 rounded-lg border border-gray-100 group-hover:border-red-200 mb-2">
                                    <p class="text-xs text-gray-500 mb-1">Masalah Aset:</p>
                                    <p class="text-sm font-bold text-gray-800 truncate" x-text="report.asset ? report.asset.name : 'Unknown'"></p>
                                    <p class="text-xs italic text-gray-600 line-clamp-2" x-text="report.problem_description"></p>
                            </div>
                                <div class="mt-auto text-right">
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider"
                                        :class="report.status === 'on_process' ? 'bg-yellow-100 text-yellow-700' : (report.status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600')"
                                        x-text="report.status_label"></span>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

{{-- ASSET PICKER MODAL --}}
<div x-show="showAssetModal" style="display: none;" 
     class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden animate-fade-in-up" @click.outside="showAssetModal = false">
        {{-- Header --}}
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Pilih Aset</h3>
            <button @click="showAssetModal = false" class="text-gray-400 hover:text-red-500 transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        {{-- Search --}}
        <div class="p-4 border-b border-gray-100 bg-white">
            <div class="relative">
                <input type="text" x-model="assetSearch" placeholder="Cari nama atau serial number..." class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        {{-- Asset List --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-2">
            <template x-for="asset in filteredAssets" :key="asset.id">
                <button @click="shareAsset(asset)" class="w-full flex items-center gap-3 p-3 hover:bg-indigo-50 rounded-xl transition border border-transparent hover:border-indigo-100 group text-left">
                    <div class="h-12 w-12 rounded-lg bg-gray-200 overflow-hidden shrink-0">
                        <img :src="asset.image_url" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800 truncate" x-text="asset.name"></p>
                        <p class="text-xs text-gray-500 truncate" x-text="asset.serial_number"></p>
                    </div>
                    <div class="text-indigo-600 opacity-0 group-hover:opacity-100 transition">
                         <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </div>
                </button>
            </template>
            <div x-show="filteredAssets.length === 0" class="text-center py-8 text-gray-400 text-sm">
                Tidak ada aset ditemukan.
            </div>
        </div>
    </div>
</div>

{{-- ASSET DETAIL MODAL (In-Chat) --}}
<div x-show="showAssetDetail" style="display: none;" 
     class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
    <template x-if="viewingAsset">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden animate-fade-in-up" @click.outside="showAssetDetail = false">
            {{-- Modal Header --}}
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Detail Aset</h3>
                <button @click="showAssetDetail = false" class="text-gray-400 hover:text-red-500 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            {{-- Modal Content --}}
            <div class="overflow-y-auto p-0">
                {{-- Image Banner --}}
                <div class="h-48 w-full bg-gray-200 relative">
                     <img :src="viewingAsset.image ? '{{ asset('storage') }}/' + viewingAsset.image : 'https://placehold.co/600x300'" class="w-full h-full object-cover">
                     <div class="absolute bottom-4 left-4">
                         <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm"
                              :class="viewingAsset.status === 'available' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                              x-text="viewingAsset.status.toUpperCase()"></span>
                     </div>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900" x-text="viewingAsset.name"></h2>
                            <p class="text-gray-500 font-mono text-sm" x-text="viewingAsset.serial_number"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400">ID System</p>
                            <p class="font-bold text-gray-700" x-text="'#' + viewingAsset.id"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-400 mb-1">Kategori / Merk</p>
                            <p class="font-semibold text-gray-800" x-text="viewingAsset.brand || '-'"></p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-400 mb-1">Lokasi</p>
                            <p class="font-semibold text-gray-800" x-text="viewingAsset.location || '-'"></p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-400 mb-1">Tahun Pengadaan</p>
                            <p class="font-semibold text-gray-800" x-text="viewingAsset.purchase_date || '-'"></p>
                        </div>
                         <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-400 mb-1">Harga Perolehan</p>
                            <p class="font-semibold text-gray-800" x-text="viewingAsset.purchase_price ? 'Rp' + Number(viewingAsset.purchase_price).toLocaleString('id-ID') : '-'"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
                <a :href="'{{ url('/assets') }}/' + viewingAsset.id" target="_blank" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                    Buka Halaman Penuh ↗
                </a>
                <button @click="showAssetDetail = false" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm transition shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </template>
</div>

{{-- LOAN DETAIL MODAL --}}
<div x-show="showLoanDetail" style="display: none;" 
     class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
    <template x-if="viewingLoan">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up" @click.outside="showLoanDetail = false">
             <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-blue-50">
                <h3 class="text-lg font-bold text-blue-900">Detail Peminjaman</h3>
                <button @click="showLoanDetail = false" class="text-gray-400 hover:text-red-500 transition"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-6 space-y-4">
                 <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                    <img :src="viewingLoan.asset_image" class="h-16 w-16 rounded-lg object-cover bg-white shadow-sm">
                    <div>
                        <p class="text-lg font-bold text-gray-900" x-text="viewingLoan.asset.name"></p>
                        <p class="text-sm text-gray-500" x-text="viewingLoan.asset.serial_number"></p>
                    </div>
                 </div>
                 <div class="space-y-3">
                     <div class="flex justify-between border-b border-gray-50 pb-2">
                         <span class="text-gray-500 text-sm">Peminjam</span>
                         <span class="font-bold text-gray-800 text-sm" x-text="viewingLoan.user ? viewingLoan.user.name : '-'"></span>
                     </div>
                     <div class="flex justify-between border-b border-gray-50 pb-2">
                         <span class="text-gray-500 text-sm">Tanggal Pinjam</span>
                         <span class="font-bold text-gray-800 text-sm" x-text="viewingLoan.formatted_date"></span>
                     </div>
                     <div class="flex justify-between items-center">
                         <span class="text-gray-500 text-sm">Status</span>
                         <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700" x-text="viewingLoan.status_label"></span>
                     </div>
                 </div>
                 <div class="pt-4">
                     <a :href="'{{ url('/borrowing') }}/' + viewingLoan.id" target="_blank" class="block w-full py-2 bg-blue-600 text-white text-center rounded-xl font-bold hover:bg-blue-700 transition">Buka Data Lengkap</a>
                 </div>
            </div>
        </div>
    </template>
</div>

{{-- REPORT DETAIL MODAL --}}
<div x-show="showReportDetail" style="display: none;" 
     class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
    <template x-if="viewingReport">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up" @click.outside="showReportDetail = false">
             <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-red-50">
                <h3 class="text-lg font-bold text-red-900">Detail Laporan</h3>
                <button @click="showReportDetail = false" class="text-gray-400 hover:text-red-500 transition"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-6 space-y-4">
                 <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                    <img :src="viewingReport.asset_image" class="h-16 w-16 rounded-lg object-cover bg-white shadow-sm">
                    <div>
                        <p class="text-lg font-bold text-gray-900" x-text="viewingReport.asset.name"></p>
                        <p class="text-sm text-gray-500" x-text="viewingReport.asset.serial_number"></p>
                    </div>
                 </div>
                 <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                     <p class="text-xs font-bold text-red-800 uppercase mb-1">Masalah</p>
                     <p class="text-sm text-gray-800" x-text="viewingReport.problem_description"></p>
                 </div>
                 <div class="flex justify-between items-center mt-4">
                      <span class="text-gray-500 text-sm">Status</span>
                      <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700" x-text="viewingReport.status_label"></span>
                 </div>
            </div>
        </div>
    </template>
</div>

</div> {{-- CLOSE MAIN CONTAINER --}}

@php
    $formattedUsers = $users->map(function($u) {
        $roles = ['admin' => 'Administrator', 'super_admin' => 'Super Admin', 'service_center' => 'Teknisi Service', 'user' => 'Karyawan'];
        $u['role_label'] = $roles[$u->role] ?? ucfirst($u->role);
        return $u;
    });

    $formattedAssets = \App\Models\Asset::select('id','name','serial_number','image','status','category','location','purchase_date','purchase_price')
        ->get()
        ->map(function($a){
            $a->image_url = $a->image ? asset('storage/'.$a->image) : 'https://placehold.co/100';
            $a->brand = $a->category; 
            return $a;
        });

    // Inject Borrowings (Limit 50 latest for MVP)
    // FIX: Model is AssetRequest, not Borrowing
    $formattedBorrowings = \App\Models\AssetRequest::with(['user:id,name', 'asset:id,name,serial_number,image'])
        ->latest()
        ->limit(50)
        ->get()
        ->map(function($b){
            $b->formatted_date = $b->created_at->format('d M Y');
            $labels = [
                'pending' => 'Menunggu',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'active' => 'Dipinjam',
                'returned' => 'Dikembalikan'
            ];
            // Determine friendly status
            $status = $b->status;
            if ($b->status === 'approved' && !$b->returned_at) $status = 'active';
            if ($b->returned_at) $status = 'returned';
            
            $b->status_label = $labels[$status] ?? ucfirst($status);
            $b->asset_image = $b->asset && $b->asset->image ? asset('storage/'.$b->asset->image) : 'https://placehold.co/100';
            return $b;
        });

    // Inject Maintenances (Limit 50 latest for MVP)
    $formattedMaintenances = \App\Models\Maintenance::with(['asset:id,name,serial_number,image'])
        ->latest()
        ->limit(50)
        ->get()
        ->map(function($m){
            $m->formatted_date = \Carbon\Carbon::parse($m->start_date)->format('d M Y');
            $m->status_label = $m->status == 'on_process' ? 'Proses' : ($m->status == 'completed' ? 'Selesai' : 'Batal');
            $m->asset_image = $m->asset && $m->asset->image ? asset('storage/'.$m->asset->image) : 'https://placehold.co/100';
            return $m;
        });
@endphp
<script>
    function chatHandler() {
        return {
            users: @json($formattedUsers),
            // Injecting all assets for MVP. In production, fetch via API or pagination.
            assets: @json($formattedAssets),
            borrowings: @json($formattedBorrowings),
            maintenances: @json($formattedMaintenances),
            
            activeUser: null,
            searchQuery: '',
            
            // Search filters
            assetSearch: '',
            loanSearch: '',
            reportSearch: '',
            
            // UI States
            isMenuOpen: false,

            // Modal States
            showAssetModal: false,
            showLoanModal: false,
            showReportModal: false,

            // Viewer States
            showAssetDetail: false,
            showLoanDetail: false,
            showReportDetail: false,
            
            viewingAsset: null,
            viewingLoan: null,
            viewingReport: null,
            
            selectedAsset: null, // Legacy, can be removed if not used for "attached" state visually before sending

            messages: [],
            newMessage: '',
            isLoading: false,
            isSending: false,
            pollInterval: null,
            
            // Cari User di Sidebar Kiri
            get filteredUsers() {
                if(this.searchQuery === '') return this.users;
                return this.users.filter(user => {
                     return user.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                });
            },

            // Filter Aset (Pas mau Share)
            // Ini jalan otomatis pas kamu ngetik di search box popup aset.
            get filteredAssets() {
                if(this.assetSearch === '') return this.assets;
                return this.assets.filter(asset => {
                    return asset.name.toLowerCase().includes(this.assetSearch.toLowerCase()) || 
                           asset.serial_number.toLowerCase().includes(this.assetSearch.toLowerCase());
                });
            },

            get filteredLoans() {
                if(this.loanSearch === '') return this.borrowings;
                return this.borrowings.filter(loan => {
                    return (loan.asset?.name || '').toLowerCase().includes(this.loanSearch.toLowerCase()) || 
                           (loan.user?.name || '').toLowerCase().includes(this.loanSearch.toLowerCase());
                });
            },

            get filteredReports() {
                if(this.reportSearch === '') return this.maintenances;
                return this.maintenances.filter(report => {
                    return (report.asset?.name || '').toLowerCase().includes(this.reportSearch.toLowerCase()) || 
                           (report.problem_description || '').toLowerCase().includes(this.reportSearch.toLowerCase());
                });
            },

            getInitials(name) {
                return name.match(/(\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("").toUpperCase();
            },

            formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            },

            // Buka Popup Detail Aset
            // Pas kamu klik tombol "Lihat Detail" di chat.
            viewAssetDetail(asset) {
                // Coba cari data lengkap asetnya dari daftar yang kita punya
                const fullAsset = this.assets.find(a => a.id === asset.id);
                this.viewingAsset = fullAsset || asset; 
                this.showAssetDetail = true;
            },

            // Logika "Bagikan Aset"
            // Pas kamu pilih aset terus klik OK.
            async shareAsset(asset) {
                if(!confirm(`Bagikan informasi aset "${asset.name}"?`)) return;
                this.showAssetModal = false;
                // Panggil fungsi kirim pesan khusus attachment tipe 'asset'
                await this.sendMessageWithAttachment('asset', asset.id);
            },

            async shareLoan(loan) {
                if(!confirm(`Bagikan data peminjaman ini?`)) return;
                this.showLoanModal = false;
                await this.sendMessageWithAttachment('loan', loan.id);
            },

            async shareReport(report) {
                if(!confirm(`Bagikan laporan perbaikan ini?`)) return;
                this.showReportModal = false;
                await this.sendMessageWithAttachment('report', report.id);
            },

            viewLoanDetail(loan) {
                const fullLoan = this.borrowings.find(b => b.id === loan.id);
                this.viewingLoan = fullLoan || loan;
                this.viewingLoan.asset = fullLoan ? fullLoan.asset : (loan.asset || {});
                this.showLoanDetail = true;
            },

            viewReportDetail(report) {
                 const fullReport = this.maintenances.find(m => m.id === report.id);
                 this.viewingReport = fullReport || report;
                 this.viewingReport.asset = fullReport ? fullReport.asset : (report.asset || {});
                 this.showReportDetail = true;
            },

            // Pas Klik Teman Chat
            // Fungsi ini nyiapin segala macem sebelum chat dimulai.
            async selectUser(user) {
                this.activeUser = user;
                this.messages = [];
                this.isLoading = true;
                this.newMessage = ''; 
                
                // Stop polling chat sebelumnya (kalau ada)
                if(this.pollInterval) clearInterval(this.pollInterval);

                // Ambil pesan lama
                await this.fetchMessages();
                this.isLoading = false;

                // Mulai cek pesan baru tiap 3 detik (Polling)
                this.pollInterval = setInterval(() => {
                    this.fetchMessages(true); 
                }, 3000); 
            },

            // Ambil Pesan dari Server
            // silent = true artinya gak perlu scroll ke bawah (biasanya pas polling)
            async fetchMessages(silent = false) {
                if(!this.activeUser) return;
                
                try {
                    const response = await fetch(`{{ url('/chat/conversation') }}/${this.activeUser.id}`);
                    const data = await response.json();
                    
                    if(data.status === 'found') {
                        this.messages = data.messages;
                        if(!silent) this.scrollToBottom();
                    }
                } catch (error) {
                    console.error('Gagal ambil chat:', error);
                }
            },

            // Tombol Kirim Biasa
            async sendMessage() {
                if((!this.newMessage.trim() && !this.selectedAsset) || !this.activeUser) return;
                await this.sendMessageWithAttachment(null, null);
            },

            // Fungsi Utama Kirim Pesan & Attachment
            // type bisa 'asset', 'loan', 'report', atau null (teks biasa)
            async sendMessageWithAttachment(type, id) {
                this.isSending = true;
                const payload = {
                    receiver_id: this.activeUser.id,
                    body: this.newMessage || (type === 'asset' ? 'Membagikan Aset' : (type === 'loan' ? 'Membagikan Data Peminjaman' : 'Membagikan Laporan')),
                    _token: '{{ csrf_token() }}'
                };

                // FIX: Backend butuh key 'asset_id', jadi kita set manual di sini.
                if(type === 'asset') {
                    payload.asset_id = id;
                }

                try {
                    const response = await fetch(`{{ route('chat.send') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload)
                    });
                    
                    const data = await response.json();
                    
                    if(data.success) {
                        this.messages.push(data.message); // Masukin pesan baru ke layar
                        this.newMessage = '';
                        this.scrollToBottom();
                    }
                } catch (error) {
                    console.error('Gagal kirim:', error);
                    alert('Gagal mengirim pesan');
                } finally {
                    this.isSending = false;
                }
            },



            scrollToBottom() {
                this.$nextTick(() => {
                    const container = document.getElementById('messageContainer');
                    if(container) container.scrollTop = container.scrollHeight;
                });
            },

            async handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    alert('Ukuran file terlalu besar (Maks 5MB)');
                    return;
                }

                const formData = new FormData();
                formData.append('attachment', file);
                formData.append('receiver_id', this.activeUser.id);
                formData.append('body', 'Mengirim lampiran...');
                formData.append('_token', '{{ csrf_token() }}');

                this.isSending = true;

                try {
                    const response = await fetch(`{{ route('chat.send') }}`, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if(data.success) {
                        this.messages.push(data.message);
                        this.scrollToBottom();
                    } else {
                        alert('Gagal mengirim file: ' + (data.message || 'Error'));
                    }
                } catch (error) {
                    console.error('File upload failed:', error);
                    alert('Gagal mengirim file');
                } finally {
                    this.isSending = false;
                    event.target.value = '';
                }
            }
        }
    }
</script>
@endsection
