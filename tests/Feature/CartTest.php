<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private function okur(): User
    {
        $user = User::factory()->create();
        $user->assignRole('okur');

        return $user;
    }

    public function test_only_published_books_can_be_added_to_cart(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Taslak]);

        $this->actingAs($user)
            ->post(route('panel.sepet.kitap.ekle', $book))
            ->assertNotFound();

        $this->assertDatabaseMissing('cart_items', ['user_id' => $user->id, 'book_id' => $book->id]);
    }

    public function test_adding_the_same_book_twice_is_idempotent(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);

        $this->actingAs($user)->post(route('panel.sepet.kitap.ekle', $book));
        $this->actingAs($user)->post(route('panel.sepet.kitap.ekle', $book));

        $this->assertSame(1, $user->cartItems()->where('book_id', $book->id)->count());
    }

    public function test_already_purchased_book_cannot_be_added_to_cart(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);
        $user->purchase($book);

        $this->actingAs($user)->post(route('panel.sepet.kitap.ekle', $book));

        $this->assertDatabaseMissing('cart_items', ['user_id' => $user->id, 'book_id' => $book->id]);
    }

    public function test_user_can_remove_an_item_from_cart(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);
        $user->cartItems()->create(['book_id' => $book->id]);

        $this->actingAs($user)
            ->delete(route('panel.sepet.kitap.sil', $book))
            ->assertRedirect();

        $this->assertDatabaseMissing('cart_items', ['user_id' => $user->id, 'book_id' => $book->id]);
    }

    public function test_removing_a_book_not_in_cart_does_not_error(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);

        $this->actingAs($user)
            ->delete(route('panel.sepet.kitap.sil', $book))
            ->assertRedirect();
    }

    public function test_checkout_creates_purchases_for_all_cart_items_and_empties_cart(): void
    {
        $user = $this->okur();
        $bookOne = Book::factory()->create(['status' => ContentStatus::Yayinda, 'price' => 100]);
        $bookTwo = Book::factory()->create(['status' => ContentStatus::Yayinda, 'price' => 50]);
        $user->cartItems()->createMany([
            ['book_id' => $bookOne->id],
            ['book_id' => $bookTwo->id],
        ]);

        $this->actingAs($user)
            ->post(route('panel.sepet.checkout'))
            ->assertRedirect(route('panel.satin-aldiklarim'));

        $this->assertDatabaseHas('purchases', ['user_id' => $user->id, 'book_id' => $bookOne->id]);
        $this->assertDatabaseHas('purchases', ['user_id' => $user->id, 'book_id' => $bookTwo->id]);
        $this->assertSame(0, $user->cartItems()->count());
    }

    public function test_checkout_uses_discount_price_when_present(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda, 'price' => 100, 'discount_price' => 75]);
        $user->cartItems()->create(['book_id' => $book->id]);

        $this->actingAs($user)->post(route('panel.sepet.checkout'));

        $this->assertDatabaseHas('purchases', ['user_id' => $user->id, 'book_id' => $book->id, 'amount' => 75]);
    }

    public function test_guest_cannot_access_cart_routes(): void
    {
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);

        $this->get(route('panel.sepetim'))->assertRedirect(route('login'));
        $this->post(route('panel.sepet.kitap.ekle', $book))->assertRedirect(route('login'));
    }

    public function test_add_to_cart_returns_json_when_requested_for_the_toast_ui(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda, 'title' => 'Rüzgârın Şarkısı']);

        $response = $this->actingAs($user)
            ->postJson(route('panel.sepet.kitap.ekle', $book))
            ->assertOk();

        $response->assertJson([
            'added' => true,
            'cartCount' => 1,
            'book' => ['title' => 'Rüzgârın Şarkısı'],
        ]);
    }

    public function test_add_to_cart_json_response_reports_already_purchased_without_adding(): void
    {
        $user = $this->okur();
        $book = Book::factory()->create(['status' => ContentStatus::Yayinda]);
        $user->purchase($book);

        $this->actingAs($user)
            ->postJson(route('panel.sepet.kitap.ekle', $book))
            ->assertOk()
            ->assertJson(['added' => false]);

        $this->assertDatabaseMissing('cart_items', ['user_id' => $user->id, 'book_id' => $book->id]);
    }
}
