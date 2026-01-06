<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'user_id' => mt_rand(1, 5), // Acak penulis (User ID 1 sampai 5)
        'title' => $this->faker->sentence(mt_rand(3, 8)), // Judul acak 3-8 kata
        'body' => $this->faker->paragraph(mt_rand(5, 10)), // Isi acak 5-10 paragraf
    ];
}
}
