@extends('layouts.main')

@section('container')
    <div class="max-w-4xl mx-auto">
        <a href="/blog" class="inline-flex items-center text-gray-500 hover:text-blue-600 mb-6">
            &larr; Kembali ke Blog
        </a>

        <article class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4 leading-tight">{{ $post->title }}</h1>

            <div class="flex items-center text-gray-500 text-sm mb-8 border-b pb-8">
                <span class="font-semibold text-gray-900 mr-2">Oleh: {{ $post->author->name }}</span>
                <span>• Diposting {{ $post->created_at->format('d F Y') }}</span>
            </div>

            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                {!! nl2br(e($post->body)) !!} 
            </div>
        </article>
    </div>
@endsection