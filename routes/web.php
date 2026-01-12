<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetRequestController;
use App\Http\Controllers\AssetReturnController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;

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

// --- PROTECTED ROUTES (User Login: Admin & Karyawan) ---
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard Utama
    Route::get('/dashboard', [AssetController::class, 'dashboard'])->name('dashboard');

    // [PERBAIKAN] Route '/assets' (Index/Daftar) ditaruh DI LUAR grup admin
    // Agar Karyawan bisa akses halaman list untuk melakukan peminjaman
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    
    // QR Code Scanning Route (Public untuk karyawan & admin)
    Route::get('/assets/scan/{id}', [AssetController::class, 'scanQr'])->name('assets.scan');

    // === SECTION: KARYAWAN ===
    // Aset Saya
    Route::get('/my-assets', [AssetController::class, 'myAssets'])->name('my.assets');
    
    // Request Peminjaman (Booking & Pinjam)
    Route::post('/requests', [AssetRequestController::class, 'store'])->name('requests.store');
    
    // Pengembalian Aset (User)
    Route::post('/returns', [AssetReturnController::class, 'store'])->name('returns.store');

    // === SECTION: ADMIN ONLY ===
    Route::middleware(['is_admin'])->group(function () {
        
        // Manajemen Aset (CRUD Lengkap KECUALI Index yang sudah ada di atas)
        // Kita gunakan 'except' index agar tidak bentrok
        Route::resource('assets', AssetController::class)->except(['index']);
        
        // Manajemen User
        Route::resource('users', UserController::class);
        
        // Approval Request (Terima/Tolak)
        Route::post('/requests/{id}/approve', [AssetRequestController::class, 'approve'])->name('requests.approve');
        Route::post('/requests/{id}/reject', [AssetRequestController::class, 'reject'])->name('requests.reject');
        
        // Verifikasi Pengembalian (Restock)
        Route::post('/returns/{id}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');

        // Laporan (View & PDF Export)
        Route::get('/reports', [ReportController::class, 'report'])->name('report.index');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('report.pdf');
        Route::get('/reports/print', [ReportController::class, 'printReport'])->name('report.print');
        Route::get('/reports/download', [ReportController::class, 'downloadPdf'])->name('report.download');
    });

});

