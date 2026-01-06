@extends('layouts.main')

@section('container')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-100">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">✍️ Tulis Artikel Baru</h1>

    <form action="/blog" method="POST">
        @csrf
        
        <div class="mb-5">
            <label for="title" class="block mb-2 text-sm font-medium text-gray-900">Judul Artikel</label>
            <input type="text" name="title" id="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Tutorial Laravel untuk Pemula" required>
        </div>

        <div class="mb-5">
            <label for="body" class="block mb-2 text-sm font-medium text-gray-900">Isi Artikel</label>
            <textarea name="body" id="body" rows="8" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Tulis ceritamu di sini..." required></textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Terbitkan Artikel</button>
            <a href="/blog" class="text-gray-500 hover:text-gray-900 text-sm font-medium">Batal</a>
        </div>
    </form>
</div>
@endsection