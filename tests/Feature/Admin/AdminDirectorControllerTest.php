<?php
namespace Tests\Feature\Admin;

use App\Models\Director;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDirectorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_displays_directors(): void
    {
        $this->actingAs($this->admin);
        Director::factory()->count(3)->create();

        $this->get(route('admin.directors.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.directors.index')
            ->assertViewHas('directors');
    }

    public function test_store_saves_director_and_redirects(): void
    {
        $this->actingAs($this->admin);

        $data = ['name' => 'Christopher Nolan'];

        $this->post(route('admin.directors.store'), $data)
            ->assertRedirect(route('admin.directors.index'))
            ->assertSessionHas('success', 'Режисера додано');

        $this->assertDatabaseHas('directors', $data);
    }

    public function test_update_modifies_director_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $director = Director::factory()->create(['name' => 'Old Name']);
        $newData = ['name' => 'New Name'];

        $this->put(route('admin.directors.update', $director->id), $newData)
            ->assertRedirect(route('admin.directors.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('directors', ['id' => $director->id, 'name' => 'New Name']);
    }

    public function test_destroy_deletes_director_when_no_films_linked(): void
    {
        $this->actingAs($this->admin);
        $director = Director::factory()->create();

        $this->delete(route('admin.directors.destroy', $director))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('directors', [
            'id' => $director->id,
        ]);

    }

    public function test_destroy_prevents_deletion_if_films_linked(): void
    {
        $this->actingAs($this->admin);
        $director = Director::factory()->create();
        $film = Film::factory()->create();

        $director->films()->attach($film->id);

        $this->delete(route('admin.directors.destroy', $director))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('directors', [
            'id' => $director->id,
        ]);

        $this->assertDatabaseHas('directors', ['id' => $director->id]);
    }

}
