<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Buat excerpt otomatis

class PostController extends Controller
{
    public function index()
    {
        return view('blog', [
            'title' => 'Blog & Artikel',
            // Ambil semua post, urutkan terbaru (latest), sertakan data author biar ringan
            'posts' => Post::with('author')->latest()->get()
        ]);
    }
    
    // Fitur Tambahan: Baca Detail Artikel
    public function show($id)
    {
        $post = Post::find($id);
        return view('post', [
            'title' => $post->title,
            'post' => $post
        ]);
    }

    // 1. TAMPILKAN FORM TULIS ARTIKEL
    public function create()
    {
        return view('create_post', [
            'title' => 'Tulis Artikel Baru'
        ]);
    }

    // 2. PROSES SIMPAN ARTIKEL
    public function store(Request $request)
    {
        // Validasi
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        // Tambahan data otomatis
        $validatedData['user_id'] = Auth::id(); // Ambil ID user yang sedang login
        
        // Simpan
        Post::create($validatedData);

        // Balik ke halaman blog
        return redirect('/blog')->with('success', 'Artikel berhasil diterbitkan!');
    }
}