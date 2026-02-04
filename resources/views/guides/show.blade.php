@extends('layouts.main')

@section('container')
{{-- COLOR THEME SETUP --}}
@php
    $themeMap = [
        'blue'   => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'text-blue-600', 'num_bg' => 'bg-blue-600', 'num_text' => 'text-white'],
        'green'  => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-200', 'icon' => 'text-green-600', 'num_bg' => 'bg-green-600', 'num_text' => 'text-white'],
        'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'icon' => 'text-indigo-600', 'num_bg' => 'bg-indigo-600', 'num_text' => 'text-white'],
        'red'    => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => 'text-red-600', 'num_bg' => 'bg-red-600', 'num_text' => 'text-white'],
        'amber'  => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'text-amber-600', 'num_bg' => 'bg-amber-600', 'num_text' => 'text-white'],
        'teal'   => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'border' => 'border-teal-200', 'icon' => 'text-teal-600', 'num_bg' => 'bg-teal-600', 'num_text' => 'text-white'],
        'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => 'text-purple-600', 'num_bg' => 'bg-purple-600', 'num_text' => 'text-white'],
        'gray'   => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'icon' => 'text-gray-600', 'num_bg' => 'bg-gray-600', 'num_text' => 'text-white'],
    ];
    // Default fallback
    $theme = $themeMap[$guide->color] ?? $themeMap['blue'];
@endphp

<div class="min-h-screen bg-gray-50/50">
    
    {{-- 1. HEADER SECTION (Full Width) --}}
    <div class="relative {{ $theme['bg'] }} border-b {{ $theme['border'] }} pt-12 pb-16 overflow-hidden">
        {{-- Decorative Blob --}}
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-40 blur-3xl"></div>

        <div class="max-w-5xl mx-auto px-6 sm:px-8 relative z-10 text-center">
            
            {{-- Breadcrumb --}}
            <a href="{{ route('guides.index') }}" class="inline-flex items-center text-sm font-semibold {{ $theme['text'] }} hover:underline mb-6 transition-all bg-white/50 px-3 py-1 rounded-full backdrop-blur-sm">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Daftar Panduan
            </a>

            {{-- Title & Meta --}}
            <div class="flex flex-col items-center gap-4">
                <div class="p-4 bg-white rounded-2xl shadow-sm {{ $theme['icon'] }}">
                    {{-- Render Icon based on DB --}}
                    @if($guide->icon == 'book-open')
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    @elseif($guide->icon == 'cube')
                         <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    @elseif($guide->icon == 'clipboard-check')
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    @elseif($guide->icon == 'hand-raised')
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" /></svg>
                    @else
                         <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @endif
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ $guide->title }}
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    {{ $guide->description }}
                </p>

                {{-- Admin Buttons --}}
                @if(auth()->user()->role->slug === 'super_admin')
                    <div class="flex gap-3 mt-2">
                        <a href="{{ route('guides.edit', $guide->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Edit Konten
                        </a>
                        <form action="{{ route('guides.destroy', $guide->id) }}" method="POST" onsubmit="return confirm('Hapus panduan ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 2. STEPS SECTION --}}
    <div class="max-w-4xl mx-auto px-6 py-12 space-y-8">
        @if($guide->steps->count() > 0)
            @foreach($guide->steps as $index => $step)
                
                {{-- UNIFIED CARD --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row">
                    
                    {{-- Left Content --}}
                    <div class="p-6 md:p-8 flex-1">
                        {{-- Header Step Number & Title --}}
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $theme['num_bg'] }} {{ $theme['num_text'] }} flex items-center justify-center font-bold text-sm shadow-sm ring-4 ring-white">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 leading-tight mb-2">{{ $step->title }}</h3>
                                {{-- Optional: If description contains "Navigasi:", parse it or just show description --}}
                            </div>
                        </div>

                        {{-- Rich Description --}}
                        <div class="prose prose-indigo prose-sm text-gray-600 pl-12 whitespace-pre-line">
                            {{ $step->description }}
                        </div>
                    </div>

                    {{-- Right Image (Visualisasi) --}}
                     <div class="bg-gray-50 border-t md:border-t-0 md:border-l border-gray-100 md:w-1/3 min-h-[200px] flex items-center justify-center relative group overflow-hidden">
                        @if($step->image)
                             <img src="{{ Storage::url($step->image) }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="{{ $step->title }}">
                        @else
                            <div class="text-center p-6">
                                <svg class="w-12 h-12 text-gray-200 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs text-gray-400 font-medium">Visualisasi {{ $step->title }}</span>
                            </div>
                        @endif
                    </div>
                </div>

            @endforeach
        @else
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                <h3 class="text-lg font-medium text-gray-900">Belum ada konten langkah</h3>
                <p class="text-gray-500 max-w-xs mx-auto mt-2">Panduan ini belum memiliki langkah-langkah detail.</p>
            </div>
        @endif
    </div>

    {{-- Footer CTA --}}
    <div class="text-center pb-12">
        <p class="text-sm text-gray-400">Punya kendala lain? Hubungi IT Support.</p>
    </div>
</div>
@endsection
