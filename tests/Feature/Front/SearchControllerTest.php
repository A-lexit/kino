<?php
namespace Tests\Feature\Front;

use App\Enums\FilmStatus;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_displays_matching_films(): void
    {
        $film = Film::factory()->create([
            'title' => 'Avatar',
            'publish_status' => FilmStatus::Published,
        ]);

        Film::factory()->create([
            'title' => 'Titanic',
            'publish_status' => FilmStatus::Published,
        ]);

        $response = $this->get(route('search', [
            's' => 'Avatar',
        ]));

        $response->assertOk();

        $response->assertViewIs('search');

        $response->assertViewHas([
            'films',
            's',
        ]);

        $this->assertEquals('Avatar', $response->viewData('s'));

        $films = $response->viewData('films');

        $this->assertCount(1, $films);
        $this->assertEquals($film->id, $films->first()->id);
    }

    public function test_search_returns_empty_collection_when_nothing_found(): void
    {
        Film::factory()->create([
            'title' => 'Avatar',
            'publish_status' => FilmStatus::Published,
        ]);

        $response = $this->get(route('search', [
            's' => 'Harry Potter',
        ]));

        $response->assertOk();

        $films = $response->viewData('films');

        $this->assertCount(0, $films);
    }

    public function test_search_requires_search_query(): void
    {
        $response = $this->get(route('search'));

        $response->assertSessionHasErrors('s');
    }

}
