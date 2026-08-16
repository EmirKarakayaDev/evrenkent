<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Article;
use App\Models\MagazineIssue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MagazineIssueShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_issue_page_is_visible_to_guests(): void
    {
        $issue = MagazineIssue::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Herkese Açık Sayı']);

        $this->get(route('dergiler.show', $issue))
            ->assertOk()
            ->assertSee('Herkese Açık Sayı');
    }

    public function test_draft_issue_page_returns_404_for_guests(): void
    {
        $issue = MagazineIssue::factory()->create(['status' => ContentStatus::Taslak]);

        $this->get(route('dergiler.show', $issue))->assertNotFound();
    }

    public function test_owning_editor_can_preview_their_own_draft_issue(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('dergi_editoru');
        $issue = MagazineIssue::factory()->for($editor, 'editor')->create(['status' => ContentStatus::Taslak]);

        $this->actingAs($editor)
            ->get(route('dergiler.show', $issue))
            ->assertOk();
    }

    public function test_issue_page_lists_only_its_published_articles(): void
    {
        $issue = MagazineIssue::factory()->create(['status' => ContentStatus::Yayinda]);
        Article::factory()->for($issue, 'magazineIssue')->create(['status' => ContentStatus::Yayinda, 'title' => 'Yayındaki Makale']);
        Article::factory()->for($issue, 'magazineIssue')->create(['status' => ContentStatus::Taslak, 'title' => 'Taslak Makale']);

        $this->get(route('dergiler.show', $issue))
            ->assertOk()
            ->assertSee('Yayındaki Makale')
            ->assertDontSee('Taslak Makale');
    }
}
