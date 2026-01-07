<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController; // Import Controller Baru
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// UBAH INI: Arahkan Root URL ke Dashboard Asset
Route::get('/', [AssetController::class, 'index'])->name('dashboard');

// --- Route User Management (Tetap dipertahankan untuk fitur User) ---
Route::get('/users/create', [UserController::class, 'create']);
Route::post('/users', [UserController::class, 'store']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::get('/users/{id}/edit', [UserController::class, 'edit']);
Route::put('/users/{id}', [UserController::class, 'update']);

// --- Auth Routes ---
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// --- Blog/Ticket Routes (Opsional, sesuaikan jika masih dipakai) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/blog/create', [PostController::class, 'create']);
    Route::post('/blog', [PostController::class, 'store']);
    Route::get('/blog', [PostController::class, 'index']);
    Route::get('/blog/{id}', [PostController::class, 'show']);
});