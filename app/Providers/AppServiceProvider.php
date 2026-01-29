<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Mendaftarkan layanan aplikasi apa pun.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap (Jalankan Awal) layanan aplikasi apa pun.
     * Di sini kita mendefinisikan aturan Hak Akses (Gates).
     */
    public function boot(): void
    {
        // ==========================================
        // DAFTAR SEMUA IZIN APLIKASI (Granular)
        // ==========================================
        // Ini adalah "Kamus Izin" yang dikenali sistem.
        // String ini harus sama persis dengan yang ada di database kolom 'permissions'.
        
        $permissions = [
            // 1. Manajemen Aset (Katalog)
            'asset.view',   // Boleh lihat daftar aset
            'asset.create', // Boleh tambah aset baru
            'asset.edit',   // Boleh edit data aset
            'asset.delete', // Boleh hapus aset
            'asset.export', // Boleh download Excel/PDF list aset

            // 2. Sirkulasi (Peminjaman & Pengembalian)
            'borrow.view',    // Boleh lihat riwayat peminjaman
            'borrow.request', // Boleh mengajukan peminjaman (biasanya User/Staff)
            'borrow.action',  // Boleh menyetujui (Approve/Reject) (biasanya Admin)
            'borrow.return',  // Boleh memproses pengembalian barang

            // 3. Maintenance (Perbaikan)
            'maintenance.view',   // Boleh lihat jadwal servis
            'maintenance.create', // Boleh lapor kerusakan (Ticket baru)
            'maintenance.action', // Boleh update status perbaikan (Teknisi)
            
            // 4. Laporan & Audit
            'report.view',   // Boleh buka menu Laporan
            'report.export', // Boleh download laporan lengkap

            // 5. Manajemen User (Pengguna)
            'user.view',   // Boleh lihat daftar user
            'user.create', // Boleh tambah user baru
            'user.edit',   // Boleh edit data user
            'user.delete', // Boleh hapus user

            // 6. Monitoring & Dashboard
            'dashboard.view',  // Boleh akses halaman Dashboard
            'dashboard.stats', // Boleh lihat angka statistik sensitif

            // 7. Komunikasi
            'chat.access', // Boleh akses fitur Chat

            // 8. Verifikasi Pengembalian
            'return.verify' // Khusus pengecekan kondisi barang saat kembali
        ];

        // ==========================================
        // REGISTRASI GATE OTOMATIS
        // ==========================================
        // Loop ini mendaftarkan setiap izin ke sistem Laravel Gate secara otomatis.
        // Jadi TIDAK PERLU nulis Gate::define satu per satu.
        
        foreach ($permissions as $permission) {
            \Illuminate\Support\Facades\Gate::define($permission, function ($user) use ($permission) {
                // Panggil fungsi 'hasPermission' milik Model User (App\Models\User.php)
                // Fungsi itu yang akan cek ke database apakah user punya izin ini.
                return $user->hasPermission($permission);
            });
        }
    }
}
