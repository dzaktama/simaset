@extends('layouts.main')

@section('container')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- Main Card --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            
            {{-- Header dengan Nama Aset --}}
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 sm:px-8 py-8">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $asset->name }}</h1>
                        <p class="text-indigo-100 text-lg">Serial: <span class="font-mono font-bold">{{ $asset->serial_number }}</span></p>
                    </div>
                    <span class="px-4 py-2 bg-white/20 backdrop-blur text-white rounded-full text-sm font-bold">
                        {{ ucfirst($asset->status) }}
                    </span>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8 p-6 sm:p-8">
                
                {{-- Kolom Kiri: Info Aset --}}
                <div class="md:col-span-2 space-y-8">
                    
                    {{-- Basic Info Section --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 100-2 1 1 0 000 2zM8 7a1 1 0 100-2 1 1 0 000 2zm4-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                            </svg>
                            Informasi Dasar
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Stok</p>
                                <p class="text-2xl font-bold text-indigo-600">{{ $asset->quantity }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Status</p>
                                <p class="text-lg font-bold text-gray-800">{{ ucfirst($asset->status) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if ($asset->description)
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Deskripsi</h3>
                        <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-200">
                            {{ $asset->description }}
                        </p>
                    </div>
                    @endif

                    {{-- Condition Notes --}}
                    @if ($asset->condition_notes)
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Catatan Kondisi</h3>
                        <p class="text-gray-700 leading-relaxed bg-amber-50 p-4 rounded-lg border border-amber-200">
                            {{ $asset->condition_notes }}
                        </p>
                    </div>
                    @endif

                    {{-- Dates & Owner Info --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z" />
                            </svg>
                            Detail Penggunaan
                        </h3>
                        <div class="space-y-3">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Tanggal Pembelian</p>
                                <p class="text-gray-800 font-medium">
                                    {{ $asset->purchase_date ? $asset->purchase_date->format('d M Y') : '-' }}
                                </p>
                            </div>
                            @if ($asset->status === 'deployed' && $asset->holder)
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Dipinjam Oleh</p>
                                <p class="text-gray-800 font-medium">{{ $asset->holder->name }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $asset->holder->position }}</p>
                            </div>
                            @endif
                            @if ($asset->assigned_date)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Tanggal Assigned</p>
                                <p class="text-gray-800 font-medium">{{ $asset->assigned_date->format('d M Y H:i') }}</p>
                            </div>
                            @endif
                            @if ($asset->return_date)
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <p class="text-xs font-semibold text-red-600 uppercase mb-1">Target Tanggal Kembali</p>
                                <p class="text-gray-800 font-medium">{{ $asset->return_date->format('d M Y H:i') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        @auth
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('assets.edit', $asset) }}" class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition text-center">
                                Edit Aset
                            </a>
                        @else
                            @if ($asset->status === 'available')
                            <a href="{{ route('assets.index') }}" class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition text-center">
                                Ajukan Peminjaman
                            </a>
                            @elseif ($asset->status === 'deployed')
                            <a href="{{ route('assets.index') }}" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition text-center">
                                Booking Aset
                            </a>
                            @endif
                        @endif
                        @endauth
                    </div>
                </div>

                {{-- Kolom Kanan: QR Code & Images --}}
                <div class="md:col-span-1 space-y-6">
                    
                    {{-- QR Code Section --}}
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-xl border border-gray-200 text-center">
                        <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">QR Code</h4>
                        <div class="bg-white p-4 rounded-lg inline-block border border-gray-200">
                            <img src="{{ $asset->qr_code }}" alt="QR Code - {{ $asset->name }}" class="w-48 h-48">
                        </div>
                        <p class="text-xs text-gray-500 mt-3">Scan untuk akses detail aset</p>
                    </div>

                    {{-- Images Section --}}
                    @if ($asset->image || $asset->image2 || $asset->image3)
                    <div class="space-y-3">
                        <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Foto Aset</h4>
                        <div class="space-y-2">
                            @foreach (['image', 'image2', 'image3'] as $imageKey)
                                @if ($asset->$imageKey)
                                <div class="relative overflow-hidden rounded-lg border border-gray-200 h-32">
                                    <img src="{{ asset('storage/' . $asset->$imageKey) }}" 
                                         alt="Asset image" 
                                         class="w-full h-full object-cover hover:scale-105 transition">
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="bg-gray-100 p-6 rounded-lg text-center border border-gray-200">
                        <p class="text-sm text-gray-500">Tidak ada foto</p>
                    </div>
                    @endif

                    {{-- Info Badge --}}
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                        <p class="text-xs text-indigo-600 font-semibold uppercase mb-2">Last Updated</p>
                        <p class="text-sm text-gray-700">{{ $asset->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
