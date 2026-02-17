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
| Web Routes (Cakupan Penuh & Perbaikan)
|--------------------------------------------------------------------------
*/

// 1. Arahkan Root ke Halaman Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Rute Autentikasi (Tamu/Belum Login)
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest');
    // Route::get('/register', 'showRegisterForm')->name('register')->middleware('guest');
    // Route::post('/register', 'register')->middleware('guest');
    // Route register tidak digunakan saat ini
    
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});

// 3. Rute Terautentikasi (User & Admin)
Route::middleware(['auth'])->group(function () {
    
    // ====================================================
    // GRUP A: UMUM (Bisa diakses Semua Karyawan/User)
    // ====================================================
    Route::get('/home', [AssetController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/charts/asset-stats', [AssetController::class, 'chartsData'])->name('charts.assets'); // Dipakai di dashboard
    Route::get('/charts/borrow-stats', [AssetController::class, 'borrowStats'])->name('charts.borrows');
    
    // API Search (AJAX)
    Route::get('/ajax/assets/search', [AssetController::class, 'searchJson'])->name('assets.searchJson');
    
    // Lihat Katalog & Peta (Akses Semua User)
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->where('asset', '[0-9]+')->name('assets.show');
    Route::get('/assets/map', [AssetController::class, 'locationMap'])->name('assets.map');
    Route::get('/assets/my', [AssetController::class, 'myAssets'])->name('assets.my'); 
    Route::get('/assets/{id}/scan-qr-image', [AssetController::class, 'scanQrImage'])->name('assets.scan_image');
    Route::get('/assets/scan/{asset}', [AssetController::class, 'scanQr'])->name('assets.scan');

    // ==========================================
    // GUIDES
    // GUIDES
    Route::get('guides/check-slug', [App\Http\Controllers\GuideController::class, 'checkSlug'])->name('guides.checkSlug');
    Route::resource('guides', App\Http\Controllers\GuideController::class);
    
    // ASSETSUR CHAT (Percakapan Internal)
    // ==========================================
    // 1. Pintu masuk utama. Jika user akses /chat, arahkan ke halaman chat.
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    
    // 2. Ambil data percakapan dengan user lain via AJAX (tanpa reload).
    Route::get('/chat/conversation/{userId}', [App\Http\Controllers\ChatController::class, 'getConversation'])->name('chat.get');
    
    // 3. Endpoint untuk mengirim pesan baru.
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');

    // Transaksi Peminjaman (User)
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])->name('borrowing.history');
    Route::get('/borrowing/{id}', [BorrowingController::class, 'show'])->name('borrowing.show'); // Detail Shared
    Route::post('/borrowing/{id}/return-user', [BorrowingController::class, 'returnAsset'])->name('borrowing.return_user');
    Route::post('/borrowing/{id}/cancel', [BorrowingController::class, 'cancelRequest'])->name('borrowing.cancel'); // ROUTE BARU: Cancel Request

    // ====================================================
    // GRUP: RUTE DENGAN IZIN DINAMIS (Akses Granular)
    // ====================================================
    // Catatan: Pengecekan Izin Middleware ditangani di dalam setiap Controller (__construct)
    
    // 1. MANAJEMEN ASET
    Route::post('/assets/add-stock', [AssetController::class, 'addStock'])->name('assets.addStock'); // ROUTE BARU: Tambah Stok
    Route::resource('assets', AssetController::class)->except(['index', 'show']); // Index/Show bisa diakses publik/login
    Route::resource('maintenances', App\Http\Controllers\MaintenanceController::class);

    // 1.b MANAJEMEN GUDANG (BARU)
    Route::controller(App\Http\Controllers\WarehouseController::class)->prefix('warehouse')->name('warehouse.')->group(function() {
        Route::get('/', 'index')->name('index'); // Dashboard Gudang
        Route::get('/move/{id?}', 'createMove')->name('createMove'); // Form Mutasi
        Route::post('/move', 'storeMove')->name('storeMove'); // Proses Simpan
        Route::get('/history', 'history')->name('history'); // Riwayat Perpindahan
    });
    
    // 2. SIRKULASI (Admin/Staff Operasional)
    // Aksi Admin Peminjaman
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index')->middleware('can:borrow.action'); 
    Route::post('/borrowing/{id}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve')->middleware('can:borrow.action');
    Route::put('/borrowing/{id}/extend', [BorrowingController::class, 'extendDuration'])->name('borrowing.extend')->middleware('can:borrow.action'); // ROUTE BARU
    Route::post('/borrowing/{id}/reject', [BorrowingController::class, 'reject'])->name('borrowing.reject')->middleware('can:borrow.action');
    Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])->name('borrowing.return')->middleware('can:borrow.action');

    // Verifikasi Pengembalian
    Route::resource('returns', AssetReturnController::class)->only(['index', 'show', 'update', 'store']);
    Route::post('/returns/{return}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');

    // Rute Penyamaran (Mode Override - Login sebagai user lain)
    Route::get('/impersonate/leave', [AuthController::class, 'leaveImpersonation'])->name('impersonate.leave');
    Route::get('/impersonate/{user}', [AuthController::class, 'impersonate'])->name('impersonate');

    // 3. LAPORAN & AUDIT
    Route::middleware(['can:report.view'])->group(function() {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    });

    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // 4. ANALYTICS (Pusat Data)
    Route::get('/analytics', [App\Http\Controllers\ChartController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [App\Http\Controllers\ChartController::class, 'getData'])->name('analytics.data');
    Route::get('/analytics/detail', [App\Http\Controllers\ChartController::class, 'getDetail'])->name('analytics.detail');

    // 5. MANAJEMEN USER (Dilindungi oleh izin user.*)
    Route::resource('users', UserController::class);

});