@extends('layouts.main')

@section('title', 'Pusat Bantuan')

@section('container')
<div class="min-h-screen bg-gray-50/50 p-6 sm:p-8">
    {{-- Header Section --}}
    <div class="max-w-7xl mx-auto mb-10 text-center sm:text-left flex flex-col sm:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl mb-3">
                Pusat Bantuan & Panduan Sistem
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl">
                Dokumentasi lengkap untuk membantu Anda mengoperasikan SIMASET. 
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 uppercase tracking-wide ml-2">
                    ROLE: {{ str_replace('_', ' ', $role) }}
                </span>
            </p>
        </div>
        
        @if(auth()->user()->role->slug === 'super_admin')
            <a href="{{ route('guides.create') }}" class="inline-flex items-center px-5 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-search hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Buat Panduan Baru
            </a>
        @endif
    </div>

    {{-- Grid Content --}}
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        @foreach($guides as $guide)
            
            @php
                // Dynamic Color Classes based on 'color' key
                $colorClasses = [
                    'blue'   => 'bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white',
                    'green'  => 'bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white',
                    'indigo' => 'bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white',
                    'red'    => 'bg-red-50 text-red-600 group-hover:bg-red-600 group-hover:text-white',
                    'amber'  => 'bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white',
                    'teal'   => 'bg-teal-50 text-teal-600 group-hover:bg-teal-600 group-hover:text-white',
                    'purple' => 'bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white',
                    'gray'   => 'bg-gray-50 text-gray-600 group-hover:bg-gray-700 group-hover:text-white',
                ];
                $iconColor = $colorClasses[$guide->color] ?? $colorClasses['blue'];
            @endphp

            <a href="{{ route('guides.show', $guide->id) }}" class="group relative bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                {{-- Decorative Background Blob (Optional) --}}
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-{{ $guide->color }}-50 opacity-50 group-hover:scale-150 transition-transform duration-700 ease-in-out"></div>

                {{-- Big Icon --}}
                <div class="relative h-16 w-16 rounded-2xl flex items-center justify-center mb-6 transition-colors duration-300 {{ $iconColor }} shadow-inner">
                    {{-- Render Icon based on name --}}
                    @if($guide->icon == 'book-open')
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    @elseif($guide->icon == 'question-mark-circle')
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @elseif($guide->icon == 'hand-raised')
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" /></svg>
                    @elseif($guide->icon == 'exclamation-triangle')
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    @elseif($guide->icon == 'cube')
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    @elseif($guide->icon == 'clipboard-check')
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    @elseif($guide->icon == 'users')
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    @elseif($guide->icon == 'cog')
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    @else
                        {{-- Fallback Icon --}}
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @endif
                </div>

                <div class="relative">
                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors mb-3">
                        {{ $guide->title }}
                    </h3>
                    <p class="text-gray-500 group-hover:text-gray-600 leading-relaxed mb-6">
                        {{ $guide->description }}
                    </p>
                    <div class="flex items-center text-sm font-bold text-indigo-600 group-hover:text-indigo-700">
                        <span>Baca Panduan</span>
                        <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
