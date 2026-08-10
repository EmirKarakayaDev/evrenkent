<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookShowPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_book_page_is_visible_to_guests(): void
    {
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Herkese Açık Kitap']);

        $this->get(route('kitaplar.show', $book))
            ->assertOk()
            ->assertSee('Herkese Açık Kitap');
    }

    public function test_draft_book_page_returns_404(): void
    {
        $book = Book::factory()->create(['status' => ContentStatus::Taslak]);

        $this->get(route('kitaplar.show', $book))->assertNotFound();
    }

    public function test_authenticated_reader_sees_favorite_and_purchase_actions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('okur');
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);

        $this->actingAs($user)
            ->get(route('kitaplar.show', $book))
            ->assertOk()
            ->assertSee('Favorile')
            ->assertSee('Satın Al');
    }
}
