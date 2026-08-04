<?php
namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    /**
     * БЛОК 1: ТЕСТИ БЕЗПЕКИ (МАСКУВАННЯ АДМІНКИ ЧЕРЕЗ 404)
     */

    public function test_guest_cannot_access_users_routes(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
        $this->get(route('admin.users.create'))->assertRedirect(route('login'));
        $this->post(route('admin.users.store'))->assertRedirect(route('login'));
        $this->get(route('admin.users.edit', 1))->assertRedirect(route('login'));
        $this->put(route('admin.users.update', 1))->assertRedirect(route('login'));
        $this->delete(route('admin.users.destroy', 1))->assertRedirect(route('login'));
        $this->get('/admin/users/toggle/1')->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_users_routes(): void
    {
        $regularUser = User::factory()->create(['is_admin' => 0, 'is_banned' => 0]);
        $this->actingAs($regularUser);

        $this->get(route('admin.users.index'))->assertStatus(404);
        $this->get(route('admin.users.create'))->assertStatus(404);
        $this->post(route('admin.users.store'))->assertStatus(404);
        $this->get(route('admin.users.edit', 1))->assertStatus(404);
        $this->put(route('admin.users.update', 1))->assertStatus(404);
        $this->delete(route('admin.users.destroy', 1))->assertStatus(404);
        $this->get('/admin/users/toggle/1')->assertStatus(404);
    }

    /**
     * БЛОК 2: ОСНОВНИЙ ФУНКЦІОНАЛ (HAPPY PATHS)
     */

    public function test_index_displays_users_list_with_pagination(): void
    {
        $this->actingAs($this->admin);
        User::factory()->count(5)->create();

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
    }

    public function test_create_returns_view(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
    }

    public function test_store_creates_user_with_avatar_and_redirects(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $avatar = UploadedFile::fake()->image('avatar.jpg');
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'avatar' => $avatar,
            'role' => 'user',
        ];

        $response = $this->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'Користувача додано');
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_edit_returns_view_for_existing_user(): void
    {
        $this->actingAs($this->admin);
        $user = User::factory()->create();

        $response = $this->get(route('admin.users.edit', $user->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
        $response->assertViewHas('user');
    }

    public function test_update_modifies_user_data_and_redirects(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);
        $user = User::factory()->create(['name' => 'Old Name']);
        $avatar = UploadedFile::fake()->image('new_avatar.jpg');

        $response = $this->put(route('admin.users.update', $user->id), [
            'name' => 'New Name',
            'role' => 'user',
            'avatar' => $avatar
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'Зміни збережені');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name'
        ]);
    }

    public function test_toggle_changes_ban_status_and_returns_json(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'is_banned' => 0,
            'role' => \App\Enums\UserRole::User,
        ]);

        $response = $this->get(route('admin.users.toggle', $user->id));

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'is_banned' => 1,
                'message' => 'Статус користувача змінено.',
            ]);

        $this->assertEquals(1, $user->fresh()->is_banned);
    }

    public function test_destroy_deletes_user_and_returns_json(): void
    {
        $this->actingAs($this->admin);

        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::User,
        ]);

        $response = $this->delete(route('admin.users.destroy', $user->id));

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Користувача успішно видалено.',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * БЛОК 3: ВАЛІДАЦІЯ ТА ПОМИЛКИ (EDGE CASES)
     */

    public function test_update_fails_if_name_validation_rules_violated(): void
    {
        $this->actingAs($this->admin);
        $user = User::factory()->create();

        $response = $this->put(route('admin.users.update', $user->id), [
            'name' => '',
            'role' => 'user',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_methods_return_404_for_non_existent_user(): void
    {
        $this->actingAs($this->admin);

        $id = 99999;

        $this->get(route('admin.users.edit', $id))
            ->assertStatus(404);

        $this->put(route('admin.users.update', $id), [
            'name' => 'Valid Name',
            'role' => 'user',
        ])->assertStatus(404);

        $this->delete(route('admin.users.destroy', $id))
            ->assertStatus(404);

        $this->get(route('admin.users.toggle', $id))
            ->assertStatus(404);
    }

}
