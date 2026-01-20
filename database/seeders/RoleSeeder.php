<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@vitech.asia'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('super123'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Service Center
        User::updateOrCreate(
            ['email' => 'service@vitech.asia'],
            [
                'name' => 'Service Center',
                'password' => Hash::make('service123'),
                'role' => 'service_center',
                'email_verified_at' => now(),
            ]
        );

        // 3. Admin (Existing but ensuring it exists)
        User::updateOrCreate(
            ['email' => 'admin@vitech.asia'],
            [
                'name' => 'Administrator IT',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        
        // 4. User (Existing but ensuring it exists)
        User::updateOrCreate(
            ['email' => 'user@vitech.asia'],
            [
                'name' => 'Karyawan Staff',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
    }
}
