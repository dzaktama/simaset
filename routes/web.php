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
| Web Routes (Fixed Security Logic)
|--------------------------------------------------------------------------
*/

// 1. Halaman Utama -> Redirect ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest');
    Route::get('/register', 'showRegisterForm')->name('register')->middleware('guest');
    Route::post('/register', 'register')->middleware('guest');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});

// 3. Authenticated Routes (User & Admin bisa akses ini)
Route::middleware(['auth'])->group(function () {
    
    // --- Dashboard ---
    Route::get('/home', [AssetController::class, 'dashboard'])->name('dashboard');

    // --- Chart Data (API) ---
    Route::get('/charts/asset-stats', [AssetController::class, 'chartsData'])->name('charts.assets');
    Route::get('/charts/borrow-stats', [AssetController::class, 'borrowStats'])->name('charts.borrows');

    // --- Aset (User Access limited via Controller logic or View) ---
    Route::get('/assets/map', [AssetController::class, 'locationMap'])->name('assets.map');
    Route::get('/assets/my', [AssetController::class, 'myAssets'])->name('assets.my'); 
    Route::get('/assets/{id}/scan-qr-image', [AssetController::class, 'scanQrImage'])->name('assets.scan_image');
    Route::get('/assets/scan/{asset}', [AssetController::class, 'scanQr'])->name('assets.scan');
    // Resource aset tetap ada, tapi delete/edit/create dibatasi di view/controller admin
    Route::resource('assets', AssetController::class);

    // --- PEMINJAMAN (LOGIC BARU: User Biasa) ---
    // User boleh Submit Request (Store)
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
    // User boleh Lihat History Sendiri
    Route::get('/borrowing/history', [BorrowingController::class, 'userHistory'])->name('borrowing.history');
    // User boleh Lihat Detail Peminjaman
    Route::get('/borrowing/{id}', [BorrowingController::class, 'show'])->name('borrowing.show');
    
    // --- Pengembalian (Return) ---
    // User bisa mengajukan/melihat pengembalian (tergantung implementasi controller)
    Route::resource('returns', AssetReturnController::class)->only(['index', 'show', 'update', 'store']);
    Route::post('/borrowing/{id}/return', [BorrowingController::class, 'returnAsset'])->name('borrowing.return');

    // --- ADMIN ONLY ROUTES ---
    // Semua fitur sensitif masuk ke sini agar User Nakal tidak bisa tembus lewat URL
    Route::middleware(['admin'])->group(function () {
        
        // Manajemen User
        Route::resource('users', UserController::class);

        // ... (Route admin lainnya di atas, biarkan saja) ...

    // ==========================================
    // MANAJEMEN PEMINJAMAN (ADMIN)
    // ==========================================
    
    // 1. Halaman List & Filter
    Route::get('/borrowing', [App\Http\Controllers\BorrowingController::class, 'index'])->name('borrowing.index');
    
    // 2. Halaman Detail
    Route::get('/borrowing/{id}', [App\Http\Controllers\BorrowingController::class, 'show'])->name('borrowing.show');
    
    // 3. Proses Approve
    Route::post('/borrowing/{id}/approve', [App\Http\Controllers\BorrowingController::class, 'approve'])->name('borrowing.approve');
    
    // 4. Proses Reject (INI WAJIB ADA AGAR TIDAK 404)
    Route::post('/borrowing/{id}/reject', [App\Http\Controllers\BorrowingController::class, 'reject'])->name('borrowing.reject');

    // 5. Proses Return (Admin Mengembalikan)
    Route::post('/borrowing/{id}/return', [App\Http\Controllers\BorrowingController::class, 'returnAsset'])->name('borrowing.return');
        
        // Verifikasi Pengembalian
        Route::post('/returns/{return}/verify', [AssetReturnController::class, 'verify'])->name('returns.verify');

        // Laporan
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    });

    // Blog / Post Internal
    Route::resource('posts', PostController::class);
});