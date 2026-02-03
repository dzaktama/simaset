@extends('layouts.main')

@section('title', 'Profile Saya')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 md:p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            Pengaturan Profile
        </h2>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
                <p class="font-bold">Berhasil!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama --}}
                <div class="col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="col-span-1">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Role (ReadOnly) --}}
                 <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role / Jabatan</label>
                    <input type="text" value="{{ $user->role->name ?? '-' }}" disabled
                           class="w-full rounded-md border-gray-200 bg-gray-50 text-gray-500 shadow-sm cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-400">*Role tidak dapat diubah sendiri.</p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 transition-all shadow-lg shadow-indigo-500/30">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
