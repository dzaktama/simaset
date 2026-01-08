<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================================
// 1. TAMU (BELUM LOGIN)
// ==========================================================
Route::middleware('guest')->group(function () {
    // FIX ERROR: Ubah url '/' menjadi '/login' agar sesuai standar auth Laravel
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    
    // Redirect root '/' ke '/login' otomatis
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

// Logout (Wajib POST)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ==========================================================
// 2. USER LOGIN (ADMIN & KARYAWAN)
// ==========================================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AssetController::class, 'dashboard'])->name('dashboard');
    
    // Redirect /home ke dashboard
    Route::get('/home', function () { return redirect('/dashboard'); });

    // --- MENU KARYAWAN ---
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index'); 
    Route::get('/my-assets', [AssetController::class, 'myAssets']);
    Route::post('/requests', [AssetRequestController::class, 'store']); 
});


// ==========================================================
// 3. KHUSUS ADMINISTRATOR
// ==========================================================
Route::middleware(['auth', 'admin'])->group(function () { 
    
    // Manajemen User
    Route::resource('/users', UserController::class)->except(['show']);

    // Manajemen Aset (Kecuali index, karena index dipakai bersama)
    Route::resource('/assets', AssetController::class)->except(['index']);
    
    // Laporan & Approval
    Route::get('/report/assets', [AssetController::class, 'printReport'])->name('report.assets');
    Route::post('/requests/{id}/approve', [AssetRequestController::class, 'approve']);
    Route::post('/requests/{id}/reject', [AssetRequestController::class, 'reject']);
});