<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        return $user;
    }

    public function test_only_super_admin_can_access_the_category_list(): void
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin)->get(route('panel.adminpanel.kategoriler.index'))->assertOk();

        $yazar = User::factory()->create();
        $yazar->assignRole('yazar');
        $this->actingAs($yazar)->get(route('panel.adminpanel.kategoriler.index'))->assertForbidden();
    }

    public function test_super_admin_can_create_a_category(): void
    {
        $admin = $this->superAdmin();

        $response = $this->actingAs($admin)->post(route('panel.adminpanel.kategoriler.store'), [
            'name' => 'Bilim Kurgu',
            'slug' => 'bilim-kurgu',
        ]);

        $response->assertRedirect(route('panel.adminpanel.kategoriler.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Bilim Kurgu', 'slug' => 'bilim-kurgu']);
    }

    public function test_slug_must_be_unique(): void
    {
        $admin = $this->superAdmin();
        Category::factory()->create(['slug' => 'roman']);

        $this->actingAs($admin)
            ->post(route('panel.adminpanel.kategoriler.store'), ['name' => 'Roman', 'slug' => 'roman'])
            ->assertSessionHasErrors('slug');
    }

    public function test_super_admin_can_update_a_category(): void
    {
        $admin = $this->superAdmin();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->put(route('panel.adminpanel.kategoriler.guncelle', $category), [
            'name' => 'Güncellenmiş Ad',
            'slug' => (string) $category->slug,
        ]);
        $response->assertSessionHasNoErrors();

        $this->assertSame('Güncellenmiş Ad', $category->refresh()->name);
    }

    public function test_super_admin_can_delete_a_category(): void
    {
        $admin = $this->superAdmin();
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->delete(route('panel.adminpanel.kategoriler.sil', $category))
            ->assertRedirect(route('panel.adminpanel.kategoriler.index'));

        $this->assertModelMissing($category);
    }
}
