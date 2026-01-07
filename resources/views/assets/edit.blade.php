@extends('layouts.main')

@section('container')
<div class="max-w-2xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Edit Data User
        </h2>
        <a href="/users" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
            &larr; Batal
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
        <div class="p-6 bg-white border-b border-gray-200">
            <form action="/users/{{ $user->id }}" method="POST">
                @method('put')
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role / Jabatan</label>
                        <select name="role" id="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Karyawan (User)</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-md border border-yellow-200">
                        <label for="password" class="block text-sm font-medium text-yellow-800">Ubah Password</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-yellow-400 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <p class="text-xs text-yellow-600 mt-1">*Hanya isi jika ingin mereset password user ini.</p>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection