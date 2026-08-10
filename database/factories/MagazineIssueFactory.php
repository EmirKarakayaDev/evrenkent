<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\MagazineIssue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MagazineIssue>
 */
class MagazineIssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'editor_id' => User::factory(),
            'title' => fake()->unique()->words(3, true).' Sayısı',
            'issue_number' => fake()->unique()->numberBetween(1, 999),
            'status' => ContentStatus::Taslak,
        ];
    }
}
