<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    public function definition(): array
    {
        // Daftar Alat IT yang realistis dan mapping ke kategori
        $itItems = [
            'Laptop Dell Latitude' => 'Laptop',
            'MacBook Pro M1' => 'Laptop',
            'Monitor LG 24 Inch' => 'Monitor',
            'Keyboard Mechanical Logitech' => 'Aksesoris Komputer',
            'Mouse Wireless HP' => 'Aksesoris Komputer',
            'Server Rack 4U' => 'Perangkat Jaringan',
            'Cisco Switch 24 Port' => 'Perangkat Jaringan',
            'Projector Epson' => 'Proyektor',
            'Printer Canon Pixma' => 'Printer',
            'iPad Air 5' => 'Tablet',
            'Samsung Galaxy Tab' => 'Tablet',
            'Webcam Logitech C920' => 'Aksesoris Komputer',
            'Headset Jabra Evolve' => 'Aksesoris Audio',
            'Hardisk Eksternal 1TB' => 'Penyimpanan Data',
            'Kabel HDMI 10m' => 'Kabel dan Adaptor'
        ];

        $itemName = fake()->randomElement(array_keys($itItems));
        $kategori = $itItems[$itemName];

        $statuses = ['available', 'deployed', 'maintenance', 'broken'];
        $selectedStatus = fake()->randomElement($statuses);

        // Jika status deployed, harus ada user yang pegang. Jika available/rusak, user_id null.
        $userId = ($selectedStatus === 'deployed') ? User::inRandomOrder()->first()->id ?? null : null;
        
        // Data Lokasi
        $lorongs = ['A', 'B', 'C', 'D'];
        $raks = ['01', '02', '03', '04', '05'];

        return [
            'name' => $itemName . ' - ' . fake()->numerify('##'),
            'kategori_barang' => $kategori,
            'rak' => fake()->randomElement($raks),
            'lorong' => fake()->randomElement($lorongs),
            'keterangan_lokasi' => 'Lantai ' . fake()->numberBetween(1, 4),
            'serial_number' => fake()->unique()->bothify('IT-????-#####'), // Contoh: IT-ABXY-12345
            'status' => $selectedStatus,
            'description' => fake()->sentence(10), // Deskripsi singkat
            'condition_notes' => fake()->sentence(5), // Catatan kondisi
            'purchase_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'user_id' => $userId,
            // Biarkan gambar kosong dulu biar tidak error file not found, atau pakai placeholder url
            'image' => null, 
            'image2' => null,
            'image3' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}