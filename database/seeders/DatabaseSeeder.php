<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Asset; // Pastikan Model Asset sudah ada
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun ADMIN UTAMA (Gunakan ini untuk Login)
        User::factory()->create([
            'name' => 'Admin Vitech',
            'email' => 'admin@vitech.asia',
            'password' => bcrypt('password'), // Passwordnya adalah: password
            'email_verified_at' => now(),
        ]);

        // 2. Buat beberapa user karyawan biasa
        User::factory(5)->create();

        // 3. Buat Data Dummy Aset (Pastikan Model Asset & Factory sudah dibuat di step sebelumnya)
        // Jika belum ada Model Asset, baris di bawah ini akan error. 
        // Asumsi: Anda sudah mengikuti instruksi pembuatan Model Asset di prompt sebelumnya.
        Asset::factory(20)->create(); 
    }
}