<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Penting buat hash password

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Password default untuk semua akun
        $password = Hash::make('password'); // Passwordnya: password

        // --- KELOMPOK 1: IT TEAM (OPERATOR SISTEM) ---
        
        // 1. IT Manager (Super Admin)
        User::factory()->create([
            'name' => 'Aditya Pratama (IT Manager)',
            'email' => 'admin@vitech.asia',
            'password' => $password,
        ]);

        // 2. IT Support 1 (Network)
        User::factory()->create([
            'name' => 'Budi Santoso (IT Support)',
            'email' => 'support1@vitech.asia',
            'password' => $password,
        ]);

        // 3. IT Support 2 (Hardware)
        User::factory()->create([
            'name' => 'Chandra Wijaya (IT Hardware)',
            'email' => 'support2@vitech.asia',
            'password' => $password,
        ]);

        // --- KELOMPOK 2: KARYAWAN DIVISI LAIN (ASSET HOLDERS) ---
        
        // 4. HR Manager
        User::factory()->create(['name' => 'Diana Putri (HR Manager)', 'email' => 'hr@vitech.asia', 'password' => $password]);
        
        // 5. Finance Staff
        User::factory()->create(['name' => 'Eko Prasetyo (Finance)', 'email' => 'finance@vitech.asia', 'password' => $password]);
        
        // 6. Senior Engineer
        User::factory()->create(['name' => 'Fajar Nugraha (Sr. Engineer)', 'email' => 'eng1@vitech.asia', 'password' => $password]);
        
        // 7. Junior Engineer
        User::factory()->create(['name' => 'Gita Savitri (Jr. Engineer)', 'email' => 'eng2@vitech.asia', 'password' => $password]);
        
        // 8. Marketing Lead
        User::factory()->create(['name' => 'Hendra Gunawan (Marketing)', 'email' => 'marketing@vitech.asia', 'password' => $password]);
        
        // 9. Operation Staff
        User::factory()->create(['name' => 'Indah Permata (Ops)', 'email' => 'ops@vitech.asia', 'password' => $password]);
        
        // 10. General Affair
        User::factory()->create(['name' => 'Joko Anwar (GA)', 'email' => 'ga@vitech.asia', 'password' => $password]);

        // --- BUAT ASET DUMMY ---
        // Aset akan otomatis diacak kepemilikannya ke 10 user di atas
        Asset::factory(25)->create(); 
    }
}