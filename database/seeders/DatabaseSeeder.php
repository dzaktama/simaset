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
        // 1. BUAT USER (1 ADMIN + 9 KARYAWAN)
        $admin = User::create([
            'name' => 'Administrator IT',
            'email' => 'admin@vitech.asia',
            'role' => 'admin',
            'password' => Hash::make('admin123'), 
        ]);

        $users = [];
        $userData = [
            ['name' => 'Budi Santoso', 'email' => 'budi@vitech.asia', 'department' => 'IT', 'position' => 'Sistem Administrator'],
            ['name' => 'Siti Aminah', 'email' => 'siti@vitech.asia', 'department' => 'HRD', 'position' => 'Staff HRD'],
            ['name' => 'Rinto Wijaya', 'email' => 'rinto@vitech.asia', 'department' => 'Accounting', 'position' => 'Staff Accounting'],
            ['name' => 'Maya Kusuma', 'email' => 'maya@vitech.asia', 'department' => 'Marketing', 'position' => 'Marketing Executive'],
            ['name' => 'Ahmad Rizki', 'email' => 'ahmad@vitech.asia', 'department' => 'IT', 'position' => 'Technical Support'],
            ['name' => 'Diana Sari', 'email' => 'diana@vitech.asia', 'department' => 'Finance', 'position' => 'Finance Officer'],
            ['name' => 'Hendra Gunawan', 'email' => 'hendra@vitech.asia', 'department' => 'Operations', 'position' => 'Operations Manager'],
            ['name' => 'Lina Hermawan', 'email' => 'lina@vitech.asia', 'department' => 'Legal', 'position' => 'Legal Staff'],
            ['name' => 'Bambang Setiawan', 'email' => 'bambang@vitech.asia', 'department' => 'Logistik', 'position' => 'Logistik Coordinator'],
        ];

        foreach ($userData as $data) {
            $users[] = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'department' => $data['department'],
                'position' => $data['position'],
                'role' => 'user',
                'password' => Hash::make('user123'),
            ]);
        }

        // 2. BUAT DATA ASET KANTOR (100 ASET DUMMY)
        $assetNames = [
            // Laptop & Computer
            ['name' => 'Laptop Dell Inspiron 15', 'category' => 'Laptop'],
            ['name' => 'Laptop HP Pavilion 14', 'category' => 'Laptop'],
            ['name' => 'Laptop Lenovo IdeaPad 5', 'category' => 'Laptop'],
            ['name' => 'Laptop ASUS VivoBook 15', 'category' => 'Laptop'],
            ['name' => 'Laptop MacBook Pro 16 inci', 'category' => 'Laptop'],
            ['name' => 'Komputer Desktop HP EliteDesk', 'category' => 'Desktop'],
            ['name' => 'Komputer Desktop Lenovo ThinkCentre', 'category' => 'Desktop'],
            ['name' => 'Komputer Desktop ASUS ExpertCenter', 'category' => 'Desktop'],
            ['name' => 'PC Workstation Dell Precision', 'category' => 'Workstation'],
            ['name' => 'PC Workstation HP Z440', 'category' => 'Workstation'],
            
            // Monitor & Display
            ['name' => 'Monitor LG 24 inci Full HD', 'category' => 'Monitor'],
            ['name' => 'Monitor Dell U2415', 'category' => 'Monitor'],
            ['name' => 'Monitor ASUS PA248QV', 'category' => 'Monitor'],
            ['name' => 'Monitor Samsung S24A600', 'category' => 'Monitor'],
            ['name' => 'Monitor BenQ PD2500Q', 'category' => 'Monitor'],
            ['name' => 'Monitor LG UltraWide 34 inci', 'category' => 'Monitor'],
            ['name' => 'Monitor Dell P2423DE', 'category' => 'Monitor'],
            ['name' => 'Monitor HP Z27 4K', 'category' => 'Monitor'],
            ['name' => 'Proyektor Epson EB-X41', 'category' => 'Proyektor'],
            ['name' => 'Proyektor BenQ MH535A', 'category' => 'Proyektor'],
            
            // Printer & Scanner
            ['name' => 'Printer Canon imageRUNNER ADVANCE', 'category' => 'Printer'],
            ['name' => 'Printer HP LaserJet Enterprise M507', 'category' => 'Printer'],
            ['name' => 'Printer Xerox VersaLink C405', 'category' => 'Printer'],
            ['name' => 'Printer Brother HL-L8360CDW', 'category' => 'Printer'],
            ['name' => 'Printer Epson SureColor SC-T5200', 'category' => 'Printer'],
            ['name' => 'Scanner Canon imageFORMULA', 'category' => 'Scanner'],
            ['name' => 'Scanner Fujitsu ScanSnap iX1400', 'category' => 'Scanner'],
            ['name' => 'Scanner Xerox DocuMate 5460', 'category' => 'Scanner'],
            ['name' => 'MFP (Printer+Scanner) Ricoh MP C3004', 'category' => 'MFP'],
            ['name' => 'MFP (Printer+Scanner) Konica Minolta bizhub 287', 'category' => 'MFP'],
            
            // Keyboard & Mouse
            ['name' => 'Keyboard Mekanik Corsair K70', 'category' => 'Keyboard'],
            ['name' => 'Keyboard Wireless Logitech K840', 'category' => 'Keyboard'],
            ['name' => 'Keyboard Mechanical Ducky One 3', 'category' => 'Keyboard'],
            ['name' => 'Keyboard Standard HP', 'category' => 'Keyboard'],
            ['name' => 'Keyboard Standard Dell', 'category' => 'Keyboard'],
            ['name' => 'Mouse Gaming Razer DeathAdder', 'category' => 'Mouse'],
            ['name' => 'Mouse Wireless Logitech MX Master 3', 'category' => 'Mouse'],
            ['name' => 'Mouse Trackball Kensington Orbit', 'category' => 'Mouse'],
            ['name' => 'Mouse Optical Standard HP', 'category' => 'Mouse'],
            ['name' => 'Mouse Optical Standard Lenovo', 'category' => 'Mouse'],
            
            // Headset & Speaker
            ['name' => 'Headset Sony WH-1000XM5', 'category' => 'Headset'],
            ['name' => 'Headset Bose QuietComfort 45', 'category' => 'Headset'],
            ['name' => 'Headset Jabra Evolve 75', 'category' => 'Headset'],
            ['name' => 'Headset Plantronics Voyager 8200 UC', 'category' => 'Headset'],
            ['name' => 'Speaker JBL PartyBox 310', 'category' => 'Speaker'],
            ['name' => 'Speaker Logitech Z906', 'category' => 'Speaker'],
            ['name' => 'Speaker Portable Bose SoundLink Max', 'category' => 'Speaker'],
            ['name' => 'Speakerphone Polycom VVX 601', 'category' => 'Speaker'],
            
            // Networking Equipment
            ['name' => 'Router WiFi Cisco Meraki MR52', 'category' => 'Networking'],
            ['name' => 'Router WiFi Ubiquiti UniFi 6', 'category' => 'Networking'],
            ['name' => 'Switch PoE Netgear M4100', 'category' => 'Networking'],
            ['name' => 'Switch HPE Aruba 2930F', 'category' => 'Networking'],
            ['name' => 'Access Point Ruckus Unleashed', 'category' => 'Networking'],
            ['name' => 'Modem Fiber Huawei HG8546M', 'category' => 'Networking'],
            ['name' => 'Firewall Fortinet FortiGate 100F', 'category' => 'Networking'],
            ['name' => 'NAS Synology DS920+', 'category' => 'Networking'],
            
            // Storage & Backup
            ['name' => 'Hard Drive External Seagate 4TB', 'category' => 'Storage'],
            ['name' => 'SSD External Samsung T7 1TB', 'category' => 'Storage'],
            ['name' => 'Flash Drive Kingston DataTraveler 70', 'category' => 'Storage'],
            ['name' => 'DVD Drive USB External LG GP65NB60', 'category' => 'Storage'],
            ['name' => 'Tape Drive LTO-9 IBM', 'category' => 'Storage'],
            ['name' => 'SD Card Kingston 256GB', 'category' => 'Storage'],
            ['name' => 'Memory Card SanDisk Extreme Pro', 'category' => 'Storage'],
            
            // Office Equipment
            ['name' => 'Mesin Fotokopi Canon imageRUNNER', 'category' => 'Office Equipment'],
            ['name' => 'Mesin Fax Panasonic KX-FLM653', 'category' => 'Office Equipment'],
            ['name' => 'Laminating Machine GBC Phoenix', 'category' => 'Office Equipment'],
            ['name' => 'Penghancur Kertas (Paper Shredder) HSM', 'category' => 'Office Equipment'],
            ['name' => 'Mesin Binding Renz RCS-360', 'category' => 'Office Equipment'],
            ['name' => 'Mesin Label Brother VC-500W', 'category' => 'Office Equipment'],
            ['name' => 'Scanner Dokumen Fujitsu ScanSnap', 'category' => 'Office Equipment'],
            
            // Furniture & Accessories
            ['name' => 'Meja Kerja Ergonomis Steelcase', 'category' => 'Furniture'],
            ['name' => 'Kursi Kantor Herman Miller', 'category' => 'Furniture'],
            ['name' => 'Standing Desk Flexispot', 'category' => 'Furniture'],
            ['name' => 'Lemari Arsip Metal Bisley', 'category' => 'Furniture'],
            ['name' => 'Rak Server Rittal', 'category' => 'Furniture'],
            ['name' => 'Rack Cooling Liebert', 'category' => 'Furniture'],
            ['name' => 'Monitor Stand VESA Double Arm', 'category' => 'Accessories'],
            ['name' => 'Laptop Stand Ergonomis Rain Design', 'category' => 'Accessories'],
            ['name' => 'Kabel HDMI High Speed 10m', 'category' => 'Accessories'],
            ['name' => 'Kabel DisplayPort 2m', 'category' => 'Accessories'],
            ['name' => 'Kabel Ethernet Cat 6A 50m', 'category' => 'Accessories'],
            ['name' => 'Hub USB 3.0 7-Port Anker', 'category' => 'Accessories'],
            ['name' => 'Dock Station USB-C Satechi', 'category' => 'Accessories'],
            ['name' => 'Power Bank Aukey 100W', 'category' => 'Accessories'],
            ['name' => 'Charging Cable USB-C 3m Belkin', 'category' => 'Accessories'],
        ];

        $statuses = ['available', 'deployed', 'maintenance', 'broken'];
        $descriptions = [
            'Barang dalam kondisi sangat baik dan siap digunakan.',
            'Perangkat ini telah diperiksa dan berfungsi dengan normal.',
            'Dalam proses pemeliharaan rutin, akan selesai dalam beberapa hari.',
            'Memerlukan perbaikan atau penggantian komponen.',
            'Barang lama yang sudah diarsipkan dan jarang digunakan.',
            'Unit ini telah tersertifikasi dan siap untuk produksi.',
            'Standar peralatan kantor untuk pekerjaan sehari-hari.',
        ];

        $counter = 1;
        foreach ($assetNames as $index => $assetData) {
            $serial = strtoupper(substr($assetData['category'], 0, 3)) . '-' . str_pad($counter, 5, '0', STR_PAD_LEFT);
            
            Asset::create([
                'name' => $assetData['name'],
                'serial_number' => $serial,
                'status' => $statuses[$index % count($statuses)],
                'description' => $descriptions[$index % count($descriptions)],
                'purchase_date' => now()->subMonths(rand(1, 24)),
                'user_id' => ($index % count($statuses) == 1) ? $users[rand(0, count($users)-1)]->id : null,
            ]);
            $counter++;
        }

        // Tambah aset lagi hingga 100
        for ($i = count($assetNames); $i < 100; $i++) {
            $randomAsset = $assetNames[rand(0, count($assetNames)-1)];
            $serial = 'AST-' . str_pad($i+1, 5, '0', STR_PAD_LEFT);
            
            Asset::create([
                'name' => $randomAsset['name'] . ' (Unit ' . ($i - count($assetNames) + 2) . ')',
                'serial_number' => $serial,
                'status' => $statuses[$i % count($statuses)],
                'description' => $descriptions[$i % count($descriptions)],
                'purchase_date' => now()->subMonths(rand(1, 24)),
                'user_id' => ($i % count($statuses) == 1) ? $users[rand(0, count($users)-1)]->id : null,
            ]);
        }

        // 3. BUAT HISTORY LOG
        $assets = Asset::all();
        foreach ($assets->take(10) as $asset) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => $admin->id,
                'action' => 'created',
                'notes' => 'Aset baru didaftarkan ke sistem SIMASET.'
            ]);
        }

        // 4. BUAT REQUEST DUMMY (50 total: 30 approved, 15 rejected, 5 pending)
        
        // APPROVED REQUESTS (30) - distributed over time
        for ($i = 0; $i < 30; $i++) {
            AssetRequest::create([
                'user_id' => $users[rand(0, count($users)-1)]->id,
                'asset_id' => $assets->random()->id,
                'request_date' => now()->subHours(rand(0, 168)),
                'status' => 'approved',
                'reason' => 'Diperlukan untuk keperluan pekerjaan.',
                'created_at' => now()->subHours(rand(0, 168)),
                'updated_at' => now()->subHours(rand(0, 168)),
            ]);
        }
        
        // REJECTED REQUESTS (15) - distributed over time
        for ($i = 0; $i < 15; $i++) {
            AssetRequest::create([
                'user_id' => $users[rand(0, count($users)-1)]->id,
                'asset_id' => $assets->random()->id,
                'request_date' => now()->subHours(rand(0, 168)),
                'status' => 'rejected',
                'reason' => 'Stok tidak tersedia.',
                'created_at' => now()->subHours(rand(0, 168)),
                'updated_at' => now()->subHours(rand(0, 168)),
            ]);
        }
        
        // PENDING REQUESTS (5)
        foreach (array_slice($users, 0, 5) as $user) {
            AssetRequest::create([
                'user_id' => $user->id,
                'asset_id' => $assets->random()->id,
                'request_date' => now(),
                'status' => 'pending',
                'reason' => 'Menunggu persetujuan.'
            ]);
        }
    }
}