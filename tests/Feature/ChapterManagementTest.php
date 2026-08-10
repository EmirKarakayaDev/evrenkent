<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChapterManagementTest extends TestCase
{
    use RefreshDatabase;

    private function yazar(): User
    {
        $user = User::factory()->create();
        $user->assignRole('yazar');

        return $user;
    }

    public function test_yazar_can_add_a_chapter_to_own_draft_book(): void
    {
        $author = $this->yazar();
        $book = Book::factory()->for($author, 'author')->create(['status' => ContentStatus::Taslak]);

        $this->actingAs($author)
            ->post(route('panel.yayinlarim.kitap.bolumler.store', $book), [
                'title' => 'Giriş',
                'content' => 'Bölüm içeriği burada.',
                'order' => 1,
            ])
            ->assertRedirect(route('panel.yayinlarim.kitap.bolumler', $book));

        $this->assertDatabaseHas('chapters', [
            'book_id' => $book->id,
            'title' => 'Giriş',
            'order' => 1,
        ]);
    }

    public function test_yazar_can_edit_and_delete_own_chapter(): void
    {
        $author = $this->yazar();
        $book = Book::factory()->for($author, 'author')->create(['status' => ContentStatus::Taslak]);
        $chapter = Chapter::factory()->for($book)->create(['order' => 1, 'title' => 'Eski Başlık']);

        $this->actingAs($author)
            ->put(route('panel.yayinlarim.kitap.bolumler.guncelle', [$book, $chapter]), [
                'title' => 'Yeni Başlık',
                'content' => 'Güncellenmiş içerik.',
                'order' => 1,
            ])
            ->assertRedirect(route('panel.yayinlarim.kitap.bolumler', $book));

        $this->assertSame('Yeni Başlık', $chapter->fresh()->title);

        $this->actingAs($author)
            ->delete(route('panel.yayinlarim.kitap.bolumler.sil', [$book, $chapter]))
            ->assertRedirect(route('panel.yayinlarim.kitap.bolumler', $book));

        $this->assertDatabaseMissing('chapters', ['id' => $chapter->id]);
    }

    public function test_chapter_management_is_forbidden_once_book_is_submitted(): void
    {
        $author = $this->yazar();
        $book = Book::factory()->for($author, 'author')->create(['status' => ContentStatus::Gonderildi]);

        $this->actingAs($author)
            ->post(route('panel.yayinlarim.kitap.bolumler.store', $book), [
                'title' => 'Bölüm',
                'content' => 'İçerik',
                'order' => 1,
            ])
            ->assertForbidden();
    }

    public function test_another_yazar_cannot_manage_someone_elses_chapters(): void
    {
        $owner = $this->yazar();
        $intruder = $this->yazar();
        $book = Book::factory()->for($owner, 'author')->create(['status' => ContentStatus::Taslak]);
        $chapter = Chapter::factory()->for($book)->create(['order' => 1]);

        $this->actingAs($intruder)
            ->get(route('panel.yayinlarim.kitap.bolumler', $book))
            ->assertForbidden();

        $this->actingAs($intruder)
            ->put(route('panel.yayinlarim.kitap.bolumler.guncelle', [$book, $chapter]), [
                'title' => 'Ele geçirme',
                'content' => 'x',
                'order' => 1,
            ])
            ->assertForbidden();
    }

    public function test_duplicate_chapter_order_is_rejected(): void
    {
        $author = $this->yazar();
        $book = Book::factory()->for($author, 'author')->create(['status' => ContentStatus::Taslak]);
        Chapter::factory()->for($book)->create(['order' => 1]);

        $this->actingAs($author)
            ->post(route('panel.yayinlarim.kitap.bolumler.store', $book), [
                'title' => 'İkinci Bölüm 1',
                'content' => 'İçerik',
                'order' => 1,
            ])
            ->assertSessionHasErrors('order');
    }
}
