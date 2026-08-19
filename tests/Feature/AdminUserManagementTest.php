<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        return $user;
    }

    public function test_only_super_admin_can_access_the_user_list(): void
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin)->get(route('panel.adminpanel.kullanicilar.index'))->assertOk();

        $reader = User::factory()->create();
        $reader->assignRole('okur');
        $this->actingAs($reader)->get(route('panel.adminpanel.kullanicilar.index'))->assertForbidden();
    }

    public function test_search_and_role_filter_narrow_the_list(): void
    {
        $admin = $this->superAdmin();
        $author = User::factory()->create(['name' => 'Ahmet Yazar', 'email' => 'ahmet@evrenkent.test']);
        $author->assignRole('yazar');
        $editor = User::factory()->create(['name' => 'Ayşe Editör', 'email' => 'ayse@evrenkent.test']);
        $editor->assignRole('dergi_editoru');

        $response = $this->actingAs($admin)->get(route('panel.adminpanel.kullanicilar.index', ['rol' => 'yazar']));
        $response->assertSee('Ahmet Yazar')->assertDontSee('Ayşe Editör');

        $response = $this->actingAs($admin)->get(route('panel.adminpanel.kullanicilar.index', ['q' => 'ahmet@evrenkent.test']));
        $response->assertSee('Ahmet Yazar');
    }

    public function test_super_admin_can_create_a_user_with_a_role(): void
    {
        $admin = $this->superAdmin();

        $response = $this->actingAs($admin)->post(route('panel.adminpanel.kullanicilar.store'), [
            'name' => 'Yeni Yazar',
            'email' => 'yenikullanici@evrenkent.test',
            'password' => 'password123',
            'roles' => ['yazar'],
        ]);

        $user = User::where('email', 'yenikullanici@evrenkent.test')->firstOrFail();
        $response->assertRedirect(route('panel.adminpanel.kullanicilar.duzenle', $user));
        $this->assertTrue($user->hasRole('yazar'));
    }

    public function test_email_must_be_unique(): void
    {
        $admin = $this->superAdmin();
        $existing = User::factory()->create(['email' => 'var@evrenkent.test']);

        $this->actingAs($admin)
            ->post(route('panel.adminpanel.kullanicilar.store'), [
                'name' => 'Tekrar',
                'email' => 'var@evrenkent.test',
                'password' => 'password123',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_password_is_kept_when_left_blank_on_update(): void
    {
        $admin = $this->superAdmin();
        $user = User::factory()->create();
        $originalPassword = $user->password;

        $this->actingAs($admin)->put(route('panel.adminpanel.kullanicilar.guncelle', $user), [
            'name' => 'Güncellenmiş İsim',
            'email' => $user->email,
        ]);

        $this->assertSame($originalPassword, $user->refresh()->password);
        $this->assertSame('Güncellenmiş İsim', $user->name);
    }

    public function test_super_admin_can_update_a_users_roles(): void
    {
        $admin = $this->superAdmin();
        $user = User::factory()->create();
        $user->assignRole('okur');

        $this->actingAs($admin)->put(route('panel.adminpanel.kullanicilar.guncelle', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => ['yazar'],
        ]);

        $user->refresh();
        $this->assertTrue($user->hasRole('yazar'));
        $this->assertFalse($user->hasRole('okur'));
    }

    public function test_super_admin_cannot_delete_themselves(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->delete(route('panel.adminpanel.kullanicilar.sil', $admin))
            ->assertForbidden();

        $this->assertModelExists($admin);
    }

    public function test_super_admin_can_delete_another_user(): void
    {
        $admin = $this->superAdmin();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->delete(route('panel.adminpanel.kullanicilar.sil', $user))
            ->assertRedirect(route('panel.adminpanel.kullanicilar.index'));

        $this->assertModelMissing($user);
    }

    public function test_roles_overview_page_shows_real_counts(): void
    {
        $admin = $this->superAdmin();
        $author = User::factory()->create();
        $author->assignRole('yazar');

        $response = $this->actingAs($admin)->get(route('panel.adminpanel.kullanicilar.roller'))->assertOk();
        $response->assertSee('Yazar');
        $response->assertSee('Süper Admin');
    }
}
