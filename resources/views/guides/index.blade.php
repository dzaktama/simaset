@extends('layouts.main')

@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Pusat Bantuan & Panduan Sistem</h1>
        <p class="text-gray-600 mt-2">Dokumentasi penggunaan SIMASET untuk role: <span class="badge bg-indigo-100 text-indigo-700 px-2 py-1 rounded font-bold uppercase">{{ $role }}</span></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Card Panduan Dasar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
            <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Panduan Dasar</h3>
            <p class="text-sm text-gray-500 mb-4">Pelajari cara menavigasi dashboard, mengatur profil, dan fitur dasar lainnya.</p>
            <a href="#" class="text-indigo-600 text-sm font-bold hover:underline">Baca Selengkapnya &rarr;</a>
        </div>

        {{-- Card Role Specific --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
             <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Fitur {{ ucfirst($role) }}</h3>
            <p class="text-sm text-gray-500 mb-4">
                @if(in_array($role, ['super_admin']))
                    <strong>Mode Super Admin:</strong> Akses penuh ke seluruh panduan pengelolaan sistem, user, aset, dan pelaporan.
                @elseif($role == 'admin')
                    Cara mengelola aset, approval peminjaman, dan manajemen user.
                @elseif($role == 'user')
                    Cara melakukan peminjaman aset dan pelaporan kerusakan.
                @else
                     Panduan spesifik untuk role Anda.
                @endif
            </p>
            <a href="#" class="text-indigo-600 text-sm font-bold hover:underline">Baca Selengkapnya &rarr;</a>
        </div>

        {{-- Card FAQ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
             <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600 mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">FAQ & Troublehoot</h3>
            <p class="text-sm text-gray-500 mb-4">Pertanyaan umum dan solusi masalah yang sering terjadi.</p>
            <a href="#" class="text-indigo-600 text-sm font-bold hover:underline">Baca Selengkapnya &rarr;</a>
        </div>
    </div>
</div>
@endsection
