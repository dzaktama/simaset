<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AssetReturnController;

/*
|--------------------------------------------------------------------------
| Web Routes (Fixed & Compatible)
|--------------------------------------------------------------------------
*/

// 1. Redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Auth
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest');
    Route::get('/register', 'showRegisterForm')->name('register')->middleware('guest');
    Route::post('/register', 'register')->middleware('guest');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});

// 3. Authenticated (User & Admin)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard & Umum
    Route::get('/home', [AssetController::class, 'dashboard'])->name('dashboard');
    Route::get('/charts/asset-stats', [AssetController::class, 'chartsData'])->name('charts.assets');
    Route::get('/charts/borrow-stats', [AssetController::class, 'borrowStats'])->name('charts.borrows');

    // Aset (User View Only, Admin Full Access handled in Controller/View)
    Route::get('/assets/map', [AssetController::class, 'locationMap'])->name('assets.map');
    Route::get('/assets/my', [AssetController::class, 'myAssets'])->name('assets.my'); 
    Route::get('/assets/{id}/scan-qr-image', [AssetController::class, 'scanQrImage'])->name('assets.scan_image');
    Route::get('/assets/scan/{asset}', [AssetController::class, 'scanQr'])->name('assets.scan');
    Route::resource('assets', AssetController::class);

    // ====================================================
    // PEMINJAMAN (BORROWING)
    // ====================================================
    
    // A. Rute User (Store & History)
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])->name('borrowing.history');
    Route::post('/borrowing/{id}/return-user', [BorrowingController::class, 'returnAsset'])->name('borrowing.return_user');

    // B. Rute Shared / Admin (Index, Show, Approve, Reject)
    // Kita taruh di luar grup 'admin' prefix URL, tapi diproteksi middleware di Controller atau Logic
    // Tujuannya agar nama rute tetap 'borrowing.index', 'borrowing.show' dsb.
    
    // List Peminjaman (Admin Only di Controller)
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index');
    
    // Detail Peminjaman (Admin & User Pemilik di Controller)
    Route::get('/borrowing/{id}', [BorrowingController::class, 'show'])->name('borrowing.show');
    
    // Approve & Reject (Admin Only)
    // PERHATIAN: Ini rute yang dipanggil oleh form di admin_tables.blade.php & show.blade.php
    Route::post('/borrowing/{id}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve');
    Route::post('/borrowing/{id}/reject', [BorrowingController::class, 'reject'])->name('borrowing.reject');
    Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])->name('borrowing.return');

    // Returns Resource
    Route::resource('returns', AssetReturnController::class)->only(['index', 'show', 'update', 'store']);
    Route::post('/returns/{return}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');

    // ====================================================
    // KHUSUS ADMIN (User Management & Report)
    // ====================================================
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    });

    // Blog
    Route::resource('posts', PostController::class);
});