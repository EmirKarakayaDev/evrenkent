<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_lists_all_published_books_not_just_a_slice(): void
    {
        Book::factory()->count(20)->create(['status' => ContentStatus::Yayinda]);
        Book::factory()->create(['status' => ContentStatus::Taslak, 'title' => 'Taslak Kitap']);

        $response = $this->get(route('kitaplar.index'))->assertOk();

        $response->assertViewHas('books', fn ($books) => $books->total() === 20);
        $response->assertDontSee('Taslak Kitap');
    }

    public function test_catalog_paginates_at_eighteen_per_page(): void
    {
        Book::factory()->count(20)->create(['status' => ContentStatus::Yayinda]);

        $response = $this->get(route('kitaplar.index'))->assertOk();

        $response->assertViewHas('books', fn ($books) => $books->count() === 18 && $books->hasMorePages());
    }

    public function test_raf_filter_switches_which_books_are_listed(): void
    {
        $picked = Book::factory()->create(['status' => ContentStatus::Yayinda, 'is_editors_pick' => true, 'title' => 'Seçilmiş']);
        Book::factory()->create(['status' => ContentStatus::Yayinda, 'is_editors_pick' => false, 'title' => 'Diğer Kitap']);

        $this->get(route('kitaplar.index', ['raf' => 'editorun-seckisi']))
            ->assertOk()
            ->assertSee('Seçilmiş')
            ->assertDontSee('Diğer Kitap');
    }

    public function test_invalid_raf_falls_back_to_yeni_cikanlar_instead_of_erroring(): void
    {
        Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Görünen Kitap']);

        $this->get(route('kitaplar.index', ['raf' => 'olmayan-bir-deger']))
            ->assertOk()
            ->assertSee('Görünen Kitap');
    }

    public function test_kategori_filter_shows_only_published_books_in_that_category(): void
    {
        $roman = Category::factory()->create(['name' => 'Roman', 'slug' => 'roman']);
        $siir = Category::factory()->create(['name' => 'Şiir', 'slug' => 'siir']);

        $romanBook = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Roman Kitabı']);
        $romanBook->categories()->attach($roman);

        $siirBook = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Şiir Kitabı']);
        $siirBook->categories()->attach($siir);

        $draftInRoman = Book::factory()->create(['status' => ContentStatus::Taslak, 'title' => 'Taslak Roman']);
        $draftInRoman->categories()->attach($roman);

        $this->get(route('kitaplar.index', ['kategori' => 'roman']))
            ->assertOk()
            ->assertSee('Roman Kitabı')
            ->assertDontSee('Şiir Kitabı')
            ->assertDontSee('Taslak Roman');
    }

    public function test_kategori_filter_with_unknown_slug_returns_404(): void
    {
        $this->get(route('kitaplar.index', ['kategori' => 'olmayan-kategori']))->assertNotFound();
    }
}
