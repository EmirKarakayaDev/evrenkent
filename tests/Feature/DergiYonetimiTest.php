<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Article;
use App\Models\MagazineIssue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_duzenle_button_is_hidden_for_issues_that_cannot_be_edited(): void
    {
        $editor = $this->dergiEditoru();
        $editable = MagazineIssue::factory()->for($editor, 'editor')->create(['status' => ContentStatus::Taslak]);
        $submitted = MagazineIssue::factory()->for($editor, 'editor')->create(['status' => ContentStatus::Gonderildi]);

        $response = $this->actingAs($editor)->get(route('panel.dergi.sayilarim'))->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString(route('panel.dergi.sayilarim.duzenle', $editable), $html);
        $this->assertStringNotContainsString(route('panel.dergi.sayilarim.duzenle', $submitted), $html);
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

    public function test_editor_can_create_a_new_issue_with_a_cover_image(): void
    {
        Storage::fake('public');
        $editor = $this->dergiEditoru();

        $response = $this->actingAs($editor)->post(route('panel.dergi.sayilarim.store'), [
            'title' => 'Yeni Sayım',
            'issue_number' => 99,
            'editor_note' => 'Bu sayının yazısı.',
            'cover_image' => UploadedFile::fake()->image('kapak.jpg'),
            'publish_date' => null,
        ]);

        $this->assertDatabaseHas('magazine_issues', [
            'title' => 'Yeni Sayım',
            'issue_number' => 99,
            'editor_id' => $editor->id,
            'status' => ContentStatus::Taslak->value,
        ]);

        $issue = MagazineIssue::where('title', 'Yeni Sayım')->firstOrFail();
        $response->assertRedirect(route('panel.dergi.sayilarim.duzenle', $issue));
        Storage::disk('public')->assertExists($issue->cover_image);
    }

    public function test_editor_can_only_edit_own_issue_while_it_is_taslak_or_revizyon_istendi(): void
    {
        $editor = $this->dergiEditoru();
        $otherEditor = $this->dergiEditoru();

        $ownDraft = MagazineIssue::factory()->for($editor, 'editor')->create(['status' => ContentStatus::Taslak]);
        $ownPublished = MagazineIssue::factory()->for($editor, 'editor')->create(['status' => ContentStatus::Yayinda]);
        $othersDraft = MagazineIssue::factory()->for($otherEditor, 'editor')->create(['status' => ContentStatus::Taslak]);

        $this->actingAs($editor)->get(route('panel.dergi.sayilarim.duzenle', $ownDraft))->assertOk();
        $this->actingAs($editor)->get(route('panel.dergi.sayilarim.duzenle', $ownPublished))->assertForbidden();
        $this->actingAs($editor)->get(route('panel.dergi.sayilarim.duzenle', $othersDraft))->assertForbidden();

        $this->actingAs($editor)->put(route('panel.dergi.sayilarim.guncelle', $ownDraft), [
            'title' => 'Güncellenmiş Başlık',
            'issue_number' => $ownDraft->issue_number,
        ])->assertRedirect();

        $this->assertSame('Güncellenmiş Başlık', $ownDraft->fresh()->title);
    }

    public function test_editor_can_submit_own_draft_issue_for_approval(): void
    {
        $editor = $this->dergiEditoru();
        $issue = MagazineIssue::factory()->for($editor, 'editor')->create(['status' => ContentStatus::Taslak]);

        $this->actingAs($editor)
            ->post(route('panel.dergi.sayilarim.gonder', $issue))
            ->assertRedirect();

        $this->assertSame(ContentStatus::Gonderildi, $issue->fresh()->status);
        $this->assertSame(1, $issue->reviews()->count());
    }

    public function test_editor_cannot_submit_an_already_submitted_issue(): void
    {
        $editor = $this->dergiEditoru();
        $issue = MagazineIssue::factory()->for($editor, 'editor')->create(['status' => ContentStatus::Gonderildi]);

        $this->actingAs($editor)
            ->post(route('panel.dergi.sayilarim.gonder', $issue))
            ->assertForbidden();
    }

    public function test_editor_can_view_any_submitted_article(): void
    {
        $editor = $this->dergiEditoru();
        $otherEditor = $this->dergiEditoru();
        $issue = MagazineIssue::factory()->for($otherEditor, 'editor')->create();
        $article = Article::factory()->create(['magazine_issue_id' => $issue->id, 'title' => 'Görülecek Makale']);

        $this->actingAs($editor)
            ->get(route('panel.dergi.makale-havuzu.goster', $article))
            ->assertOk()
            ->assertSee('Görülecek Makale');
    }

    public function test_editor_can_review_a_submitted_article_from_their_own_issue(): void
    {
        $editor = $this->dergiEditoru();
        $issue = MagazineIssue::factory()->for($editor, 'editor')->create();
        $article = Article::factory()->create(['magazine_issue_id' => $issue->id, 'status' => ContentStatus::Gonderildi]);

        $this->actingAs($editor)
            ->post(route('panel.dergi.makale-havuzu.incele', $article))
            ->assertRedirect();

        $this->assertSame(ContentStatus::Incelemede, $article->fresh()->status);
        $this->assertSame(1, $article->reviews()->count());
    }

    public function test_editor_cannot_review_an_article_from_another_editors_issue(): void
    {
        $editor = $this->dergiEditoru();
        $otherEditor = $this->dergiEditoru();
        $issue = MagazineIssue::factory()->for($otherEditor, 'editor')->create();
        $article = Article::factory()->create(['magazine_issue_id' => $issue->id, 'status' => ContentStatus::Gonderildi]);

        $this->actingAs($editor)
            ->post(route('panel.dergi.makale-havuzu.incele', $article))
            ->assertForbidden();

        $this->assertSame(ContentStatus::Gonderildi, $article->fresh()->status);
    }

    public function test_editor_cannot_review_an_article_that_is_not_yet_submitted(): void
    {
        $editor = $this->dergiEditoru();
        $issue = MagazineIssue::factory()->for($editor, 'editor')->create();
        $article = Article::factory()->create(['magazine_issue_id' => $issue->id, 'status' => ContentStatus::Taslak]);

        $this->actingAs($editor)
            ->post(route('panel.dergi.makale-havuzu.incele', $article))
            ->assertForbidden();
    }
}
