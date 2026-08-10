<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'author_id' => User::factory(),
            'magazine_issue_id' => null,
            'title' => $title,
            'slug' => str($title)->slug().'-'.fake()->unique()->numberBetween(1, 999999),
            'content' => fake()->paragraphs(3, true),
            'status' => ContentStatus::Taslak,
        ];
    }
}
