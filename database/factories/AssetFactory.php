<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    public function definition(): array
    {
        // Daftar Alat IT yang realistis
        $itItems = [
            'Laptop Dell Latitude', 'MacBook Pro M1', 'Monitor LG 24 Inch', 
            'Keyboard Mechanical Logitech', 'Mouse Wireless HP', 'Server Rack 4U',
            'Cisco Switch 24 Port', 'Projector Epson', 'Printer Canon Pixma',
            'iPad Air 5', 'Samsung Galaxy Tab', 'Webcam Logitech C920',
            'Headset Jabra Evolve', 'Hardisk Eksternal 1TB', 'Kabel HDMI 10m'
        ];

        $statuses = ['available', 'deployed', 'maintenance', 'broken'];
        $selectedStatus = fake()->randomElement($statuses);

        // Jika status deployed, harus ada user yang pegang. Jika available/rusak, user_id null.
        $userId = ($selectedStatus === 'deployed') ? User::inRandomOrder()->first()->id ?? null : null;

        return [
            'name' => fake()->randomElement($itItems) . ' - ' . fake()->numerify('##'),
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