<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    private function okur(): User
    {
        $user = User::factory()->create();
        $user->assignRole('okur');

        return $user;
    }

    public function test_defter_entry_does_not_require_a_related_content(): void
    {
        $user = $this->okur();

        $this->actingAs($user)
            ->post(route('panel.notlar.ekle'), [
                'type' => 'defter',
                'content' => 'Serbest günlük girdisi.',
            ])
            ->assertRedirect(route('panel.defterim'));

        $this->assertDatabaseHas('notes', [
            'user_id' => $user->id,
            'type' => 'defter',
            'noteable_type' => null,
        ]);
    }

    public function test_not_requires_a_noteable(): void
    {
        $user = $this->okur();

        $this->actingAs($user)
            ->post(route('panel.notlar.ekle'), [
                'type' => 'not',
                'content' => 'Bu kitap hakkında bir not.',
            ])
            ->assertSessionHasErrors(['noteable_type', 'noteable_id']);
    }

    public function test_alinti_is_created_with_noteable_and_location(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);

        $this->actingAs($user)
            ->post(route('panel.notlar.ekle'), [
                'type' => 'alinti',
                'noteable_type' => Book::class,
                'noteable_id' => $book->id,
                'location' => 'Sayfa 42',
                'content' => 'Etkileyici bir cümle.',
            ])
            ->assertRedirect(route('panel.alintilarim'));

        $this->assertDatabaseHas('notes', [
            'user_id' => $user->id,
            'type' => 'alinti',
            'noteable_type' => Book::class,
            'noteable_id' => $book->id,
            'location' => 'Sayfa 42',
        ]);
    }

    public function test_each_type_is_listed_on_its_own_page(): void
    {
        $user = $this->okur();
        $user->notes()->create(['type' => 'defter', 'content' => 'Defter içeriği benzersiz metni']);

        $this->actingAs($user)->get(route('panel.defterim'))->assertSee('Defter içeriği benzersiz metni');
        $this->actingAs($user)->get(route('panel.notlarim'))->assertDontSee('Defter içeriği benzersiz metni');
        $this->actingAs($user)->get(route('panel.alintilarim'))->assertDontSee('Defter içeriği benzersiz metni');
    }

    public function test_user_cannot_delete_another_users_note(): void
    {
        $owner = $this->okur();
        $intruder = $this->okur();
        $note = $owner->notes()->create(['type' => 'defter', 'content' => 'Gizli not']);

        $this->actingAs($intruder)
            ->delete(route('panel.notlar.sil', $note))
            ->assertForbidden();

        $this->assertDatabaseHas('notes', ['id' => $note->id]);
    }
}
