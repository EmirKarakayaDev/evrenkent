<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use App\Notifications\ContentApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reading_a_notification_marks_it_read_and_redirects_to_its_url(): void
    {
        $author = User::factory()->create();
        $author->assignRole('yazar');
        $book = Book::factory()->for($author, 'author')->create();
        $author->notify(new ContentApproved($book));
        $notification = $author->notifications()->first();

        $this->assertNull($notification->read_at);

        $this->actingAs($author)
            ->post(route('panel.bildirimler.oku', $notification->id))
            ->assertRedirect($notification->data['url']);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_a_user_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('yazar');
        $intruder = User::factory()->create();
        $intruder->assignRole('okur');
        $book = Book::factory()->for($owner, 'author')->create();
        $owner->notify(new ContentApproved($book));
        $notification = $owner->notifications()->first();

        $this->actingAs($intruder)
            ->post(route('panel.bildirimler.oku', $notification->id))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_read_all_marks_every_unread_notification_as_read(): void
    {
        $author = User::factory()->create();
        $author->assignRole('yazar');
        $book = Book::factory()->for($author, 'author')->create();
        $author->notify(new ContentApproved($book));
        $author->notify(new ContentApproved($book));

        $this->assertSame(2, $author->unreadNotifications()->count());

        $this->actingAs($author)
            ->post(route('panel.bildirimler.tumunu-oku'))
            ->assertRedirect();

        $this->assertSame(0, $author->fresh()->unreadNotifications()->count());
    }
}
