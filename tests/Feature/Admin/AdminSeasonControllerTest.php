<?php
namespace Tests\Feature\Admin;

use App\Models\Film;
use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSeasonControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_returns_view_with_seasons(): void
    {
        Season::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.seasons.index'))
            ->assertStatus(200)
            ->assertViewHas('seasons');
    }

    public function test_create_returns_successful_response(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.seasons.create'))
            ->assertStatus(200);
    }

    public function test_store_creates_season(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.seasons.store'), [
                'title' => 'Тестовий сезон',
                'slug'  => 'testovyi-sezon',
            ])
            ->assertRedirect(route('admin.seasons.index'))
            ->assertSessionHas('success', 'Сезон додано');

        $this->assertDatabaseHas('seasons', ['title' => 'Тестовий сезон']);
    }

    public function test_edit_returns_view_with_season(): void
    {
        $season = Season::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.seasons.edit', $season->id))
            ->assertStatus(200)
            ->assertViewHas('season');
    }

    public function test_update_updates_season(): void
    {
        $season = Season::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('admin.seasons.update', $season->id), [
                'title' => 'Оновлений сезон',
                'slug'  => 'onovlenyi-sezon',
            ])
            ->assertRedirect(route('admin.seasons.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('seasons', [
            'id' => $season->id,
            'title' => 'Оновлений сезон'
        ]);
    }

    public function test_destroy_deletes_season_without_films(): void
    {
        $this->actingAs($this->admin);
        $season = Season::factory()->create();

        $this->delete(route('admin.seasons.destroy', $season))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('seasons', ['id' => $season->id]);
    }

    public function test_destroy_fails_when_season_has_films(): void
    {
        $this->actingAs($this->admin);
        $season = Season::factory()->create();

        Film::factory()->create([
            'author_id' => $this->admin->id,
            'season_id' => $season->id,
        ]);

        $this->delete(route('admin.seasons.destroy', $season))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_unauthenticated_user_cannot_access_seasons(): void
    {
        $this->get(route('admin.seasons.index'))
            ->assertRedirect(route('login'));
    }

}
