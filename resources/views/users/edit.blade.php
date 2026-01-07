@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-2xl">
    <div class="mb-8 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Edit User: {{ $user->name }}</h2>
        <a href="/users" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Batal</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <form action="/users/{{ $user->id }}" method="POST" class="p-8">
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
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Karyawan (Staff)</option>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>

                <div class="rounded-md bg-yellow-50 p-4 border border-yellow-100">
                    <label for="password" class="block text-sm font-semibold text-yellow-800">Ubah Password (Opsional)</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-yellow-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm placeholder-yellow-700/50" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                    <p class="mt-1 text-xs text-yellow-700">*Isi hanya jika karyawan lupa password atau ingin reset.</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-6">
                <button type="submit" class="rounded-md bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection