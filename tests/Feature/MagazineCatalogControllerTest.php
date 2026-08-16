<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\MagazineIssue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MagazineCatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_lists_only_published_issues(): void
    {
        MagazineIssue::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Yayında Sayı']);
        MagazineIssue::factory()->create(['status' => ContentStatus::RevizyonIstendi, 'title' => 'Revizyondaki Sayı']);

        $this->get(route('dergiler.index'))
            ->assertOk()
            ->assertSee('Yayında Sayı')
            ->assertDontSee('Revizyondaki Sayı');
    }

    public function test_empty_catalog_shows_an_honest_empty_state(): void
    {
        $this->get(route('dergiler.index'))
            ->assertOk()
            ->assertSee('Henüz yayınlanmış bir dergi sayısı yok.');
    }
}
