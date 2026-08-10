<?php

namespace Database\Factories;

use App\Enums\ReadingStatus;
use App\Models\Book;
use App\Models\ReadingListItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReadingListItem>
 */
class ReadingListItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'readable_type' => Book::class,
            'readable_id' => Book::factory(),
            'status' => ReadingStatus::Listede,
        ];
    }
}
