<?php

namespace Database\Factories;

use App\Enums\NoteType;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => NoteType::Defter,
            'content' => fake()->paragraph(),
        ];
    }
}
