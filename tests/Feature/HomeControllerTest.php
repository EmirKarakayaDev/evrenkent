<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\MagazineIssue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_home_shows_the_newest_shelf_of_books(): void
    {
        $old = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Eski Kitap', 'published_at' => now()->subDays(10)]);
        $new = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Yeni Kitap', 'published_at' => now()]);

        $response = $this->get(route('home'))->assertOk();

        $response->assertSeeInOrder([$new->title, $old->title]);
    }

    public function test_kitaplar_stat_box_shows_the_real_total_not_just_the_shelfs_slice(): void
    {
        Book::factory()->count(9)->create(['status' => ContentStatus::Yayinda, 'published_at' => now()]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('9 eser listeleniyor');
    }

    public function test_cok_satanlar_raf_orders_books_by_purchase_count(): void
    {
        $lessPopular = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Az Satan']);
        $bestseller = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Çok Satan']);

        \App\Models\Purchase::factory()->for($bestseller)->create();
        \App\Models\Purchase::factory()->for($bestseller)->create();
        \App\Models\Purchase::factory()->for($lessPopular)->create();

        $response = $this->get(route('home', ['tur' => 'kitaplar', 'raf' => 'cok-satanlar']))->assertOk();

        $response->assertSeeInOrder([$bestseller->title, $lessPopular->title]);
    }

    public function test_editorun_seckisi_raf_only_shows_flagged_books(): void
    {
        $picked = Book::factory()->create(['status' => ContentStatus::Yayinda, 'is_editors_pick' => true, 'title' => 'Seçilmiş Kitap']);
        Book::factory()->create(['status' => ContentStatus::Yayinda, 'is_editors_pick' => false, 'title' => 'Seçilmemiş Kitap']);

        $response = $this->get(route('home', ['tur' => 'kitaplar', 'raf' => 'editorun-seckisi']))->assertOk();

        $response->assertSee('Seçilmiş Kitap')->assertDontSee('Seçilmemiş Kitap');
    }

    public function test_firsatlar_raf_only_shows_discounted_books_with_struck_through_price(): void
    {
        $onSale = Book::factory()->create([
            'status' => ContentStatus::Yayinda,
            'price' => 100,
            'discount_price' => 75,
            'title' => 'İndirimli Kitap',
        ]);
        Book::factory()->create(['status' => ContentStatus::Yayinda, 'discount_price' => null, 'title' => 'Normal Kitap']);

        $response = $this->get(route('home', ['tur' => 'kitaplar', 'raf' => 'firsatlar']))->assertOk();

        $response->assertSee('İndirimli Kitap')
            ->assertDontSee('Normal Kitap')
            ->assertSee('75,00 TL');
    }

    public function test_dergiler_tur_shows_published_magazine_issues(): void
    {
        MagazineIssue::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Yayında Sayı']);
        MagazineIssue::factory()->create(['status' => ContentStatus::Taslak, 'title' => 'Taslak Sayı']);

        $response = $this->get(route('home', ['tur' => 'dergiler']))->assertOk();

        $response->assertSee('Yayında Sayı')->assertDontSee('Taslak Sayı');
    }

    public function test_empty_shelf_shows_its_own_empty_message_instead_of_an_error(): void
    {
        $this->get(route('home', ['tur' => 'kitaplar', 'raf' => 'firsatlar']))
            ->assertOk()
            ->assertSee('Şu an indirimde bir kitap yok.');
    }
}
