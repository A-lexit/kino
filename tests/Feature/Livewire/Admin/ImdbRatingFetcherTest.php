<?php
namespace Tests\Feature\Livewire\Admin;

use App\APIs\OmdbService;
use App\Enums\FilmStatus;
use App\Livewire\Admin\ImdbRatingFetcher;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class ImdbRatingFetcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_mounts_with_film(): void
    {
        $film = Film::factory()->create();

        Livewire::test(ImdbRatingFetcher::class, [
            'film' => $film,
        ])
            ->assertSet('film.id', $film->id)
            ->assertSet('errorMessage', null);
    }


    public function test_fetch_updates_imdb_rating(): void
    {
        $film = Film::factory()->create([
            'publish_status' => FilmStatus::Draft,
            'imdb_id' => null,
            'imdb_rating' => null,
        ]);

        $service = Mockery::mock(OmdbService::class);

        $service->shouldReceive('fetchRating')
            ->once()
            ->with(Mockery::type(Film::class))
            ->andReturn([
                'imdb_id' => 'tt0133093',
                'imdb_rating' => 8.7,
            ]);

        $this->app->instance(OmdbService::class, $service);

        Livewire::test(ImdbRatingFetcher::class, [
            'film' => $film,
        ])
            ->call('fetch');

        $film->refresh();

        $this->assertSame('tt0133093', $film->imdb_id);
        $this->assertEquals(8.7, $film->imdb_rating);
    }


    public function test_fetch_sets_error_message_when_movie_not_found(): void
    {
        $film = Film::factory()->create();

        $service = Mockery::mock(OmdbService::class);

        $service->shouldReceive('fetchRating')
            ->once()
            ->andReturn(null);

        $this->app->instance(OmdbService::class, $service);

        Livewire::test(ImdbRatingFetcher::class, [
            'film' => $film,
        ])
            ->call('fetch')
            ->assertSet(
                'errorMessage',
                'Не вдалося знайти фільм на OMDb'
            );

        $film->refresh();

        $this->assertNull($film->imdb_id);
        $this->assertNull($film->imdb_rating);
    }


    public function test_render_returns_successful_response(): void
    {
        $film = Film::factory()->create();

        Livewire::test(ImdbRatingFetcher::class, [
            'film' => $film,
        ])
            ->assertStatus(200);
    }

}
