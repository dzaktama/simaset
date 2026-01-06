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