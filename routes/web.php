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

// 1. TAMU (Login/Logout)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/', function () { return redirect()->route('login'); });
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// 2. USER LOGIN (Admin & Karyawan)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AssetController::class, 'dashboard'])->name('dashboard');
    Route::get('/home', function () { return redirect('/dashboard'); });

    // Menu Karyawan
    // Index Assets kita taruh sini agar karyawan bisa LIHAT katalog & PINJAM
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index'); 
    Route::get('/my-assets', [AssetController::class, 'myAssets']);
    
    // Logic Request Pinjam (Wajib POST)
    Route::post('/requests', [AssetRequestController::class, 'store']); 
});


// 3. KHUSUS ADMIN
Route::middleware(['auth', 'admin'])->group(function () { 
    // User Management
    Route::resource('/users', UserController::class)->except(['show']);
    
    // Resource Assets
    Route::resource('/assets', AssetController::class)->except(['index']);
    
    // --- FITUR LAPORAN ---
    // Halaman Generator (Form)
    Route::get('/report-generator', [AssetController::class, 'reportIndex'])->name('report.index');
    // Action Cetak PDF (Hasil)
    Route::get('/report/print', [AssetController::class, 'printReport'])->name('report.assets');
    
    // Approval Request
    Route::post('/requests/{id}/approve', [AssetRequestController::class, 'approve']);
    Route::post('/requests/{id}/reject', [AssetRequestController::class, 'reject']);
});