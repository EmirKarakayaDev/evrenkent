<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\ReadingStatus;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitapligimTest extends TestCase
{
    use RefreshDatabase;

    private function okur(): User
    {
        $user = User::factory()->create();
        $user->assignRole('okur');

        return $user;
    }

    public function test_purchased_book_appears_with_purchased_badge(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Satın Alınan Kitap', 'price' => 50]);
        $user->purchases()->create([
            'book_id' => $book->id,
            'amount' => 50,
            'purchased_at' => now(),
            'payment_status' => 'completed',
        ]);

        $this->actingAs($user)
            ->get(route('panel.index'))
            ->assertOk()
            ->assertSee('Satın Alınan Kitap')
            ->assertSee('Satın Alındı');
    }

    public function test_favorited_book_appears_with_favori_badge(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Favori Kitap']);
        $user->favorites()->create([
            'favoritable_type' => Book::class,
            'favoritable_id' => $book->id,
        ]);

        $this->actingAs($user)
            ->get(route('panel.index'))
            ->assertOk()
            ->assertSee('Favori Kitap')
            ->assertSee('Favori');
    }

    public function test_reading_list_book_shows_devam_et_action(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Okunan Kitap']);
        $user->readingListItems()->create([
            'readable_type' => Book::class,
            'readable_id' => $book->id,
            'status' => ReadingStatus::Listede,
        ]);

        $this->actingAs($user)
            ->get(route('panel.index'))
            ->assertOk()
            ->assertSee('Okunan Kitap')
            ->assertSee('Okumaya Devam Et')
            ->assertSee('Listede');
    }

    public function test_unrelated_book_does_not_appear(): void
    {
        $user = $this->okur();
        Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'İlgisiz Kitap']);

        $this->actingAs($user)
            ->get(route('panel.index'))
            ->assertOk()
            ->assertDontSee('İlgisiz Kitap');
    }

    public function test_empty_state_when_no_related_books(): void
    {
        $user = $this->okur();

        $this->actingAs($user)
            ->get(route('panel.index'))
            ->assertOk()
            ->assertSee('Henüz kitaplığınıza eklenmiş bir eser yok.');
    }
}
