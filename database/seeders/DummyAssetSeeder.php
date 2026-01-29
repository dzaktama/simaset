<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use Carbon\Carbon;

class DummyAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = ['Laptop', 'Monitor', 'Printer', 'Aksesoris', 'Furniture', 'Server', 'Networking'];
        $statuses = ['available', 'maintenance', 'broken']; // deployed handled separately logic wise
        
        $faker = \Faker\Factory::create('id_ID');

        for ($i = 1; $i <= 50; $i++) {
            $category = $faker->randomElement($categories);
            $startDate = Carbon::now()->subMonths(rand(1, 24));
            
            // Format SN: CAT-000XX
            $prefix = strtoupper(substr($category, 0, 3));
            $sn = $prefix . '-' . str_pad($i + 1000, 5, '0', STR_PAD_LEFT); // Ensure uniqueness

            $asset = Asset::create([
                'name' => $this->generateAssetName($category, $faker),
                'serial_number' => $sn,
                'category' => $category,
                'status' => 'available', // Default available
                'description' => $faker->sentence(10),
                'quantity' => 1,
                
                // Lokasi
                'lorong' => 'Area ' . $faker->randomElement(['A', 'B', 'C', 'D']),
                'rak' => 'R-' . str_pad($faker->numberBetween(1, 50), 2, '0', STR_PAD_LEFT),
                'location' => 'Gudang', 

                // Finansial
                'purchase_date' => $startDate,
                'purchase_price' => $faker->numberBetween(1000000, 25000000),
                'useful_life_years' => 4,
                'residual_value' => $faker->numberBetween(100000, 1000000),
                
                // Gambar 
                'image' => null,
            ]);

            // [LOGGING] Catat aktivitas seeding ke histori agar muncul di log
            // Gunakan User ID 1 (Biasanya Super Admin) sebagai aktor
            \App\Models\AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => 1, 
                'action' => 'created',
                'notes' => 'Aset didaftarkan otomatis oleh sistem (Seeder Preview).'
            ]);
        }
    }

    private function generateAssetName($category, $faker) {
        switch($category) {
            case 'Laptop': return $faker->randomElement(['MacBook Pro M1', 'Dell XPS 13', 'Lenovo ThinkPad X1', 'Asus ROG Zephyrus']);
            case 'Monitor': return $faker->randomElement(['Samsung Odyssey G5', 'LG UltraGear 27"', 'Dell Ultrasharp 24"', 'BenQ Zowie XL2546']);
            case 'Printer': return $faker->randomElement(['Epson L3110', 'HP LaserJet Pro M404n', 'Canon Pixma G3010']);
            case 'Aksesoris': return $faker->randomElement(['Logitech MX Master 3', 'Keychron K2 V2', 'Mouse Wireless Murah']);
            case 'Furniture': return $faker->randomElement(['Meja Kerja Ergonomis', 'Kursi Gaming Secretlab', 'Lemari Arsip Besi']);
            case 'Server': return $faker->randomElement(['Dell PowerEdge R740', 'HP ProLiant DL380']);
            case 'Networking': return $faker->randomElement(['Cisco Catalyst 2960', 'Mikrotik RB4011', 'Ubiquiti Unifi AP AC Pro']);
            default: return 'Barang Umum';
        }
    }
}
