<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // [SECURITY] Kebijakan password standar untuk seluruh form (tambah user, edit user, ganti password).
        // Hash sudah aman (bcrypt, BCRYPT_ROUNDS=12); peringatan "sandi terekspos" dari Chrome muncul karena
        // password yang dipakai (mis. admin123) ada di daftar kebocoran publik. Aturan ini mencegah password
        // lemah/bocor dipakai lagi. uncompromised() memeriksa ke Have I Been Pwned memakai k-anonymity
        // (hanya 5 karakter awal hash SHA-1 yang dikirim, password tidak pernah keluar dari server).
        // [PRESENTASI FR-01 #8] Aturan password untuk semua form: minimal 8 karakter, huruf besar-kecil, angka,
        //     simbol, dan tidak boleh ada di daftar password bocor (uncompromised).
        Password::defaults(function () {
            $rule = Password::min(8)->letters()->mixedCase()->numbers()->symbols();

            return app()->environment('testing') ? $rule : $rule->uncompromised();
        });

        
        try {
            // Ambil daftar izin dari Cache atau Database (Cache 24 Jam)
            // Menggunakan try-catch agar tidak crash saat migrasi awal (table permissions belum ada)
            // [PRESENTASI FR-01 #6] Daftar izin dibaca dari tabel permissions lalu disimpan di cache
            //     24 jam supaya tidak query database di setiap request.
            $permissions = \Illuminate\Support\Facades\Cache::remember('app_permissions', 60 * 60 * 24, function () {
                return \App\Models\Permission::pluck('slug')->toArray();
            });

            foreach ($permissions as $permission) {
                // [PRESENTASI FR-01 #7] Setiap izin didaftarkan sebagai Gate. Inilah yang membuat
                //     can:borrow.action dan sejenisnya di route dan controller bekerja.
                \Illuminate\Support\Facades\Gate::define($permission, function ($user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }

            // [MANUAL GATE] Untuk akses menu Dashboard Admin (Overview)
            \Illuminate\Support\Facades\Gate::define('view-admin-dashboard', function ($user) {
                return in_array(optional($user->role)->slug, ['admin', 'super_admin']);
            });
        } catch (\Exception $e) {
            // Do nothing (Database belum siap saat migrasi)
        }
    }
}
