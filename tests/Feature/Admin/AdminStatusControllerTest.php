<?php
namespace Tests\Feature\Admin;

use App\Models\Film;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStatusControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_displays_statuses_list(): void
    {
        Status::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.statuses.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.statuses.index')
            ->assertViewHas('statuses');
    }

    public function test_create_returns_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.statuses.create'))
            ->assertStatus(200)
            ->assertViewIs('admin.statuses.create');
    }

    public function test_store_creates_status_and_redirects(): void
    {
        $data = [
            'title' => 'Новий статус',
            'slug'  => 'novii-status',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.statuses.store'), $data)
            ->assertRedirect(route('admin.statuses.index'))
            ->assertSessionHas('success', 'Статус додано');

        $this->assertDatabaseHas('statuses', $data);
    }

    public function test_edit_displays_view_with_status(): void
    {
        $status = Status::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.statuses.edit', $status->id))
            ->assertStatus(200)
            ->assertViewIs('admin.statuses.edit')
            ->assertViewHas('status', $status);
    }

    public function test_update_modifies_status_and_redirects(): void
    {
        $status = Status::factory()->create();
        $updatedData = [
            'title' => 'Оновлений статус',
            'slug'  => 'onovlenii-status',
        ];

        $this->actingAs($this->admin)
            ->put(route('admin.statuses.update', $status->id), $updatedData)
            ->assertRedirect(route('admin.statuses.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('statuses', array_merge(['id' => $status->id], $updatedData));
    }

    public function test_destroy_deletes_status_if_it_has_no_films(): void
    {
        $this->actingAs($this->admin);
        $status = Status::factory()->create();

        $this->delete(route('admin.statuses.destroy', $status))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('statuses', ['id' => $status->id]);
    }

    public function test_destroy_does_not_delete_status_if_it_has_films(): void
    {
        $this->actingAs($this->admin);
        $status = Status::factory()->create();
        Film::factory()->create([
            'author_id' => $this->admin->id,
            'status_id' => $status->id,
        ]);

        $this->delete(route('admin.statuses.destroy', $status))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_unauthenticated_user_cannot_access_statuses(): void
    {
        $this->get(route('admin.statuses.index'))
            ->assertRedirect(route('login'));
    }

}
