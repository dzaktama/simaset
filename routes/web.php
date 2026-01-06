<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; // Panggil Controller-nya


// Arahkan URL '/' ke UserController, fungsi index
Route::get('/', [UserController::class, 'index']);

// Route untuk TAMPILKAN Form Create (GET)
Route::get('/users/create', [UserController::class, 'create']);


// Route untuk PROSES SIMPAN data (Method POST)
Route::post('/users', [UserController::class, 'store']);


// Route untuk MENGHAPUS data (Method DELETE)
Route::delete('/users/{id}', [UserController::class, 'destroy']);



// 1. Route untuk TAMPILKAN Form Edit (GET)
Route::get('/users/{id}/edit', [UserController::class, 'edit']);

// 2. Route untuk PROSES UPDATE data (PUT)
Route::put('/users/{id}', [UserController::class, 'update']);

use App\Http\Controllers\AuthController;

// Jalur untuk Tamu (Belum Login)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Jalur Logout (Harus Login dulu baru bisa logout)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

use App\Http\Controllers\PostController;
// Route untuk TAMPILKAN Form Create Post
// 1. Route spesifik harus duluan
Route::get('/blog/create', [PostController::class, 'create']);
    Route::post('/blog', [PostController::class, 'store']);

// Route Halaman Blog
Route::get('/blog', [PostController::class, 'index']);

// Route Detail Artikel (Single Post)
Route::get('/blog/{id}', [PostController::class, 'show']);


// 2. Route dinamis ({id}) belakangan
Route::get('/blog/{id}', [PostController::class, 'show']);

