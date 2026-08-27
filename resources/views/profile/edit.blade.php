@extends('layouts.main')

@section('title', 'Profile Saya')

@section('container')
<div class="max-w-7xl mx-auto py-6">
    
    <div class="flex items-center gap-3 mb-8">
        <div class="p-3 bg-indigo-600 rounded-xl text-white shadow-lg shadow-indigo-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Profile Saya</h2>
            <p class="text-sm text-gray-500">Kelola informasi pribadi, data karyawan, dan keamanan akun Anda.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm animate-fade-in-down">
            <p class="font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Berhasil!
            </p>
            <p class="text-sm mt-1">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Kiri: Informasi Personal & Pekerjaan --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Form Profil --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4 bg-gray-50">
                    <h3 class="font-semibold text-gray-800">Informasi Dasar & Pekerjaan</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        @php
                            $isSuperAdmin = optional($user->role)->slug === 'super_admin';
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Foto Profil --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil (Opsional)</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        @endif
                                    </div>
                                    <input type="file" name="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all">
                                </div>
                                @error('avatar')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Nama --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- ID Karyawan --}}
                            <div>
                                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">ID Karyawan (NIP/NIK)</label>
                                <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', $user->employee_id) }}" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isSuperAdmin ? 'bg-white text-gray-900' : 'bg-gray-100 text-gray-500 cursor-not-allowed' }}" 
                                       {{ $isSuperAdmin ? '' : 'readonly' }}>
                                @if(!$isSuperAdmin)
                                <p class="text-xs text-gray-400 mt-1">*Hanya Super Admin yang dapat mengubah ID Karyawan.</p>
                                @endif
                                @error('employee_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Nomor Telepon --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon (WhatsApp)</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Departemen --}}
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                                <input type="text" name="department" id="department" value="{{ old('department', $user->department) }}" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                @error('department')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Jabatan --}}
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                                <input type="text" name="position" id="position" value="{{ old('position', $user->position) }}" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                @error('position')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Lokasi Kerja --}}
                            <div class="md:col-span-2">
                                <label for="work_location" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Kerja / Ruangan</label>
                                <input type="text" name="work_location" id="work_location" value="{{ old('work_location', $user->work_location) }}" 
                                       placeholder="Misal: Gedung A Lantai 2, Ruang IT"
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                @error('work_location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 transition-all shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Form Ubah Password --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4 bg-gray-50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <h3 class="font-semibold text-gray-800">Keamanan (Ubah Password)</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="update_password" value="1">

                        <div class="grid grid-cols-1 gap-6 md:w-2/3">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                                <input type="password" name="current_password" id="current_password" required
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('current_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                                <input type="password" name="password" id="password" required
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="px-5 py-2.5 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-900 focus:ring-4 focus:ring-gray-300 transition-all shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- Kanan: Ringkasan Akun & Aset Aktif --}}
        <div class="space-y-6">
            
            {{-- Kartu Nama (Badge) --}}
            <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-6 text-center text-white shadow-lg shadow-indigo-200">
                <div class="w-24 h-24 mx-auto bg-white/20 rounded-full flex items-center justify-center overflow-hidden border-2 border-white/50 mb-4 backdrop-blur-sm">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <h3 class="font-bold text-xl">{{ $user->name }}</h3>
                <p class="text-indigo-100 text-sm mt-1">{{ $user->email }}</p>
                <div class="mt-4 inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-semibold backdrop-blur-md">
                    Role: {{ $user->role->name ?? 'User' }}
                </div>
            </div>

            {{-- Ringkasan Aset yang Dipinjam --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Aset yang Dipinjam</h3>
                    <span class="bg-indigo-100 text-indigo-700 py-0.5 px-2.5 rounded-full text-xs font-bold">{{ $borrowedAssets->count() }}</span>
                </div>
                <div class="p-0">
                    @if($borrowedAssets->count() > 0)
                        <ul class="divide-y divide-gray-100 max-h-80 overflow-y-auto custom-scrollbar">
                            @foreach($borrowedAssets as $req)
                                <li class="p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded bg-gray-100 flex-shrink-0 flex items-center justify-center overflow-hidden border border-gray-200">
                                            @if($req->asset->image)
                                                <img src="{{ asset('storage/' . $req->asset->image) }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 line-clamp-1">{{ $req->asset->name }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5 font-mono">{{ $req->asset->serial_number }}</p>
                                            <p class="text-[11px] text-indigo-600 mt-1 mt-1 font-medium bg-indigo-50 inline-block px-2 py-0.5 rounded">Sejak: {{ $req->borrowed_at ? \Carbon\Carbon::parse($req->borrowed_at)->format('d M Y') : '-' }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="p-3 border-t border-gray-100 bg-gray-50 text-center">
                            <a href="{{ route('assets.my') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Lihat Semua Aset Saya &rarr;</a>
                        </div>
                    @else
                        <div class="p-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="text-sm">Anda tidak sedang meminjam aset apapun saat ini.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
