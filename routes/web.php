<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- AUTHENTICATION ---
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- ROUTES UNTUK USER YANG SUDAH LOGIN ---
Route::middleware(['auth'])->group(function () {
    
    // Dashboard (Admin & User logic di dalam controller)
    Route::get('/dashboard', [AssetController::class, 'dashboard'])->name('dashboard');

    // --- FITUR ASET (USER) ---
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/my', [AssetController::class, 'myAssets'])->name('assets.my');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    
    // --- FITUR PEMINJAMAN (USER) ---
    Route::post('/borrowing/store', [BorrowingController::class, 'store'])->name('borrowing.store');
    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])->name('borrowing.history');
    Route::get('/borrowing/{id}', [BorrowingController::class, 'show'])->name('borrowing.show'); // Detail Peminjaman
    
    // User Return (Mengembalikan aset sendiri)
    // Note: Kita gunakan POST sesuai form di view
    Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])->name('borrowing.return');

    // QR Code & Map
    Route::get('/scan/{asset}', [AssetController::class, 'scanQr'])->name('assets.scan');
    Route::get('/assets/{id}/qr', [AssetController::class, 'scanQrImage'])->name('assets.qr_image');
    Route::get('/map', [AssetController::class, 'locationMap'])->name('assets.map');
});

// --- ROUTES KHUSUS ADMIN ---
Route::middleware(['auth', 'admin'])->group(function () {

    // --- MANAJEMEN ASET (CRUD) ---
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

    // --- MANAJEMEN PEMINJAMAN (ADMIN) ---
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index');
    
    // [PENTING] Route Actions untuk Admin (Approve/Reject)
    Route::post('/borrowing/{id}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve');
    Route::post('/borrowing/{id}/reject', [App\Http\Controllers\BorrowingController::class, 'reject'])->name('borrowing.reject');

    // --- MANAJEMEN USER ---
    Route::resource('users', UserController::class);

    // --- LAPORAN ---
    Route::get('/reports', [BorrowingController::class, 'report'])->name('reports.index');
    Route::get('/reports/export', [BorrowingController::class, 'exportExcel'])->name('reports.export');
    
    // API Chart Data
    Route::get('/api/charts/assets', [AssetController::class, 'chartsData']);
    Route::get('/api/charts/borrowing', [AssetController::class, 'borrowStats']);
});