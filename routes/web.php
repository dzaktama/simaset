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
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Welcome (Landing Page)
Route::get('/', function () {
    return view('welcome');
});

// AUTHENTICATION ROUTES
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest');
    Route::get('/register', 'showRegisterForm')->name('register')->middleware('guest');
    Route::post('/register', 'register')->middleware('guest');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});

// DASHBOARD & MANAGED ROUTES (Perlu Login)
Route::middleware(['auth'])->group(function () {
    
    // [PERBAIKAN] Ubah name('home') menjadi name('dashboard') agar sesuai dengan sidebar/layout
    Route::get('/home', [AssetController::class, 'dashboard'])->name('dashboard');

    // Chart Data Routes
    Route::get('/charts/asset-stats', [AssetController::class, 'chartsData'])->name('charts.assets');
    Route::get('/charts/borrow-stats', [AssetController::class, 'borrowStats'])->name('charts.borrows');

    // Manajemen Aset
    Route::get('/assets/map', [AssetController::class, 'locationMap'])->name('assets.map'); // Peta Lokasi
    Route::get('/assets/my', [AssetController::class, 'myAssets'])->name('assets.my'); // Aset Saya (User)
    
    // QR Code Scan Helpers
    Route::get('/assets/{id}/scan-qr-image', [AssetController::class, 'scanQrImage'])->name('assets.scan_image');
    Route::get('/assets/scan/{asset}', [AssetController::class, 'scanQr'])->name('assets.scan');

    Route::resource('assets', AssetController::class);

    // Manajemen Peminjaman (Borrowing)
    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])->name('borrowing.history');
    Route::resource('borrowing', BorrowingController::class);
    
    // Approval & Return (Admin Action)
    Route::post('/borrowing/{id}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve')->middleware('admin');
    Route::post('/borrowing/{id}/reject', [BorrowingController::class, 'reject'])->name('borrowing.reject')->middleware('admin');
    Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])->name('borrowing.return');

    // Pengembalian Aset (Returns Management)
    Route::resource('returns', AssetReturnController::class)->only(['index', 'show', 'update']);
    Route::post('/returns/{return}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');

    // Laporan (Reports)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    
    // Manajemen User (Hanya Admin)
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Blog / Post Internal
    Route::resource('posts', PostController::class);
});