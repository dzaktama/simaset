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
| Web Routes (Full Coverage & Fix)
|--------------------------------------------------------------------------
*/

// 1. Redirect Root ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Authentication Routes (Tamu)
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest');
    Route::get('/register', 'showRegisterForm')->name('register')->middleware('guest');
    Route::post('/register', 'register')->middleware('guest');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
    // Role Switcher Route
    Route::get('/impersonate', 'impersonate')->name('impersonate')->middleware(['auth']);
});

// 3. Authenticated Routes (User & Admin)
Route::middleware(['auth'])->group(function () {
    
    // ====================================================
    // GROUP A: UMUM (Bisa diakses Semua Karyawan/User)
    // ====================================================
    Route::get('/home', [AssetController::class, 'dashboard'])->name('dashboard');
    Route::get('/charts/asset-stats', [AssetController::class, 'chartsData'])->name('charts.assets'); // Dipakai di dashboard
    Route::get('/charts/borrow-stats', [AssetController::class, 'borrowStats'])->name('charts.borrows');
    
    // Lihat Katalog & Peta (Akses Semua User)
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->where('asset', '[0-9]+')->name('assets.show');
    Route::get('/assets/map', [AssetController::class, 'locationMap'])->name('assets.map');
    Route::get('/assets/my', [AssetController::class, 'myAssets'])->name('assets.my'); 
    Route::get('/assets/{id}/scan-qr-image', [AssetController::class, 'scanQrImage'])->name('assets.scan_image');
    Route::get('/assets/scan/{asset}', [AssetController::class, 'scanQr'])->name('assets.scan');

    // Transaksi Peminjaman (User)
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])->name('borrowing.history');
    Route::get('/borrowing/{id}', [BorrowingController::class, 'show'])->name('borrowing.show'); // Detail Shared
    Route::post('/borrowing/{id}/return-user', [BorrowingController::class, 'returnAsset'])->name('borrowing.return_user');

    // ====================================================
    // GROUP B: OPERASIONAL ASET (Admin & Service Center)
    // ====================================================
    // Boleh: Create, Edit, Update, Index, Show
    // Note: Service Center TIDAK BOLEH destroy (hapus), nanti diblokir di controller/blade
    Route::middleware(['role:admin,super_admin,service_center'])->group(function() {
        Route::resource('assets', AssetController::class)->except(['index', 'show']);
        Route::resource('maintenances', App\Http\Controllers\MaintenanceController::class);
    });

    // ====================================================
    // GROUP C: ADMINISTRASI PENUH (Admin & Super Admin)
    // ====================================================
    // Boleh: Approval Peminjaman, Laporan
    // Note: Service Center DILARANG masuk sini
    Route::middleware(['role:admin,super_admin'])->group(function() {
        
        // Approval & Manajemen Peminjaman
        Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index');
        // Route borrowing/{id} moved to general group (line 52)
        Route::post('/borrowing/{id}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve');
        Route::post('/borrowing/{id}/reject', [BorrowingController::class, 'reject'])->name('borrowing.reject');
        Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])->name('borrowing.return');

        // Pengembalian (Resource)
        Route::resource('returns', AssetReturnController::class)->only(['index', 'show', 'update', 'store']);
        Route::post('/returns/{return}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');

        // Laporan
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    });

    // ====================================================
    // GROUP D: SUPER ADMIN ONLY
    // ====================================================
    Route::middleware(['role:super_admin'])->group(function() {
        // Manajemen User (Full CRUD)
        Route::resource('users', UserController::class);
    });
});