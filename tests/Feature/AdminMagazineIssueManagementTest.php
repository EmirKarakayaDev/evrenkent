<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\MagazineIssue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMagazineIssueManagementTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        return $user;
    }

    private function dergiEditoru(): User
    {
        $user = User::factory()->create();
        $user->assignRole('dergi_editoru');

        return $user;
    }

    public function test_only_super_admin_can_access_the_issue_list(): void
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin)->get(route('panel.adminpanel.dergiler.index'))->assertOk();

        $editor = $this->dergiEditoru();
        $this->actingAs($editor)->get(route('panel.adminpanel.dergiler.index'))->assertForbidden();
    }

    public function test_search_and_status_filter_narrow_the_list(): void
    {
        $admin = $this->superAdmin();
        MagazineIssue::factory()->create(['title' => 'Bilim Tarihi Dergisi', 'status' => ContentStatus::Yayinda]);
        MagazineIssue::factory()->create(['title' => 'Astronomi Dergisi', 'status' => ContentStatus::Taslak]);

        $response = $this->actingAs($admin)->get(route('panel.adminpanel.dergiler.index', ['q' => 'Bilim']));
        $response->assertSee('Bilim Tarihi Dergisi')->assertDontSee('Astronomi Dergisi');

        $response = $this->actingAs($admin)->get(route('panel.adminpanel.dergiler.index', ['durum' => 'taslak']));
        $response->assertSee('Astronomi Dergisi')->assertDontSee('Bilim Tarihi Dergisi');
    }

    public function test_super_admin_can_create_an_issue_with_a_cover(): void
    {
        Storage::fake('public');
        $admin = $this->superAdmin();
        $editor = $this->dergiEditoru();

        $response = $this->actingAs($admin)->post(route('panel.adminpanel.dergiler.store'), [
            'editor_id' => $editor->id,
            'title' => 'Yeni Sayı',
            'issue_number' => 12,
            'cover_image' => UploadedFile::fake()->image('kapak.jpg'),
            'status' => ContentStatus::Taslak->value,
        ]);

        $issue = MagazineIssue::where('title', 'Yeni Sayı')->firstOrFail();
        $response->assertRedirect(route('panel.adminpanel.dergiler.duzenle', $issue));
        $this->assertSame($editor->id, $issue->editor_id);
        Storage::disk('public')->assertExists($issue->cover_image);
    }

    public function test_status_cannot_be_changed_through_the_update_endpoint(): void
    {
        $admin = $this->superAdmin();
        $issue = MagazineIssue::factory()->create(['status' => ContentStatus::Onaylandi]);

        $this->actingAs($admin)->put(route('panel.adminpanel.dergiler.guncelle', $issue), [
            'editor_id' => $issue->editor_id,
            'title' => $issue->title,
            'issue_number' => $issue->issue_number,
            'status' => ContentStatus::Yayinda->value,
        ]);

        $this->assertSame(ContentStatus::Onaylandi, $issue->refresh()->status);
    }

    public function test_super_admin_can_delete_an_issue_and_its_cover(): void
    {
        Storage::fake('public');
        $admin = $this->superAdmin();
        $cover = UploadedFile::fake()->image('kapak.jpg')->store('covers/magazine-issues', 'public');
        $issue = MagazineIssue::factory()->create(['cover_image' => $cover]);

        $this->actingAs($admin)
            ->delete(route('panel.adminpanel.dergiler.sil', $issue))
            ->assertRedirect(route('panel.adminpanel.dergiler.index'));

        $this->assertModelMissing($issue);
        Storage::disk('public')->assertMissing($cover);
    }
}
