<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\AssetRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. BUAT USER (ADMIN & KARYAWAN)
        // Password kita hash manual karena fitur auto-hash sudah kita matikan tadi
        $admin = User::create([
            'name' => 'Administrator IT',
            'email' => 'admin@vitech.asia',
            'role' => 'admin',
            'password' => Hash::make('password'), 
        ]);

        $user1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@vitech.asia',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@vitech.asia',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        // 2. BUAT DATA ASET
        // Aset 1: Available
        $macbook = Asset::create([
            'name' => 'MacBook Pro M1 2020',
            'serial_number' => 'MBP-M1-001',
            'status' => 'available',
            'description' => 'Laptop spek tinggi untuk Designer.',
            'purchase_date' => '2025-01-10',
            // 'image' => null (biarkan kosong dulu)
        ]);

        // Aset 2: Maintenance
        $dell = Asset::create([
            'name' => 'Dell XPS 13 Plus',
            'serial_number' => 'DXP-13-999',
            'status' => 'maintenance',
            'description' => 'Sedang perbaikan layar (LCD Flickering).',
            'purchase_date' => '2024-05-20',
        ]);

        // Aset 3: Deployed (Dipakai Budi)
        $lenovo = Asset::create([
            'name' => 'Lenovo ThinkPad X1',
            'serial_number' => 'LNV-TP-555',
            'status' => 'deployed',
            'description' => 'Laptop operasional harian.',
            'purchase_date' => '2024-08-15',
            'user_id' => $user1->id, // Dipakai Budi
        ]);

        // Aset 4: Broken
        Asset::create([
            'name' => 'Monitor Samsung 24 Inch',
            'serial_number' => 'MON-SAM-001',
            'status' => 'broken',
            'description' => 'Mati total kena air.',
            'purchase_date' => '2023-01-01',
        ]);

        // 3. BUAT HISTORY LOG (Agar Dashboard Admin "Aktivitas Terbaru" tidak kosong)
        AssetHistory::create([
            'asset_id' => $macbook->id,
            'user_id' => $admin->id,
            'action' => 'created',
            'notes' => 'Aset baru didaftarkan ke sistem.'
        ]);

        AssetHistory::create([
            'asset_id' => $dell->id,
            'user_id' => $admin->id,
            'action' => 'status_change',
            'notes' => 'Status berubah dari Available menjadi Maintenance.'
        ]);

        AssetHistory::create([
            'asset_id' => $lenovo->id,
            'user_id' => $admin->id,
            'action' => 'deployed',
            'notes' => 'Barang diserahkan kepada Budi Santoso.'
        ]);

        // 4. BUAT REQUEST DUMMY (Agar notifikasi request masuk terlihat)
        // Siti request pinjam MacBook
        AssetRequest::create([
            'user_id' => $user2->id,
            'asset_id' => $macbook->id,
            'request_date' => now(),
            'status' => 'pending',
            'reason' => 'Butuh untuk editing video project baru.'
        ]);
        
        // Budi request maintenance (Contoh request lama yg sudah selesai)
        AssetRequest::create([
            'user_id' => $user1->id,
            'asset_id' => $lenovo->id,
            'request_date' => now()->subDays(5),
            'status' => 'approved',
            'reason' => 'Upgrade RAM'
        ]);
    }
}