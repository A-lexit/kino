<?php
namespace Tests\Feature\Admin;

use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGenreControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_returns_view_with_genres(): void
    {
        Genre::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.genres.index'))
            ->assertStatus(200)
            ->assertViewHas('genres');
    }

    public function test_create_returns_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.genres.create'))
            ->assertStatus(200);
    }

    public function test_store_creates_genre_and_redirects(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.genres.store'), [
                'title' => 'Action',
                'slug'  => 'action',
            ])
            ->assertRedirect(route('admin.genres.index'))
            ->assertSessionHas('success', 'Жанр додано');

        $this->assertDatabaseHas('genres', ['title' => 'Action']);
    }

    public function test_edit_returns_view_with_genre(): void
    {
        $genre = Genre::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.genres.edit', $genre->id))
            ->assertStatus(200)
            ->assertViewHas('genre');
    }

    public function test_update_modifies_genre_and_redirects(): void
    {
        $genre = Genre::factory()->create(['title' => 'Old Title']);

        $this->actingAs($this->admin)
            ->put(route('admin.genres.update', $genre->id), [
                'title' => 'New Title',
                'slug'  => 'new-title',
            ])
            ->assertRedirect(route('admin.genres.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('genres', ['id' => $genre->id, 'title' => 'New Title']);
    }

    public function test_destroy_prevents_deletion_if_films_exist(): void
    {
        $this->actingAs($this->admin);
        $genre = Genre::factory()->create();
        $film = Film::factory()->create(['author_id' => $this->admin->id]);
        $genre->films()->attach($film->id);

        $this->delete(route('admin.genres.destroy', $genre))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
        ]);
    }

    public function test_destroy_deletes_genre_if_no_films_exist(): void
    {
        $this->actingAs($this->admin);
        $genre = Genre::factory()->create();

        $this->delete(route('admin.genres.destroy', $genre))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('genres', [
            'id' => $genre->id,
        ]);
        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }

    public function test_unauthenticated_user_cannot_access_genres(): void
    {
        $this->get(route('admin.genres.index'))
            ->assertRedirect(route('login'));
    }

}
