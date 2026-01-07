<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes (Fixed Version)
|--------------------------------------------------------------------------
*/

// 1. ROUTE PUBLIC (Tamu / Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout harus bisa diakses siapa saja yang login
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');


// 2. ROUTE LOGGED IN (Admin & Karyawan)
Route::middleware(['auth'])->group(function () {
    
    // DASHBOARD
    Route::get('/', [AssetController::class, 'dashboard'])->name('dashboard');

    // FITUR KARYAWAN
    Route::get('/my-assets', [AssetController::class, 'myAssets'])->name('my-assets');
    Route::post('/assets/{id}/request', [AssetController::class, 'requestAsset']); // Action klik tombol pinjam

    // FITUR SHARED (PENTING: Ini perbaikannya!)
    // Kita keluarkan 'index' dari middleware admin, biar Karyawan bisa lihat daftar barang available
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index'); 

    // 3. AREA KHUSUS ADMIN (Satpam: IsAdmin)
    Route::middleware(['is_admin'])->group(function () {
        
        // MANAJEMEN ASET (Create, Edit, Update, Delete)
        // Kita definisikan manual karena 'index' sudah di luar
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

        // MANAJEMEN USER
        Route::resource('users', UserController::class);

        // APPROVAL SYSTEM
        Route::post('/requests/{id}/approve', [AssetController::class, 'approveRequest']);
        Route::post('/requests/{id}/reject', [AssetController::class, 'rejectRequest']);

        // LAPORAN
        Route::get('/report/print-assets', [AssetController::class, 'printReport']);
    });

});