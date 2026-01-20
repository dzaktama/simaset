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
});

// 3. Authenticated Routes (User & Admin)
Route::middleware(['auth'])->group(function () {
    
    // --- DASHBOARD & STATISTIK ---
    Route::get('/home', [AssetController::class, 'dashboard'])->name('dashboard');
    Route::get('/charts/asset-stats', [AssetController::class, 'chartsData'])->name('charts.assets');
    Route::get('/charts/borrow-stats', [AssetController::class, 'borrowStats'])->name('charts.borrows');

    // --- MANAJEMEN ASET ---
    // Custom Routes untuk Aset
    Route::get('/assets/map', [AssetController::class, 'locationMap'])->name('assets.map');
    Route::get('/assets/my', [AssetController::class, 'myAssets'])->name('assets.my'); 
    Route::get('/assets/{id}/scan-qr-image', [AssetController::class, 'scanQrImage'])->name('assets.scan_image');
    Route::get('/assets/scan/{asset}', [AssetController::class, 'scanQr'])->name('assets.scan');
    
    // Resource Aset (index, create, store, show, edit, update, destroy)
    // Akses create/edit/delete dibatasi oleh Middleware di dalam Controller atau Blade
    Route::resource('assets', AssetController::class);

    // --- MANAJEMEN PEMINJAMAN (BORROWING) ---
    // Masalah "Route not defined" selesai disini karena kita pakai nama standar.
    
    // 1. User Mengajukan & History
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])->name('borrowing.history');
    
    // 2. Admin List & Detail (Dipakai User juga untuk detail punya sendiri)
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index');
    Route::get('/borrowing/{id}', [BorrowingController::class, 'show'])->name('borrowing.show');
    
    // 3. Action Buttons (Approve, Reject, Return)
    // PENTING: Rute Reject ada di sini. URL: /borrowing/{id}/reject
    Route::post('/borrowing/{id}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve');
    Route::post('/borrowing/{id}/reject', [BorrowingController::class, 'reject'])->name('borrowing.reject');
    
    // 4. Pengembalian (Return)
    // Bisa dipanggil oleh Admin (di dashboard) atau User (di history)
    Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])->name('borrowing.return');
    Route::post('/borrowing/{id}/return-user', [BorrowingController::class, 'returnAsset'])->name('borrowing.return_user');

    // --- PENGEMBALIAN (RESOURCE) ---
    Route::resource('returns', AssetReturnController::class)->only(['index', 'show', 'update', 'store']);
    Route::post('/returns/{return}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');

    // --- BLOG / POSTINGAN ---
    Route::resource('posts', PostController::class);

    // ====================================================
    // KHUSUS ADMIN (Middleware Guard Tambahan)
    // ====================================================
    Route::middleware(['admin'])->group(function () {
        
        // Manajemen User (CRUD User)
        Route::resource('users', UserController::class);

        // Laporan (Reports)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    });
});