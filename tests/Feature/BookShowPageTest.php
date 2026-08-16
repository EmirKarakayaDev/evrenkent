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
            ->assertSee('Satın Al')
            ->assertSee('Sepete Ekle');
    }

    public function test_rating_is_shown_only_when_reviews_have_actually_been_entered(): void
    {
        $rated = Book::factory()->create([
            'status' => ContentStatus::Yayinda,
            'average_rating' => 4.5,
            'review_count' => 12,
        ]);
        $unrated = Book::factory()->create(['status' => ContentStatus::Yayinda]);

        $this->get(route('kitaplar.show', $rated))
            ->assertOk()
            ->assertSee('4.5')
            ->assertSee('12 değerlendirme');

        // Hiç değerlendirme girilmemişse sahte "0.0 (0 değerlendirme)" yazmamalı.
        $this->get(route('kitaplar.show', $unrated))
            ->assertOk()
            ->assertDontSee('değerlendirme');
    }

    public function test_content_stats_only_show_the_fields_the_author_actually_filled_in(): void
    {
        $book = Book::factory()->create([
            'status' => ContentStatus::Yayinda,
            'page_count' => 310,
            'document_count' => null,
            'video_count' => null,
        ]);

        $this->get(route('kitaplar.show', $book))
            ->assertOk()
            ->assertSee('310 sayfa')
            ->assertDontSee('belge')
            ->assertDontSee('video');
    }
}
