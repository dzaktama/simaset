@extends('layouts.main')

@section('container')
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900">Blog Terbaru</h1>
        @auth
        <div class="mt-4">
            <a href="/blog/create" class="inline-block bg-blue-600 text-white px-5 py-2 rounded-full font-bold hover:bg-blue-700 transition">
                + Tulis Artikel Baru
            </a>
        </div>
        @endauth
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        
        @foreach ($posts as $post)
        <article class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100 flex flex-col h-full">
            
            <img class="w-full h-48 object-cover" src="https://source.unsplash.com/500x300?tech,computer&sig={{ $loop->iteration }}" alt="Gambar Artikel">

            <div class="p-6 flex flex-col flex-grow">
                <h2 class="text-xl font-bold text-gray-900 mb-2 hover:text-blue-600">
                    <a href="/blog/{{ $post->id }}">{{ $post->title }}</a>
                </h2>

                <div class="text-sm text-gray-500 mb-4 flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Penulis: {{ $post->author->name }}</span>
                    <span>• {{ $post->created_at->diffForHumans() }}</span>
                </div>

                <p class="text-gray-600 mb-4 flex-grow">
                    {{ Str::limit($post->body, 100) }} </p>

                <a href="/blog/{{ $post->id }}" class="mt-auto inline-flex items-center text-blue-600 font-semibold hover:underline">
                    Baca Selengkapnya &rarr;
                </a>
            </div>
        </article>
        @endforeach

    </div>
@endsection