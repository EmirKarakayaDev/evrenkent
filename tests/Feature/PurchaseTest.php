<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private function okur(): User
    {
        $user = User::factory()->create();
        $user->assignRole('okur');

        return $user;
    }

    public function test_an_unpublished_book_cannot_be_purchased(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Taslak]);

        $this->actingAs($user)
            ->post(route('panel.satin-al', $book))
            ->assertNotFound();

        $this->assertDatabaseMissing('purchases', ['user_id' => $user->id, 'book_id' => $book->id]);
    }

    public function test_purchasing_a_published_book_records_it_instantly(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda, 'price' => 99.90, 'title' => 'Satılan Kitap']);

        $this->actingAs($user)
            ->post(route('panel.satin-al', $book))
            ->assertRedirect();

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'payment_status' => 'completed',
        ]);

        $this->actingAs($user)
            ->get(route('panel.satin-aldiklarim'))
            ->assertOk()
            ->assertSee('Satılan Kitap');
    }

    public function test_purchasing_the_same_book_twice_is_idempotent(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);

        $this->actingAs($user)->post(route('panel.satin-al', $book));
        $this->actingAs($user)->post(route('panel.satin-al', $book));

        $this->assertSame(1, $user->purchases()->where('book_id', $book->id)->count());
    }
}
