<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\ReadingStatus;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingListTest extends TestCase
{
    use RefreshDatabase;

    private function okur(): User
    {
        $user = User::factory()->create();
        $user->assignRole('okur');

        return $user;
    }

    public function test_adding_a_book_puts_it_on_the_reading_list(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Okunacak Kitap']);

        $this->actingAs($user)
            ->post(route('panel.okuma-listesi.kitap.ekle', $book))
            ->assertRedirect();

        $this->actingAs($user)
            ->get(route('panel.okuma-listem'))
            ->assertOk()
            ->assertSee('Okunacak Kitap');
    }

    public function test_completing_moves_item_to_okuduklarim(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);
        $item = $user->readingListItems()->create([
            'readable_type' => Book::class,
            'readable_id' => $book->id,
            'status' => ReadingStatus::Listede,
        ]);

        $this->actingAs($user)
            ->patch(route('panel.okuma-listesi.tamamla', $item))
            ->assertRedirect();

        $item->refresh();
        $this->assertSame(ReadingStatus::Tamamlandi, $item->status);
        $this->assertNotNull($item->completed_at);

        $this->actingAs($user)->get(route('panel.okuma-listem'))->assertDontSee($book->title);
        $this->actingAs($user)->get(route('panel.okuduklarim'))->assertSee($book->title);
    }

    public function test_reopen_moves_item_back_to_okuma_listem(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);
        $item = $user->readingListItems()->create([
            'readable_type' => Book::class,
            'readable_id' => $book->id,
            'status' => ReadingStatus::Tamamlandi,
            'completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->patch(route('panel.okuma-listesi.listeye-al', $item))
            ->assertRedirect();

        $item->refresh();
        $this->assertSame(ReadingStatus::Listede, $item->status);
        $this->assertNull($item->completed_at);
    }

    public function test_user_cannot_modify_another_users_reading_list_item(): void
    {
        $owner = $this->okur();
        $intruder = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);
        $item = $owner->readingListItems()->create([
            'readable_type' => Book::class,
            'readable_id' => $book->id,
            'status' => ReadingStatus::Listede,
        ]);

        $this->actingAs($intruder)
            ->patch(route('panel.okuma-listesi.tamamla', $item))
            ->assertForbidden();

        $this->actingAs($intruder)
            ->delete(route('panel.okuma-listesi.sil', $item))
            ->assertForbidden();
    }
}
