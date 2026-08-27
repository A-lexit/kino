<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFilmControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_create_returns_successful_response(): void
    {
        Category::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.films.create'))
            ->assertStatus(200)
            ->assertViewHas('formData');
    }


    public function test_edit_returns_successful_response(): void
    {
        $film = Film::factory()->create(['author_id' => $this->admin->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.films.edit', $film->id))
            ->assertStatus(200)
            ->assertViewHas(['film', 'formData']);
    }

    public function test_update_modifies_film(): void
    {
        $film = Film::factory()->create(['author_id' => $this->admin->id]);

        $this->actingAs($this->admin)
            ->put(route('admin.films.update', $film->id), [
                'title' => 'Оновлений фільм',
                'slug'  => 'onovlenyi-film',
            ])
            ->assertRedirect(route('admin.films.index'))
            ->assertSessionHas('success', 'Фільм оновлено');

        $this->assertDatabaseHas('films', ['id' => $film->id, 'title' => 'Оновлений фільм']);
    }

    public function test_destroy_soft_deletes_film(): void
    {
        $film = Film::factory()->create(['author_id' => $this->admin->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.films.destroy', $film->id))
            ->assertRedirect(route('admin.films.index'))
            ->assertSessionHas('success', 'Фільм видалено');

        $this->assertSoftDeleted('films', ['id' => $film->id]);
    }

    public function test_restore_film(): void
    {
        $film = Film::factory()->create(['author_id' => $this->admin->id]);
        $film->delete();

        $this->actingAs($this->admin)
            ->patch(route('admin.films.restore', $film->id))
            ->assertRedirect(route('admin.films.index'))
            ->assertSessionHas('success', 'Фільм відновлено');

        $this->assertDatabaseHas('films', ['id' => $film->id, 'deleted_at' => null]);
    }

    public function test_restore_all_films(): void
    {
        $films = Film::factory(3)->create(['author_id' => $this->admin->id]);
        $films->each->delete();

        // restoreAll() тепер AJAX-ендпоінт — повертає JSON, не redirect
        $this->actingAs($this->admin)
            ->patch(route('admin.films.restoreAll'))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals(0, Film::onlyTrashed()->count());
    }

    public function test_force_delete_removes_film_permanently(): void
    {
        $film = Film::factory()->create(['author_id' => $this->admin->id]);
        $film->delete();

        $this->actingAs($this->admin)
            ->delete(route('admin.films.forceDelete', $film->id))
            ->assertRedirect(route('admin.films.index'))
            ->assertSessionHas('success', 'Фільм повністю видалено');

        $this->assertDatabaseMissing('films', ['id' => $film->id]);
    }

    public function test_force_delete_all_removes_all_films_permanently(): void
    {
        $films = Film::factory(3)->create(['author_id' => $this->admin->id]);
        $films->each->delete();

        // forceDeleteAll() теж AJAX-ендпоінт — повертає JSON, не redirect
        $this->actingAs($this->admin)
            ->delete(route('admin.films.forceDeleteAll'))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals(0, Film::onlyTrashed()->count());
    }

    public function test_unauthenticated_user_cannot_access_films(): void
    {
        // Тепер це СПРАВДІ гість (нема actingAs у setUp) — тест перевіряє
        // реальну поведінку EnsureStaffRole middleware для незалогіненого
        $this->get(route('admin.films.index'))
            ->assertRedirect(route('login'));
    }

}
