<?php
namespace Tests\Feature\Admin;

use App\Models\Film;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSelectionControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_returns_view_with_selections(): void
    {
        Selection::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.selections.index'))
            ->assertStatus(200)
            ->assertViewHas('selections');
    }

    public function test_create_returns_successful_response(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.selections.create'))
            ->assertStatus(200);
    }

    public function test_store_creates_selection(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.selections.store'), [
                'title' => 'Тестова підбірка',
                'slug'  => 'testova-pidbirka',
            ])
            ->assertRedirect(route('admin.selections.index'))
            ->assertSessionHas('success', 'Добірку додано');

        $this->assertDatabaseHas('selections', [
            'title' => 'Тестова підбірка',
        ]);
    }

    public function test_edit_returns_view_with_selection(): void
    {
        $selection = Selection::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.selections.edit', $selection->id))
            ->assertStatus(200)
            ->assertViewHas('selection');
    }

    public function test_update_updates_selection(): void
    {
        $selection = Selection::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('admin.selections.update', $selection->id), [
                'title' => 'Оновлена підбірка',
                'slug'  => 'onovlena-pidbirka',
            ])
            ->assertRedirect(route('admin.selections.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('selections', [
            'id' => $selection->id,
            'title' => 'Оновлена підбірка'
        ]);
    }

    public function test_destroy_deletes_selection_without_films(): void
    {
        $this->actingAs($this->admin);
        $selection = Selection::factory()->create();

        $this->delete(route('admin.selections.destroy', $selection))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('selections', ['id' => $selection->id]);
    }

    public function test_destroy_fails_when_selection_has_films(): void
    {
        $this->actingAs($this->admin);
        $selection = Selection::factory()->create();
        $film = Film::factory()->create(['author_id' => $this->admin->id]);
        $selection->films()->attach($film->id);

        $this->delete(route('admin.selections.destroy', $selection))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_unauthenticated_user_cannot_access_selections(): void
    {
        $this->get(route('admin.selections.index'))
            ->assertRedirect(route('login'));
    }

}
