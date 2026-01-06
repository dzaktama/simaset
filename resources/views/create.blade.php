@extends('layouts.main')

@section('container')
    <h1 class="text-3xl font-bold mb-6">Tambah User Baru</h1>

    <div class="max-w-lg bg-white p-6 rounded-lg shadow-md">
        <form action="/users" method="POST">
            @csrf <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="flex justify-between">
                <a href="/" class="text-gray-500 hover:text-gray-700 py-2">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Simpan Data</button>
            </div>
        </form>
    </div>
@endsection