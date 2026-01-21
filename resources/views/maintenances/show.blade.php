@extends('layouts.main')

@section('container')
<div class="container mx-auto px-4 py-8">
    
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Tiket Perbaikan</h1>
            <p class="text-gray-600 mt-1">ID Tiket: #{{ $maintenance->id }} • {{ $maintenance->created_at->format('d M Y') }}</p>
        </div>
        <a href="{{ url()->previous() }}" class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- LEFT COLUMN: ASSET INFO --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Asset Card --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden group">
                <div class="relative h-48 bg-gray-200">
                    <img src="{{ $maintenance->asset->image ? asset('storage/' . $maintenance->asset->image) : 'https://placehold.co/400x300?text=No+Image' }}" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                         alt="Asset Image">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <div class="text-xs font-bold uppercase tracking-wider bg-indigo-600 px-2 py-0.5 rounded w-fit mb-1">{{ $maintenance->asset->category ?? 'General' }}</div>
                        <h3 class="text-lg font-bold leading-tight">{{ $maintenance->asset->name }}</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Serial Number</p>
                            <p class="font-mono font-bold text-gray-800 text-sm mt-0.5">{{ $maintenance->asset->serial_number }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Status Aset</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full {{ $maintenance->asset->status == 'maintenance' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $maintenance->asset->status }}
                            </span>
                        </div>
                    </div>
                    <div class="pt-4">
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-2">Lokasi Aset</p>
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $maintenance->asset->lorong ?? '-' }} / Rak {{ $maintenance->asset->rak ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vendor Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Informasi Vendor</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nama Vendor / Service Center</p>
                        <p class="font-bold text-gray-900 text-lg">{{ $maintenance->vendor_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Tanggal Mulai Service</p>
                        <div class="flex items-center gap-2 text-gray-800 font-medium">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ \Carbon\Carbon::parse($maintenance->start_date)->translatedFormat('d F Y') }}
                        </div>
                    </div>
                    @if($maintenance->completion_date)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Tanggal Selesai</p>
                        <div class="flex items-center gap-2 text-green-700 font-medium">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ \Carbon\Carbon::parse($maintenance->completion_date)->translatedFormat('d F Y') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: MAINTENANCE LOG & ACTIONS --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Problem Description --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-start gap-4">
                    <div class="bg-red-50 p-3 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="w-full">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Deskripsi Masalah / Kerusakan</h3>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 text-gray-700 leading-relaxed">
                            {{ $maintenance->problem_description }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Taken / Resolution (Form Update) --}}
            <div class="bg-white rounded-xl shadow-sm border border-indigo-100 overflow-hidden relative">
                @if($maintenance->status == 'completed')
                    <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg uppercase tracking-wider">Tiket Selesai</div>
                    <div class="p-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Tindakan Perbaikan & Solusi
                        </h3>
                        <div class="mb-6">
                            <p class="text-gray-600 mb-1 font-bold">Catatan Penyelesaian:</p>
                            <div class="bg-green-50 p-4 rounded-lg text-green-800 border border-green-100">
                                {{ $maintenance->resolution_notes ?? 'Tidak ada catatan detail.' }}
                            </div>
                        </div>
                        <div class="flex items-center gap-6 pt-4 border-t border-gray-100">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Total Biaya</p>
                                <p class="text-xl font-bold text-gray-900">Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</p>
                            </div>
                            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                            <div class="ml-auto">
                                <button onclick="document.getElementById('editForm').classList.toggle('hidden')" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 underline">
                                    Edit Data
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Form Update Status --}}
                    <div class="bg-indigo-50 px-8 py-4 border-b border-indigo-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-indigo-900">Update Status Perbaikan</h3>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full uppercase">Sedang Diproses</span>
                    </div>
                    <form action="{{ route('maintenances.update', $maintenance->id) }}" method="POST" class="p-8">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tindakan yang Dilakukan (Solusi) <span class="text-red-500">*</span></label>
                            <textarea name="resolution_notes" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Jelaskan langkah perbaikan yang telah dilakukan...">{{ $maintenance->resolution_notes }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Isi catatan ini untuk merekam progres atau solusi akhir perbaikan.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Update Biaya (Rp)</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                    </div>
                                    <input type="number" name="cost" value="{{ $maintenance->cost }}" class="pl-10 block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Status Tiket</label>
                                <select name="status" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="on_process" {{ $maintenance->status == 'on_process' ? 'selected' : '' }}>Masih Dalam Proses</option>
                                    <option value="completed" {{ $maintenance->status == 'completed' ? 'selected' : '' }}>✅ Selesai (Aset Kembali Available)</option>
                                    <option value="cancelled" {{ $maintenance->status == 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan</option>
                                </select>
                            </div>
                        </div>

                        {{-- Hidden Completion Date --}}
                        <input type="hidden" name="completion_date" value="{{ date('Y-m-d') }}">

                        <div class="flex justify-end gap-3 pt-4 border-t border-indigo-50">
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition">
                                Simpan Update
                            </button>
                        </div>
                    </form>
                @endif

                {{-- Edit Form (Hidden by default for completed items) --}}
                @if($maintenance->status == 'completed')
                <div id="editForm" class="hidden border-t-4 border-indigo-500 bg-gray-50 p-8">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Edit Data Selesai</h4>
                    <form action="{{ route('maintenances.update', $maintenance->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Revisi Catatan Solusi</label>
                                <textarea name="resolution_notes" rows="2" class="w-full rounded border-gray-300 text-sm">{{ $maintenance->resolution_notes }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Revisi Biaya</label>
                                    <input type="number" name="cost" value="{{ $maintenance->cost }}" class="w-full rounded border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Status</label>
                                    <select name="status" class="w-full rounded border-gray-300 text-sm">
                                        <option value="completed" selected>Tetap Selesai</option>
                                        <option value="on_process">Kembalikan ke Proses</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="bg-indigo-600 text-white text-xs font-bold px-4 py-2 rounded hover:bg-indigo-700">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
