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
    // Route::get('/register', 'showRegisterForm')->name('register')->middleware('guest');
    // Route::post('/register', 'register')->middleware('guest');
    // Route::register route not used
    // Route::post('/logout', 'logout')->name('logout')->middleware('auth'); // moved below
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
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

    // ==========================================
    // FITUR CHAT (Percakapan Internal)
    // ==========================================
    // 1. Ini pintu masuk utama. Kalau user buka /chat, arahin ke halaman chat.
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    
    // 2. Ini buat ngambil data chat sama orang lain. Dipanggil pake AJAX (tanpa reload).
    Route::get('/chat/conversation/{userId}', [App\Http\Controllers\ChatController::class, 'getConversation'])->name('chat.get');
    
    // 3. Ini jalur buat ngirim pesan baru.
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');

    // Transaksi Peminjaman (User)
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])->name('borrowing.history');
    Route::get('/borrowing/{id}', [BorrowingController::class, 'show'])->name('borrowing.show'); // Detail Shared
    Route::post('/borrowing/{id}/return-user', [BorrowingController::class, 'returnAsset'])->name('borrowing.return_user');

    // ====================================================
    // GROUP: DYNAMIC PERMISSION ROUTES (Granular Access)
    // ====================================================
    // Note: Middleware Permissions check is handled inside each Controller (__construct)
    
    // 1. MANAJEMEN ASET
    Route::resource('assets', AssetController::class)->except(['index', 'show']); // Index/Show is public/auth
    Route::resource('maintenances', App\Http\Controllers\MaintenanceController::class);
    
    // 2. SIRKULASI (Admin/Staff Ops)
    // Borrowing Admin Actions
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index')->middleware('can:borrow.action'); 
    Route::post('/borrowing/{id}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve')->middleware('can:borrow.action');
    Route::post('/borrowing/{id}/reject', [BorrowingController::class, 'reject'])->name('borrowing.reject')->middleware('can:borrow.action');
    Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])->name('borrowing.return')->middleware('can:borrow.action');

    // Returns Verification
    Route::resource('returns', AssetReturnController::class)->only(['index', 'show', 'update', 'store']);
    Route::post('/returns/{return}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');

    // Impersonation Routes (Override Mode)
    Route::get('/impersonate/leave', [AuthController::class, 'leaveImpersonation'])->name('impersonate.leave');
    Route::get('/impersonate/{user}', [AuthController::class, 'impersonate'])->name('impersonate');

    // 3. LAPORAN & AUDIT
    Route::middleware(['can:report.view'])->group(function() {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    });

    // 4. ANALYTICS (Pusat Data)
    Route::get('/analytics', [App\Http\Controllers\ChartController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [App\Http\Controllers\ChartController::class, 'getData'])->name('analytics.data');
    Route::get('/analytics/detail', [App\Http\Controllers\ChartController::class, 'getDetail'])->name('analytics.detail');


    // 4. MANAJEMEN USER (Protected by user.* permissions)
    Route::resource('users', UserController::class);

});