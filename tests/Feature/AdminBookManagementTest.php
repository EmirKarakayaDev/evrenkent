<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBookManagementTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        return $user;
    }

    private function yazar(): User
    {
        $user = User::factory()->create();
        $user->assignRole('yazar');

        return $user;
    }

    public function test_only_super_admin_can_access_the_book_list(): void
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin)->get(route('panel.adminpanel.kitaplar.index'))->assertOk();

        $reader = User::factory()->create();
        $reader->assignRole('okur');
        $this->actingAs($reader)->get(route('panel.adminpanel.kitaplar.index'))->assertForbidden();
    }

    public function test_search_and_status_filter_narrow_the_list(): void
    {
        $admin = $this->superAdmin();
        Book::factory()->create(['title' => 'Kozmosun Sırları', 'status' => ContentStatus::Yayinda]);
        Book::factory()->create(['title' => 'Zamanın İzinde', 'status' => ContentStatus::Taslak]);

        $response = $this->actingAs($admin)->get(route('panel.adminpanel.kitaplar.index', ['q' => 'Kozmos']));
        $response->assertSee('Kozmosun Sırları')->assertDontSee('Zamanın İzinde');

        $response = $this->actingAs($admin)->get(route('panel.adminpanel.kitaplar.index', ['durum' => 'taslak']));
        $response->assertSee('Zamanın İzinde')->assertDontSee('Kozmosun Sırları');
    }

    public function test_super_admin_can_create_a_book_with_cover_and_categories(): void
    {
        Storage::fake('public');
        $admin = $this->superAdmin();
        $author = $this->yazar();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('panel.adminpanel.kitaplar.store'), [
            'author_id' => $author->id,
            'title' => 'Yeni Bir Kitap',
            'slug' => 'yeni-bir-kitap',
            'description' => 'Açıklama',
            'cover_image' => UploadedFile::fake()->image('kapak.jpg'),
            'price' => 49.90,
            'status' => ContentStatus::Taslak->value,
            'categories' => [$category->id],
        ]);

        $book = Book::where('slug', 'yeni-bir-kitap')->firstOrFail();
        $response->assertRedirect(route('panel.adminpanel.kitaplar.duzenle', $book));
        $this->assertSame($author->id, $book->author_id);
        $this->assertTrue($book->categories->contains($category));
        Storage::disk('public')->assertExists($book->cover_image);
    }

    public function test_status_cannot_be_changed_through_the_update_endpoint(): void
    {
        $admin = $this->superAdmin();
        $book = Book::factory()->create(['status' => ContentStatus::Onaylandi]);

        $this->actingAs($admin)->put(route('panel.adminpanel.kitaplar.guncelle', $book), [
            'author_id' => $book->author_id,
            'title' => $book->title,
            'slug' => $book->slug,
            'price' => $book->price,
            'status' => ContentStatus::Yayinda->value,
        ]);

        $this->assertSame(ContentStatus::Onaylandi, $book->refresh()->status);
    }

    public function test_cover_image_is_kept_when_not_reuploaded_on_update(): void
    {
        Storage::fake('public');
        $admin = $this->superAdmin();
        $book = Book::factory()->create(['cover_image' => 'covers/books/original.jpg']);

        $this->actingAs($admin)->put(route('panel.adminpanel.kitaplar.guncelle', $book), [
            'author_id' => $book->author_id,
            'title' => 'Güncellenmiş Başlık',
            'slug' => $book->slug,
            'price' => $book->price,
        ]);

        $this->assertSame('covers/books/original.jpg', $book->refresh()->cover_image);
        $this->assertSame('Güncellenmiş Başlık', $book->title);
    }

    public function test_super_admin_can_delete_a_book_and_its_cover(): void
    {
        Storage::fake('public');
        $admin = $this->superAdmin();
        $cover = UploadedFile::fake()->image('kapak.jpg')->store('covers/books', 'public');
        $book = Book::factory()->create(['cover_image' => $cover]);

        $this->actingAs($admin)
            ->delete(route('panel.adminpanel.kitaplar.sil', $book))
            ->assertRedirect(route('panel.adminpanel.kitaplar.index'));

        $this->assertModelMissing($book);
        Storage::disk('public')->assertMissing($cover);
    }
}
