<?php
namespace Tests\Feature\APIs;

use App\APIs\OmdbService;
use App\Models\Film;
use App\Models\Year;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OmdbServiceTest extends TestCase
{
    private function makeFilm(): Film
    {
        $film = new Film();

        $film->title = 'Avatar';
        $film->origin_title = 'Avatar';

        // щоб Laravel не ліз у БД за year
        $film->setRelation('year', null);

        return $film;
    }


    public function test_fetch_rating_returns_imdb_rating(): void
    {
        Http::fake([
            'https://www.omdbapi.com/*' => Http::response([
                'Response' => 'True',
                'imdbID' => 'tt1234567',
                'imdbRating' => '8.5',
            ], 200),
        ]);

        $film = $this->makeFilm();

        $result = app(OmdbService::class)->fetchRating($film);

        $this->assertEquals([
            'imdb_id' => 'tt1234567',
            'imdb_rating' => 8.5,
        ], $result);
    }


    public function test_fetch_rating_uses_fallback_without_year(): void
    {
        $film = $this->makeFilm();

        $year = new Year([
            'title' => 2020,
        ]);

        $film->setRelation('year', $year);

        Http::fakeSequence()
            ->push([
                'Response' => 'False',
            ])
            ->push([
                'Response' => 'True',
                'imdbID' => 'tt9999999',
                'imdbRating' => '7.9',
            ]);

        $result = app(OmdbService::class)->fetchRating($film);

        $this->assertEquals([
            'imdb_id' => 'tt9999999',
            'imdb_rating' => 7.9,
        ], $result);

        Http::assertSentCount(2);
    }


    public function test_fetch_rating_returns_null_when_movie_not_found(): void
    {
        Http::fake([
            'https://www.omdbapi.com/*' => Http::response([
                'Response' => 'False',
            ], 200),
        ]);

        $film = $this->makeFilm();

        $result = app(OmdbService::class)->fetchRating($film);

        $this->assertNull($result);
    }


    public function test_fetch_rating_returns_null_when_rating_is_na(): void
    {
        Http::fake([
            'https://www.omdbapi.com/*' => Http::response([
                'Response' => 'True',
                'imdbID' => 'tt1111111',
                'imdbRating' => 'N/A',
            ], 200),
        ]);

        $film = $this->makeFilm();

        $result = app(OmdbService::class)->fetchRating($film);

        $this->assertNull($result);
    }


    public function test_fetch_rating_returns_null_when_api_error(): void
    {
        Http::fake([
            'https://www.omdbapi.com/*' => Http::response([], 500),
        ]);

        $film = $this->makeFilm();

        $result = app(OmdbService::class)->fetchRating($film);

        $this->assertNull($result);
    }

}
