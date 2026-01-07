<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- AUTHENTICATION ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'register']); // Opsional
    Route::post('/register', [AuthController::class, 'store']);   // Opsional
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// --- MAIN FEATURES (BUTUH LOGIN) ---
Route::middleware(['auth'])->group(function () {
    
    // 1. DASHBOARD (Semua User)
    Route::get('/', [AssetController::class, 'dashboard'])->name('home');

    // 2. KATALOG ASET (PENTING: Ditaruh DI LUAR grup admin agar Karyawan bisa akses)
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    
    // 3. FITUR KARYAWAN
    Route::get('/my-assets', [AssetController::class, 'myAssets']);
    Route::post('/assets/{id}/request', [AssetController::class, 'requestAsset']);

    // --- AREA KHUSUS ADMIN ---
    Route::middleware(['is_admin'])->group(function () {
        
        // Manajemen Aset (Kecuali Index/Show yang sudah di atas)
        // Ini menangani: Create, Store, Edit, Update, Destroy
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

        // Manajemen User
        Route::resource('users', UserController::class);

        // Approval & Report
        Route::post('/requests/{id}/approve', [AssetController::class, 'approveRequest']);
        Route::post('/requests/{id}/reject', [AssetController::class, 'rejectRequest']);
        Route::get('/report/print-assets', [AssetController::class, 'printReport'])->name('report.assets');
    });
});