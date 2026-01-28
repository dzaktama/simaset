<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftar Permission Granular
        $permissions = [
            // 1. Manajemen Aset
            'asset.view', 'asset.create', 'asset.edit', 'asset.delete', 'asset.export',
            // 2. Sirkulasi (Peminjaman)
            'borrow.view', 'borrow.request', 'borrow.action', 'borrow.return',
            // 3. Maintenance
            'maintenance.view', 'maintenance.create', 'maintenance.action',
            // 4. Laporan
            'report.view', 'report.export',
            // 5. Manajemen User
            'user.view', 'user.create', 'user.edit', 'user.delete',
            // 6. Monitoring & Dashboard (NEW)
            'dashboard.view', 'dashboard.stats',
            // 7. Komunikasi (NEW)
            'chat.access',
            // 8. Verifikasi Pengembalian (NEW)
            'return.verify'
        ];

        foreach ($permissions as $permission) {
            \Illuminate\Support\Facades\Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }
}
