<?php
namespace Tests\Feature\Admin;

use App\Models\Caption;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCaptionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_displays_captions(): void
    {
        $this->actingAs($this->admin);
        Caption::factory()->count(3)->create();

        $this->get(route('admin.captions.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.captions.index')
            ->assertViewHas('captions');
    }

    public function test_store_saves_caption_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $data = ['title' => 'Ukrainian'];

        $this->post(route('admin.captions.store'), $data)
            ->assertRedirect(route('admin.captions.index'))
            ->assertSessionHas('success', 'Підпис додано');

        $this->assertDatabaseHas('captions', $data);
    }

    public function test_update_modifies_caption_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $caption = Caption::factory()->create(['title' => 'Old']);
        $newData = ['title' => 'New'];

        $this->put(route('admin.captions.update', $caption->id), $newData)
            ->assertRedirect(route('admin.captions.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('captions', ['id' => $caption->id, 'title' => 'New']);
    }

    public function test_destroy_deletes_caption_when_no_films_linked(): void
    {
        $this->actingAs($this->admin);
        $caption = Caption::factory()->create();

        $this->delete(route('admin.captions.destroy', $caption))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('captions', [
            'id' => $caption->id,
        ]);
    }

    public function test_destroy_prevents_deletion_if_films_linked(): void
    {
        $this->actingAs($this->admin);
        $caption = Caption::factory()->create();
        $film = Film::factory()->create();

        // Оскільки це belongsToMany, використовуємо attach
        $caption->films()->attach($film->id);


        $this->delete(route('admin.captions.destroy', $caption))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('captions', [
            'id' => $caption->id,
        ]);

        $this->assertDatabaseHas('captions', ['id' => $caption->id]);
    }

}
