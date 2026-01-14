<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetRequestController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman awal redirect ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes (Sudah Login)
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [AssetController::class, 'dashboard'])->name('dashboard');
    Route::get('/home', [AssetController::class, 'dashboard'])->name('home'); // Alias

    // --- FITUR ASET ---
    // PENTING: Route 'map' harus DI ATAS resource 'assets' agar tidak dianggap sebagai ID
    Route::get('/assets/map', [AssetController::class, 'locationMap'])->name('assets.map'); 
    Route::get('/assets/my-assets', [AssetController::class, 'myAssets'])->name('assets.my_assets');
    Route::get('/assets/scan/{asset}', [AssetController::class, 'scanQr'])->name('assets.scan');
    
    // Resource Controller untuk Aset
    Route::resource('assets', AssetController::class);

    // --- FITUR PEMINJAMAN (Borrowing) ---
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index');
    Route::get('/borrowing/create', [BorrowingController::class, 'create'])->name('borrowing.create');
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])->name('borrowing.history');
    Route::get('/borrowing/{id}', [BorrowingController::class, 'show'])->name('borrowing.show');
    
    // Action Khusus Peminjaman
    Route::post('/borrowing/{id}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve');
    Route::post('/borrowing/{id}/reject', [BorrowingController::class, 'reject'])->name('borrowing.reject');
    Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])->name('borrowing.return');
    Route::post('/borrowing/{id}/quick-approve', [BorrowingController::class, 'quickApprove'])->name('borrowing.quick_approve');

    // --- FITUR REPORT & CHARTS ---
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/api/charts/data', [AssetController::class, 'chartsData'])->name('api.charts.data');
    Route::get('/api/charts/borrow-stats', [AssetController::class, 'borrowStats'])->name('api.charts.borrow_stats');

    // --- KHUSUS ADMIN ---
    // Menggunakan alias 'is_admin' yang baru
    Route::middleware('is_admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Blog / Post (Opsional)
    Route::resource('posts', PostController::class);
});