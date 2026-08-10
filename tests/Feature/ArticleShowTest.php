<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_article_is_visible_to_guests(): void
    {
        $article = Article::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Herkese Açık Makale']);

        $this->get(route('makaleler.show', $article))
            ->assertOk()
            ->assertSee('Herkese Açık Makale');
    }

    public function test_draft_article_returns_404_for_guests(): void
    {
        $article = Article::factory()->create(['status' => ContentStatus::Taslak]);

        $this->get(route('makaleler.show', $article))->assertNotFound();
    }

    public function test_author_can_view_own_draft_article(): void
    {
        $author = User::factory()->create();
        $author->assignRole('yazar');
        $article = Article::factory()->for($author, 'author')->create(['status' => ContentStatus::Taslak, 'title' => 'Taslak Makalem']);

        $this->actingAs($author)
            ->get(route('makaleler.show', $article))
            ->assertOk()
            ->assertSee('Taslak Makalem');
    }

    public function test_authenticated_reader_sees_note_form(): void
    {
        $user = User::factory()->create();
        $user->assignRole('okur');
        $article = Article::factory()->create(['status' => ContentStatus::Yayinda]);

        $this->actingAs($user)
            ->get(route('makaleler.show', $article))
            ->assertOk()
            ->assertSee('Not / Alıntı Ekle');
    }
}
