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
                    <div class="flex w-full" :class="msg.sender_id == {{ auth()->id() }} ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[70%] flex flex-col items-end gap-1">
                            {{-- Asset Card Attachment --}}
                            <template x-if="msg.asset">
                                <a :href="'{{ url('/assets') }}/' + msg.asset.id" target="_blank" 
                                   class="block bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-1 hover:shadow-md transition w-64 text-left group">
                                    <div class="h-32 bg-gray-200 relative overflow-hidden">
                                        <img :src="msg.asset.image ? '{{ asset('storage') }}/' + msg.asset.image : 'https://placehold.co/300x200'" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute bottom-2 left-2 bg-black/50 text-white text-[10px] px-2 py-0.5 rounded backdrop-blur-sm" x-text="msg.asset.status"></div>
                                    </div>
                                    <div class="p-3">
                                        <p class="font-bold text-gray-900 text-sm truncate" x-text="msg.asset.name"></p>
                                        <p class="text-xs text-gray-500 font-mono" x-text="msg.asset.serial_number"></p>
                                    </div>
                                </a>
                            </template>

                            <div class="rounded-2xl px-4 py-2 shadow-sm text-sm"
                                 :class="msg.sender_id == {{ auth()->id() }} ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none'">
                                <p x-text="msg.body" class="leading-relaxed"></p>
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
                        </div>
                    </div>
                </template>
                
                <div x-show="messages.length === 0 && !isLoading" class="text-center text-gray-400 text-sm mt-10">
                    Belum ada pesan. Sapa sekarang! 👋
                </div>
            </div>

            {{-- Input Area --}}
            <div class="p-4 bg-white border-t border-gray-100">
                <form @submit.prevent="sendMessage" class="flex items-end gap-3">
                    <div class="flex-1 bg-gray-50 rounded-xl border border-gray-200 focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-400 transition">
                        <input type="text" x-model="newMessage" 
                               class="w-full bg-transparent border-none focus:ring-0 text-sm px-4 py-3 text-gray-900 placeholder-gray-400" 
                               placeholder="Ketik pesan Anda...">
                    </div>
                    <button type="submit" 
                            :disabled="!newMessage.trim() || isSending"
                            class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl p-3 shadow-md transition-all duration-200 flex-shrink-0">
                        <svg x-show="!isSending" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <svg x-show="isSending" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@php
    $formattedUsers = $users->map(function($u) {
        $roles = ['admin' => 'Administrator', 'super_admin' => 'Super Admin', 'service_center' => 'Teknisi Service', 'user' => 'Karyawan'];
        $u['role_label'] = $roles[$u->role] ?? ucfirst($u->role);
        return $u;
    });
@endphp
<script>
    function chatHandler() {
        return {
            users: @json($formattedUsers),
            activeUser: null,
            searchQuery: '',
            messages: [],
            newMessage: '',
            isLoading: false,
            isSending: false,
            pollInterval: null,
            
            get filteredUsers() {
                if(this.searchQuery === '') return this.users;
                return this.users.filter(user => {
                    return user.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                });
            },

            getInitials(name) {
                return name.match(/(\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("").toUpperCase();
            },

            formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            },

            async selectUser(user) {
                this.activeUser = user;
                this.messages = [];
                this.isLoading = true;
                
                // Clear previous poll if any
                if(this.pollInterval) clearInterval(this.pollInterval);

                await this.fetchMessages();
                this.isLoading = false;

                // Start Polling 
                this.pollInterval = setInterval(() => {
                    this.fetchMessages(true); // Silent fetch
                }, 3000); 
            },

            async fetchMessages(silent = false) {
                if(!this.activeUser) return;
                
                try {
                    const response = await fetch(`{{ url('/chat/conversation') }}/${this.activeUser.id}`);
                    const data = await response.json();
                    
                    if(data.status === 'found') {
                        // Logic to only append new messages or replace if structure complex
                        // For simplicity MVP: Replace all (Optimizable later)
                        // Or better: Check length diff.
                        // Let's just replace for now to ensure Sync.
                        this.messages = data.messages;
                        
                        if(!silent) this.scrollToBottom();
                        // If silent and user is at bottom, auto scroll. If up, don't.
                    }
                } catch (error) {
                    console.error('Error fetching chat:', error);
                }
            },

            async sendMessage() {
                if(!this.newMessage.trim() || !this.activeUser) return;
                
                this.isSending = true;
                const payload = {
                    receiver_id: this.activeUser.id,
                    body: this.newMessage,
                    _token: '{{ csrf_token() }}'
                };

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
                        this.messages.push(data.message);
                        this.newMessage = '';
                        this.scrollToBottom();
                    }
                } catch (error) {
                    console.error('Send failed:', error);
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
            }
        }
    }
</script>
@endsection
