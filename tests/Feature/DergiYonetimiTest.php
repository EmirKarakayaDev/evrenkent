<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Article;
use App\Models\MagazineIssue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DergiYonetimiTest extends TestCase
{
    use RefreshDatabase;

    private function dergiEditoru(): User
    {
        $user = User::factory()->create();
        $user->assignRole('dergi_editoru');

        return $user;
    }

    public function test_dashboard_only_shows_the_editors_own_active_issue(): void
    {
        $editor = $this->dergiEditoru();
        $otherEditor = $this->dergiEditoru();

        $ownIssue = MagazineIssue::factory()->for($editor, 'editor')->create([
            'status' => ContentStatus::Taslak,
            'title' => 'Kendi Sayım',
        ]);
        MagazineIssue::factory()->for($otherEditor, 'editor')->create([
            'status' => ContentStatus::Taslak,
            'title' => 'Başka Editörün Sayısı',
        ]);

        $response = $this->actingAs($editor)->get(route('panel.dergi.index'))->assertOk();

        $response->assertSee('Kendi Sayım')->assertDontSee('Başka Editörün Sayısı');
    }

    public function test_checklist_reflects_real_data(): void
    {
        $editor = $this->dergiEditoru();
        $issue = MagazineIssue::factory()->for($editor, 'editor')->create([
            'status' => ContentStatus::Taslak,
            'cover_image' => null,
            'editor_note' => null,
        ]);

        $response = $this->actingAs($editor)->get(route('panel.dergi.index'))->assertOk();
        // Kapak ve editör yazısı boş — henüz tamamlanmamış olmalı.
        $response->assertSee('%0');

        $issue->update(['cover_image' => 'covers/magazine-issues/test.jpg', 'editor_note' => 'Bu sayının editör yazısı.']);
        Article::factory()->create(['magazine_issue_id' => $issue->id, 'status' => ContentStatus::Onaylandi]);

        $response = $this->actingAs($editor)->get(route('panel.dergi.index'))->assertOk();
        $response->assertDontSee('%0');
    }

    public function test_empty_state_when_no_active_issue(): void
    {
        $editor = $this->dergiEditoru();

        $this->actingAs($editor)
            ->get(route('panel.dergi.index'))
            ->assertOk()
            ->assertSee('Şu an hazırlanan bir sayınız yok.');
    }

    public function test_article_pool_only_shows_articles_from_the_editors_own_issues(): void
    {
        $editor = $this->dergiEditoru();
        $otherEditor = $this->dergiEditoru();

        $ownIssue = MagazineIssue::factory()->for($editor, 'editor')->create();
        $otherIssue = MagazineIssue::factory()->for($otherEditor, 'editor')->create();

        Article::factory()->create(['magazine_issue_id' => $ownIssue->id, 'title' => 'Kendi Makalem']);
        Article::factory()->create(['magazine_issue_id' => $otherIssue->id, 'title' => 'Başkasının Makalesi']);
        Article::factory()->create(['magazine_issue_id' => null, 'title' => 'Bağımsız Makale']);

        $response = $this->actingAs($editor)->get(route('panel.dergi.makale-havuzu'))->assertOk();

        $response->assertSee('Kendi Makalem')
            ->assertDontSee('Başkasının Makalesi')
            ->assertDontSee('Bağımsız Makale');
    }

    public function test_article_pool_tab_filters_by_status_group(): void
    {
        $editor = $this->dergiEditoru();
        $issue = MagazineIssue::factory()->for($editor, 'editor')->create();

        Article::factory()->create(['magazine_issue_id' => $issue->id, 'status' => ContentStatus::Incelemede, 'title' => 'İncelemedeki Makale']);
        Article::factory()->create(['magazine_issue_id' => $issue->id, 'status' => ContentStatus::RevizyonIstendi, 'title' => 'Revizyondaki Makale']);

        $response = $this->actingAs($editor)
            ->get(route('panel.dergi.makale-havuzu', ['durum' => 'incelenmeyi-bekleyen']))
            ->assertOk();

        $response->assertSee('İncelemedeki Makale')->assertDontSee('Revizyondaki Makale');
    }

    public function test_sayilarim_only_lists_own_issues_and_filters_by_status(): void
    {
        $editor = $this->dergiEditoru();
        $otherEditor = $this->dergiEditoru();

        MagazineIssue::factory()->for($editor, 'editor')->create(['status' => ContentStatus::Yayinda, 'title' => 'Yayındaki Sayım']);
        MagazineIssue::factory()->for($editor, 'editor')->create(['status' => ContentStatus::Taslak, 'title' => 'Taslak Sayım']);
        MagazineIssue::factory()->for($otherEditor, 'editor')->create(['status' => ContentStatus::Yayinda, 'title' => 'Başkasının Sayısı']);

        $response = $this->actingAs($editor)
            ->get(route('panel.dergi.sayilarim', ['durum' => 'yayinda']))
            ->assertOk();

        $response->assertSee('Yayındaki Sayım')
            ->assertDontSee('Taslak Sayım')
            ->assertDontSee('Başkasının Sayısı');
    }

    public function test_yayin_takvimi_only_lists_own_issues(): void
    {
        $editor = $this->dergiEditoru();
        $otherEditor = $this->dergiEditoru();

        MagazineIssue::factory()->for($editor, 'editor')->create(['title' => 'Benim Takvimim']);
        MagazineIssue::factory()->for($otherEditor, 'editor')->create(['title' => 'Başkasının Takvimi']);

        $response = $this->actingAs($editor)->get(route('panel.dergi.yayin-takvimi'))->assertOk();

        $response->assertSee('Benim Takvimim')->assertDontSee('Başkasının Takvimi');
    }

    public function test_yazar_and_okur_cannot_access_dergi_yonetimi_routes(): void
    {
        $yazar = User::factory()->create();
        $yazar->assignRole('yazar');

        $okur = User::factory()->create();
        $okur->assignRole('okur');

        $this->actingAs($yazar)->get(route('panel.dergi.index'))->assertForbidden();
        $this->actingAs($okur)->get(route('panel.dergi.index'))->assertForbidden();
    }

    public function test_dergi_editoru_is_redirected_to_own_dashboard_not_filament(): void
    {
        $editor = $this->dergiEditoru();

        $this->assertSame('/panel/dergi', $editor->redirectPath());
    }
}
