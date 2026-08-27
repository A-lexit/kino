<?php

namespace Tests\Feature\Api;

use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_show_a_film_by_slug()
    {
        $film = Film::factory()->create([
            'slug' => 'matrix-1999',
        ]);

        $response = $this->getJson('/api/film-json?slug=matrix-1999');

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $film->id);
    }

    public function test_it_returns_404_if_film_not_found_in_show()
    {
        $response = $this->getJson('/api/film-json?slug=non-existing-slug');

        $response->assertStatus(404);
    }

    public function test_it_can_increment_views_and_create_state_if_missing()
    {
        $film = Film::factory()->create(['slug' => 'inception']);

        if ($film->state) {
            $film->state()->delete();
            $film->unsetRelation('state');
        }

        $this->assertNull($film->state);

        $response = $this->postJson('/api/film-views-increment', [
            'slug' => 'inception'
        ]);

        $response->assertStatus(200);

        $film->refresh();
        $this->assertNotNull($film->state);
        $this->assertEquals(1, $film->state->views);
    }

    public function test_it_increments_existing_views()
    {
        $film = Film::factory()->create(['slug' => 'interstellar']);

        if (!$film->state) {
            $film->state()->create(['views' => 10]);
        } else {
            $film->state->update(['views' => 10]);
        }

        $response = $this->postJson('/api/film-views-increment', [
            'slug' => 'interstellar'
        ]);

        $response->assertStatus(200);

        $film->refresh();
        $this->assertEquals(11, $film->state->views);
    }

    public function test_it_returns_404_on_views_increment_if_film_not_found()
    {
        $response = $this->postJson('/api/film-views-increment', [
            'slug' => 'ghost-film'
        ]);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Фільм не знайдено']);
    }

    public function test_it_can_increment_likes()
    {
        $film = Film::factory()->create(['slug' => 'avatar']);

        if (!$film->state) {
            $film->state()->create(['likes' => 5]);
        } else {
            $film->state->update(['likes' => 5]);
        }

        $response = $this->postJson('/api/film-likes-increment', [
            'slug' => 'avatar',
            'increment' => true
        ]);

        $response->assertStatus(200);

        $film->refresh();
        $this->assertEquals(6, $film->state->likes);
    }

    public function test_it_can_decrement_likes()
    {
        $film = Film::factory()->create(['slug' => 'avatar-2']);

        if (!$film->state) {
            $film->state()->create(['likes' => 5]);
        } else {
            $film->state->update(['likes' => 5]);
        }

        $response = $this->postJson('/api/film-likes-increment', [
            'slug' => 'avatar-2',
            'increment' => false
        ]);

        $response->assertStatus(200);

        $film->refresh();

        $this->assertEquals(4, $film->state->likes);
    }

    public function test_it_returns_404_on_likes_increment_if_film_not_found()
    {
        $response = $this->postJson('/api/film-likes-increment', [
            'slug' => 'ghost-film',
            'increment' => true
        ]);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Фільм не знайдено']);
    }
}
