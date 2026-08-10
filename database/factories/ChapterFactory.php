<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'title' => fake()->unique()->sentence(3),
            'content' => fake()->paragraphs(5, true),
            'order' => 1,
        ];
    }
}
