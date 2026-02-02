<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 1. Create Roles
        $roles = [
            'super_admin' => 'Super Admin', 
            'admin' => 'Administrator',
            'user' => 'Karyawan / User',
            'service_center' => 'Teknisi Service Center'
        ];

        foreach ($roles as $slug => $name) {
            \App\Models\Role::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        // 2. Define Permissions (Based on AppServiceProvider)
        $permissions = [
            'asset.view', 'asset.create', 'asset.edit', 'asset.delete', 'asset.export',
            'borrow.view', 'borrow.request', 'borrow.action', 'borrow.return',
            'maintenance.view', 'maintenance.create', 'maintenance.action',
            'report.view', 'report.export',
            'user.view', 'user.create', 'user.edit', 'user.delete',
            'dashboard.view', 'dashboard.stats',
            'chat.access',
            'return.verify'
        ];

        // 3. Insert Permissions & Assign to Roles
        $adminRole = \App\Models\Role::where('slug', 'admin')->first();
        $userRole = \App\Models\Role::where('slug', 'user')->first();
        $serviceRole = \App\Models\Role::where('slug', 'service_center')->first();

        foreach ($permissions as $slug) {
            $perm = \App\Models\Permission::firstOrCreate(
                ['slug' => $slug], 
                ['name' => ucwords(str_replace('.', ' ', $slug))]
            );

            // Assign All to Admin (Except super_admin who has bypass)
            $adminRole->permissions()->syncWithoutDetaching([$perm->id]);

            // Assign Specific to User
            if (in_array($slug, ['asset.view', 'borrow.request', 'borrow.view', 'dashboard.view', 'chat.access'])) {
                $userRole->permissions()->syncWithoutDetaching([$perm->id]);
            }

            // Assign Specific to Service Center
            if (in_array($slug, ['maintenance.view', 'maintenance.action', 'dashboard.view'])) {
                $serviceRole->permissions()->syncWithoutDetaching([$perm->id]);
            }
        }
    }
}
