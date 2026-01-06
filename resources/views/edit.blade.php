@extends('layouts.main')

@section('container')
    <h1 class="text-3xl font-bold mb-6">Edit Data User</h1>

    <div class="max-w-lg bg-white p-6 rounded-lg shadow-md">
        <form action="/users/{{ $user->id }}" method="POST">
            @csrf
            @method('PUT') <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ $user->name }}" class="w-full px-3 py-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ $user->email }}" class="w-full px-3 py-2 border rounded-lg" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password Baru (Opsional)</label>
                <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-lg" placeholder="Isi jika ingin ganti password">
            </div>

            <div class="flex justify-between">
                <a href="/" class="text-gray-500 hover:text-gray-700 py-2">Batal</a>
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">Update Data</button>
            </div>
        </form>
    </div>
@endsection