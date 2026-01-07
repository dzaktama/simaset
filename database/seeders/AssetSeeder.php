<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 50 data aset baru TANPA menghapus data lama
        Asset::factory()->count(50)->create();
    }
}