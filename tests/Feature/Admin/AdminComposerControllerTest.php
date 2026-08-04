<?php
namespace Tests\Feature\Admin;

use App\Models\Composer;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminComposerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_displays_composers(): void
    {
        $this->actingAs($this->admin);
        Composer::factory()->count(3)->create();

        $this->get(route('admin.composers.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.composers.index')
            ->assertViewHas('composers');
    }

    public function test_store_saves_composer_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $data = ['name' => 'Hans Zimmer'];

        $this->post(route('admin.composers.store'), $data)
            ->assertRedirect(route('admin.composers.index'))
            ->assertSessionHas('success', 'Композитора додано');

        $this->assertDatabaseHas('composers', $data);
    }

    public function test_update_modifies_composer_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $composer = Composer::factory()->create(['name' => 'Old Name']);
        $newData = ['name' => 'New Name'];

        $this->put(route('admin.composers.update', $composer->id), $newData)
            ->assertRedirect(route('admin.composers.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('composers', ['id' => $composer->id, 'name' => 'New Name']);
    }

    public function test_destroy_deletes_composer_when_no_films_linked(): void
    {
        $this->actingAs($this->admin);
        $composer = Composer::factory()->create();

        $this->delete(route('admin.composers.destroy', $composer))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('composers', [
            'id' => $composer->id,
        ]);
    }

    public function test_destroy_prevents_deletion_if_films_linked(): void
    {
        $this->actingAs($this->admin);
        $composer = Composer::factory()->create();
        $film = Film::factory()->create();

        $composer->films()->attach($film->id);

        $this->delete(route('admin.composers.destroy', $composer))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('composers', [
            'id' => $composer->id,
        ]);

        $this->assertDatabaseHas('composers', ['id' => $composer->id]);
    }

}
