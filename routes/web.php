<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AssetReturnController;

// Halaman Welcome
Route::get('/', function () { return view('welcome'); });

// Authentication
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest');
    Route::get('/register', 'showRegisterForm')->name('register')->middleware('guest');
    Route::post('/register', 'register')->middleware('guest');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});

// DASHBOARD & APPS (Auth Required)
Route::middleware(['auth'])->group(function () {
    
    // [PENTING 1] Nama route harus 'dashboard' (bukan home)
    Route::get('/home', [AssetController::class, 'dashboard'])->name('dashboard');

    // Chart Data
    Route::get('/charts/asset-stats', [AssetController::class, 'chartsData'])->name('charts.assets');
    Route::get('/charts/borrow-stats', [AssetController::class, 'borrowStats'])->name('charts.borrows');

    // [PENTING 2] Nama route harus 'assets.my' (konsisten dengan sidebar)
    Route::get('/assets/my', [AssetController::class, 'myAssets'])->name('assets.my'); 
    
    // Manajemen Aset Lainnya
    Route::get('/assets/map', [AssetController::class, 'locationMap'])->name('assets.map');
    Route::get('/assets/{id}/scan-qr-image', [AssetController::class, 'scanQrImage'])->name('assets.scan_image');
    Route::get('/assets/scan/{asset}', [AssetController::class, 'scanQr'])->name('assets.scan');
    Route::resource('assets', AssetController::class);

    // Peminjaman
    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])->name('borrowing.history');
    Route::resource('borrowing', BorrowingController::class);
    Route::post('/borrowing/{id}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve')->middleware('admin');
    Route::post('/borrowing/{id}/reject', [BorrowingController::class, 'reject'])->name('borrowing.reject')->middleware('admin');
    Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])->name('borrowing.return');

    // Pengembalian & Laporan
    Route::resource('returns', AssetReturnController::class)->only(['index', 'show', 'update']);
    Route::post('/returns/{return}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // Admin Users & Posts
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
    Route::resource('posts', PostController::class);
});