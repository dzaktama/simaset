<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetRequestController; // <--- Pastikan ini ada

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Login (Tamu)
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
});

// Logout
Route::post('/logout', [LoginController::class, 'logout']);

// ==========================================================
// GROUP 1: USER LOGIN (ADMIN & KARYAWAN BISA AKSES)
// ==========================================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard (Controller mendeteksi role otomatis)
    Route::get('/dashboard', [AssetController::class, 'dashboard'])->name('dashboard');
    
    // Menu Karyawan: Aset Saya
    Route::get('/my-assets', [AssetController::class, 'myAssets']);

    // LOGIC REQUEST/BOOKING (KARYAWAN & ADMIN BISA)
    // PENTING: Route ini harus di sini agar Karyawan tidak kena 403 Forbidden
    Route::post('/requests', [AssetRequestController::class, 'store']); 

    // Redirect root ke dashboard jika sudah login
    Route::get('/home', function () { return redirect('/dashboard'); });
});

// ==========================================================
// GROUP 2: KHUSUS ADMINISTRATOR
// ==========================================================
Route::middleware(['auth', 'admin'])->group(function () { // Middleware 'admin' atau 'is_admin' sesuaikan dengan nama di Kernel.php Anda
    
    // Manajemen User
    Route::resource('/users', UserController::class)->except(['show']);

    // Manajemen Aset (CRUD)
    Route::resource('/assets', AssetController::class);
    
    // Laporan PDF
    Route::get('/report/assets', [AssetController::class, 'printReport'])->name('report.assets');

    // LOGIC APPROVAL (KHUSUS ADMIN)
    // Hanya admin yang boleh setujui/tolak
    Route::post('/requests/{id}/approve', [AssetRequestController::class, 'approve']);
    Route::post('/requests/{id}/reject', [AssetRequestController::class, 'reject']);
});