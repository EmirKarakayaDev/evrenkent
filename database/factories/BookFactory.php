<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => str($title)->slug().'-'.fake()->unique()->numberBetween(1, 999999),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 20, 300),
            'status' => ContentStatus::Taslak,
        ];
    }
}
