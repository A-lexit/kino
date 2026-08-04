<?php
namespace Tests\Feature\Admin;

use App\Models\Actor;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        /*$this->admin = User::factory()->create(['is_admin' => 1]);*/
        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    public function test_index_displays_actors_list(): void
    {
        // Оскільки маршрути в групі 'admin', спробуйте звернутися безпосередньо за іменем,
        // яке згенероване через resource:
        $response = $this->get(route('admin.actors.index'));

        $response->assertStatus(200);
    }

    public function test_store_creates_actor_and_generates_slug(): void
    {
        $data = ['name' => 'Tom Hanks'];

        $this->post(route('admin.actors.store'), $data)
            ->assertRedirect(route('admin.actors.index'))
            ->assertSessionHas('success', 'Актор доданий');

        $this->assertDatabaseHas('actors', [
            'name' => 'Tom Hanks',
            'slug' => 'tom-hanks' // Перевірка роботи slug
        ]);
    }

    public function test_update_modifies_actor_data(): void
    {
        $actor = Actor::factory()->create(['name' => 'Old Name']);

        $this->put(route('admin.actors.update', $actor->id), ['name' => 'New Name'])
            ->assertRedirect(route('admin.actors.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('actors', ['id' => $actor->id, 'name' => 'New Name']);
    }

    public function test_destroy_prevents_deletion_if_linked_to_films(): void
    {
        $actor = Actor::factory()->create();

        $film = Film::factory()->create();
        $actor->films()->attach($film->id);

        $this->delete(route('admin.actors.destroy', $actor))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('actors', [
            'id' => $actor->id,
        ]);
    }

    public function test_destroy_deletes_actor_if_no_films_present(): void
    {
        $actor = Actor::factory()->create();

        $this->delete(route('admin.actors.destroy', $actor))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('actors', [
            'id' => $actor->id,
        ]);
    }

}
