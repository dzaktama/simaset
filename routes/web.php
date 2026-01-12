<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetRequestController;
use App\Http\Controllers\AssetReturnController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES (Tamu) ---
Route::middleware('guest')->group(function () {
    Route::get('/', function () { return view('welcome'); });
    
    // Login & Register
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store']);
    
    // Blog (Public)
    Route::get('/blog', [PostController::class, 'index']);
    Route::get('/post/{post:slug}', [PostController::class, 'show']);
});

// --- CAPTCHA REFRESH ---
Route::get('/refresh-captcha', [AuthController::class, 'refreshCaptcha'])->name('refresh.captcha');

// --- PROTECTED ROUTES (User Login) ---
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard Utama
    Route::get('/dashboard', [AssetController::class, 'dashboard'])->name('dashboard');

    // === SECTION: KARYAWAN ===
    // Aset Saya
    Route::get('/my-assets', [AssetController::class, 'myAssets'])->name('my.assets');
    
    // Request Peminjaman (Booking & Pinjam)
    Route::post('/requests', [AssetRequestController::class, 'store'])->name('requests.store');
    
    // Pengembalian Aset (User)
    Route::post('/returns', [AssetReturnController::class, 'store'])->name('returns.store');

    // === SECTION: ADMIN ONLY ===
    Route::middleware(['is_admin'])->group(function () {
        
        // Manajemen Aset (CRUD)
        Route::resource('assets', AssetController::class);
        
        // Manajemen User
        Route::resource('users', UserController::class);
        
        // Approval Request (Terima/Tolak)
        Route::post('/requests/{id}/approve', [AssetRequestController::class, 'approve'])->name('requests.approve');
        Route::post('/requests/{id}/reject', [AssetRequestController::class, 'reject'])->name('requests.reject');
        
        // Verifikasi Pengembalian (Restock)
        Route::post('/returns/{id}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');

        // Laporan (View & PDF Export)
        // [PERBAIKAN] Pastikan method report() dan exportPdf() ada di AssetController
        Route::get('/reports', [AssetController::class, 'report'])->name('report.index');
        Route::get('/reports/export-pdf', [AssetController::class, 'exportPdf'])->name('report.pdf');
    });

});