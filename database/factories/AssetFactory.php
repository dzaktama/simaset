<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // User ID bisa null (di gudang) atau dipegang user acak (1-5)
            'user_id' => mt_rand(0, 1) ? mt_rand(1, 5) : null,
            
            // Nama barang IT acak
            'name' => $this->faker->randomElement([
                'MacBook Pro M2', 'Dell Latitude 5420', 'ThinkPad X1 Carbon', 
                'Monitor LG 24 Inch', 'Server HP ProLiant', 'Logitech MX Master 3'
            ]),
            
            'serial_number' => strtoupper($this->faker->bothify('VITECH-####-????')),
            
            'status' => $this->faker->randomElement(['available', 'deployed', 'maintenance', 'broken']),
            
            'description' => $this->faker->sentence(10), // Deskripsi singkat
            
            'purchase_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ];
    }
}