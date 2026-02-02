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
        
        try {
            // Ambil daftar izin dari Cache atau Database (Cache 24 Jam)
            // Menggunakan try-catch agar tidak crash saat migrasi awal (table permissions belum ada)
            $permissions = \Illuminate\Support\Facades\Cache::remember('app_permissions', 60 * 60 * 24, function () {
                return \App\Models\Permission::pluck('slug')->toArray();
            });

            foreach ($permissions as $permission) {
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
