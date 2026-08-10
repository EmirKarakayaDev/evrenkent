<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    private function okur(): User
    {
        $user = User::factory()->create();
        $user->assignRole('okur');

        return $user;
    }

    public function test_toggle_adds_and_removes_a_favorite(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);

        $this->actingAs($user)
            ->post(route('panel.favoriler.kitap.toggle', $book))
            ->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'favoritable_type' => Book::class,
            'favoritable_id' => $book->id,
        ]);

        $this->actingAs($user)
            ->post(route('panel.favoriler.kitap.toggle', $book))
            ->assertRedirect();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'favoritable_type' => Book::class,
            'favoritable_id' => $book->id,
        ]);
    }

    public function test_favorilerim_page_lists_favorited_books(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Sevilen Kitap']);
        $user->favorites()->create([
            'favoritable_type' => Book::class,
            'favoritable_id' => $book->id,
        ]);

        $this->actingAs($user)
            ->get(route('panel.favorilerim'))
            ->assertOk()
            ->assertSee('Sevilen Kitap');
    }

    public function test_user_cannot_delete_another_users_favorite(): void
    {
        $owner = $this->okur();
        $intruder = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);

        $favorite = $owner->favorites()->create([
            'favoritable_type' => Book::class,
            'favoritable_id' => $book->id,
        ]);

        $this->actingAs($intruder)
            ->delete(route('panel.favoriler.sil', $favorite))
            ->assertForbidden();

        $this->assertDatabaseHas('favorites', ['id' => $favorite->id]);
    }
}
