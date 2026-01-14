@extends('layouts.main')

@section('container')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto"> {{-- Diperlebar dikit jadi max-w-6xl biar lega --}}
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('assets.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
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
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $asset->name }}</h1>
                        <p class="text-indigo-100 text-lg flex items-center gap-2">
                            Serial: <span class="font-mono font-bold bg-white/10 px-2 py-0.5 rounded">{{ $asset->serial_number }}</span>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-4 py-2 bg-white/20 backdrop-blur text-white rounded-full text-sm font-bold border border-white/30 uppercase tracking-wider">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8 p-6 sm:p-8">
                
                {{-- Kolom Kiri: Info Aset --}}
                <div class="md:col-span-2 space-y-8 order-2 md:order-1">
                    
                    {{-- Basic Info Section --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 100-2 1 1 0 000 2zM8 7a1 1 0 100-2 1 1 0 000 2zm4-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                            </svg>
                            Informasi Dasar
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Kategori</p>
                                <p class="text-base font-bold text-gray-800">{{ $asset->category ?? '-' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Stok</p>
                                <p class="text-2xl font-bold text-indigo-600">{{ $asset->quantity }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Lorong</p>
                                <p class="text-base font-bold text-gray-800">{{ $asset->lorong ?? '-' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Rak</p>
                                <p class="text-base font-bold text-gray-800">{{ $asset->rak ?? '-' }}</p>
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
                        <p class="text-gray-700 leading-relaxed bg-amber-50 p-4 rounded-lg border border-amber-200 text-amber-900">
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
                                    {{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d F Y') : '-' }}
                                </p>
                            </div>
                            
                            {{-- Info Peminjam jika ada --}}
                            @if ($asset->status === 'deployed' && $asset->holder)
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Dipinjam Oleh</p>
                                <p class="text-gray-800 font-medium">{{ $asset->holder->name }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $asset->holder->position ?? 'Karyawan' }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                        @auth
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('assets.edit', $asset) }}" class="flex-1 px-4 py-3 bg-yellow-500 text-white rounded-lg font-bold hover:bg-yellow-600 transition text-center shadow-sm flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit Aset
                            </a>
                            <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus aset ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition text-center shadow-sm flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus Aset
                                </button>
                            </form>
                        @else
                            @if ($asset->status === 'available')
                            <a href="{{ route('borrowing.create', ['asset_id' => $asset->id]) }}" class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition text-center shadow-md">
                                Ajukan Peminjaman
                            </a>
                            @endif
                        @endif
                        @endauth
                    </div>
                </div>

                {{-- Kolom Kanan: Carousel Foto & QR Code --}}
                <div class="md:col-span-1 space-y-6 order-1 md:order-2">
                    
                    {{-- CAROUSEL FOTO (FITUR BARU) --}}
                    @if($asset->image || $asset->image2 || $asset->image3)
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm" x-data="{ activeSlide: 0 }">
                            <h4 class="text-sm font-bold text-gray-700 bg-gray-50 px-4 py-3 border-b border-gray-200 uppercase tracking-wide">
                                Foto Fisik Aset
                            </h4>
                            
                            {{-- Main Image Area --}}
                            <div class="relative h-64 bg-gray-100 flex items-center justify-center group">
                                {{-- Slide 1 --}}
                                @if($asset->image)
                                <div x-show="activeSlide === 0" class="w-full h-full" x-transition.opacity>
                                    <img src="{{ asset('storage/' . $asset->image) }}" class="w-full h-full object-contain p-1" alt="Foto Utama">
                                </div>
                                @endif

                                {{-- Slide 2 --}}
                                @if($asset->image2)
                                <div x-show="activeSlide === 1" class="w-full h-full" x-transition.opacity style="display: none;">
                                    <img src="{{ asset('storage/' . $asset->image2) }}" class="w-full h-full object-contain p-1" alt="Foto Samping">
                                </div>
                                @endif

                                {{-- Slide 3 --}}
                                @if($asset->image3)
                                <div x-show="activeSlide === 2" class="w-full h-full" x-transition.opacity style="display: none;">
                                    <img src="{{ asset('storage/' . $asset->image3) }}" class="w-full h-full object-contain p-1" alt="Foto Belakang">
                                </div>
                                @endif

                                {{-- Navigation Arrows (Muncul jika lebih dari 1 foto) --}}
                                @if(($asset->image && $asset->image2) || ($asset->image && $asset->image3))
                                    <button @click="activeSlide = activeSlide === 0 ? {{ $asset->image3 ? 2 : 1 }} : activeSlide - 1" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-1.5 rounded-full hover:bg-black/70 opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    <button @click="activeSlide = activeSlide === {{ $asset->image3 ? 2 : 1 }} ? 0 : activeSlide + 1" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-1.5 rounded-full hover:bg-black/70 opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                @endif
                            </div>

                            {{-- Thumbnails (Bawah) --}}
                            <div class="flex gap-2 p-3 bg-gray-50 border-t justify-center overflow-x-auto">
                                @if($asset->image)
                                <button @click="activeSlide = 0" :class="{ 'ring-2 ring-indigo-500 opacity-100': activeSlide === 0, 'opacity-60 hover:opacity-100': activeSlide !== 0 }" class="w-14 h-14 rounded-md overflow-hidden border border-gray-300 transition">
                                    <img src="{{ asset('storage/' . $asset->image) }}" class="w-full h-full object-cover">
                                </button>
                                @endif
                                @if($asset->image2)
                                <button @click="activeSlide = 1" :class="{ 'ring-2 ring-indigo-500 opacity-100': activeSlide === 1, 'opacity-60 hover:opacity-100': activeSlide !== 1 }" class="w-14 h-14 rounded-md overflow-hidden border border-gray-300 transition">
                                    <img src="{{ asset('storage/' . $asset->image2) }}" class="w-full h-full object-cover">
                                </button>
                                @endif
                                @if($asset->image3)
                                <button @click="activeSlide = 2" :class="{ 'ring-2 ring-indigo-500 opacity-100': activeSlide === 2, 'opacity-60 hover:opacity-100': activeSlide !== 2 }" class="w-14 h-14 rounded-md overflow-hidden border border-gray-300 transition">
                                    <img src="{{ asset('storage/' . $asset->image3) }}" class="w-full h-full object-cover">
                                </button>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-100 p-8 rounded-xl text-center border border-gray-200 border-dashed h-48 flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-sm text-gray-500 font-medium">Tidak ada foto aset</p>
                        </div>
                    @endif

                    {{-- QR Code Section --}}
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-xl border border-gray-200 text-center shadow-sm">
                        <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">QR Code</h4>
                        <div class="bg-white p-4 rounded-lg inline-block border border-gray-200 shadow-sm">
                            {{-- Ganti Logic QR Code jika perlu, disini asumsi pake library simple-qrcode atau generate manual --}}
                            {{-- Jika $asset->qr_code berisi path file --}}
                            @if(file_exists(public_path('storage/'.$asset->qr_code)))
                                <img src="{{ asset('storage/'.$asset->qr_code) }}" alt="QR Code" class="w-40 h-40">
                            @else
                                {{-- Fallback jika QR dinamis --}}
                                {!! QrCode::size(160)->generate($asset->serial_number) !!}
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-3">Scan untuk akses detail aset secara cepat.</p>
                    </div>

                    {{-- Info Badge --}}
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                        <p class="text-xs text-indigo-600 font-semibold uppercase mb-1">Terakhir Diupdate</p>
                        <p class="text-sm text-gray-700 font-mono">{{ $asset->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script AlpineJS (Wajib untuk Carousel) --}}
<script src="//unpkg.com/alpinejs" defer></script>
@endsection