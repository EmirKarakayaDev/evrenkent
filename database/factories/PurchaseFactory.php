<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'amount' => fake()->randomFloat(2, 20, 300),
            'purchased_at' => now(),
            'payment_status' => 'completed',
        ];
    }
}
