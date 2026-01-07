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

// 1. ROUTE PUBLIC
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// 2. ROUTE LOGGED IN (Admin & Karyawan)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard & Profile
    Route::get('/', [AssetController::class, 'dashboard']);
    Route::get('/my-assets', [AssetController::class, 'myAssets']);
    Route::post('/assets/{id}/request', [AssetController::class, 'requestAsset']);

    // AREA ADMIN (Wajib di dalam middleware is_admin)
    Route::middleware(['is_admin'])->group(function () {
        
        // Manajemen Aset
        Route::resource('assets', AssetController::class)->except(['show']); // show diganti modal
        
        // MANAJEMEN USER (INI YANG WAJIB ADA)
        Route::resource('users', UserController::class);

        // Approval & Report
        Route::post('/requests/{id}/approve', [AssetController::class, 'approveRequest']);
        Route::post('/requests/{id}/reject', [AssetController::class, 'rejectRequest']);
        Route::get('/report/print-assets', [AssetController::class, 'printReport'])->name('report.assets');
    });
});