<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetRequestController;
use App\Http\Controllers\AssetReturnController; // Pastikan Controller ini di-import

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
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index'); 
    Route::get('/my-assets', [AssetController::class, 'myAssets']);
    
    // Logic Request Pinjam
    Route::post('/requests', [AssetRequestController::class, 'store']); 

    // [FIX] Logic Pengembalian Aset (Returns)
    // Rute ini sebelumnya hilang, makanya error saat diklik
    Route::post('/returns', [AssetReturnController::class, 'store'])->name('returns.store');
});


// 3. KHUSUS ADMIN
Route::middleware(['auth', 'admin'])->group(function () { 
    // User Management
    Route::resource('/users', UserController::class)->except(['show']);
    
    // Resource Assets
    Route::resource('/assets', AssetController::class)->except(['index']);
    
    // --- FITUR LAPORAN ---
    Route::get('/report-generator', [AssetController::class, 'reportIndex'])->name('report.index');
    Route::get('/report/print', [AssetController::class, 'printReport'])->name('report.assets');
    
    // Approval Request Pinjam
    Route::post('/requests/{id}/approve', [AssetRequestController::class, 'approve']);
    Route::post('/requests/{id}/reject', [AssetRequestController::class, 'reject']);

    // [FIX] Approval Pengembalian Aset (Verify Return)
    Route::post('/returns/{id}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');
});