<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =======================
// AUTH
// =======================
Route::get('/', function () {
    return view('auth.login'); // langsung view, AMAN
})->name('login');

Route::post('/login', [AuthController::class, 'authenticate'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// =======================
// AUTHENTICATED USER
// =======================
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AssetController::class, 'dashboard'])
        ->name('dashboard');

    // ===================
    // ASSET (USER)
    // ===================
    Route::get('/assets', [AssetController::class, 'index'])
        ->name('assets.index');

    Route::get('/assets/my', [AssetController::class, 'myAssets'])
        ->name('assets.my');

    Route::get('/assets/{asset}', [AssetController::class, 'show'])
        ->name('assets.show');

    // ===================
    // BORROWING (USER)
    // ===================
    Route::post('/borrowing/store', [BorrowingController::class, 'store'])
        ->name('borrowing.store');

    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])
        ->name('borrowing.history');

    Route::get('/borrowing/{id}', [BorrowingController::class, 'show'])
        ->name('borrowing.show');

    Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])
        ->name('borrowing.return');

    // ===================
    // QR & MAP
    // ===================
    Route::get('/scan/{asset}', [AssetController::class, 'scanQr'])
        ->name('assets.scan');

    Route::get('/assets/{id}/qr', [AssetController::class, 'scanQrImage'])
        ->name('assets.qr_image');

    Route::get('/map', [AssetController::class, 'locationMap'])
        ->name('assets.map');
});


// =======================
// ADMIN ONLY
// =======================
Route::middleware(['auth', 'admin'])->group(function () {

    // ===================
    // ASSET (ADMIN CRUD)
    // ===================
    Route::get('/assets/create', [AssetController::class, 'create'])
        ->name('assets.create');

    Route::post('/assets', [AssetController::class, 'store'])
        ->name('assets.store');

    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])
        ->name('assets.edit');

    Route::put('/assets/{asset}', [AssetController::class, 'update'])
        ->name('assets.update');

    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])
        ->name('assets.destroy');

    // ===================
    // BORROWING (ADMIN)
    // ===================
    Route::get('/admin/borrowing', [BorrowingController::class, 'index'])
        ->name('admin.borrowing.index');

    Route::post('/admin/borrowing/{id}/approve', [BorrowingController::class, 'approve'])
        ->name('admin.borrowing.approve');

    Route::post('/admin/borrowing/{id}/reject', [BorrowingController::class, 'reject'])
        ->name('admin.borrowing.reject');

    // ===================
    // USER MANAGEMENT
    // ===================
    Route::resource('users', UserController::class);

    // ===================
    // REPORT
    // ===================
    Route::get('/reports', [BorrowingController::class, 'report'])
        ->name('reports.index');

    Route::get('/reports/export', [BorrowingController::class, 'exportExcel'])
        ->name('reports.export');

    // ===================
    // API
    // ===================
    Route::get('/api/charts/assets', [AssetController::class, 'chartsData']);
    Route::get('/api/charts/borrowing', [AssetController::class, 'borrowStats']);
});
