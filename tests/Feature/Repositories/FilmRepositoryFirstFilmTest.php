<?php

namespace Tests\Feature\Repositories\FilmRepository;

use App\Models\Film;
use App\Repositories\FilmRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class FilmRepositoryFirstFilmTest extends TestCase
{
    use RefreshDatabase;

    private FilmRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(FilmRepository::class);
    }

    public function test_first_film_returns_film_by_slug(): void
    {
        $film = Film::factory()->create([
            'slug' => 'matrix',
        ]);

        $result = $this->repository->firstFilm('matrix');

        $this->assertInstanceOf(
            Film::class,
            $result
        );

        $this->assertEquals(
            $film->id,
            $result->id
        );
    }

    public function test_first_film_throws_exception_when_film_not_found(): void
    {
        $this->expectException(
            ModelNotFoundException::class
        );

        $this->repository->firstFilm('unknown-slug');
    }

    public function test_first_film_is_cached(): void
    {
        Cache::flush();

        $film = Film::factory()->create([
            'slug' => 'matrix',
        ]);

        $first = $this->repository->firstFilm('matrix');

        Film::whereKey($film->id)->update([
            'title' => 'Changed title',
        ]);

        $second = $this->repository->firstFilm('matrix');

        $this->assertEquals(
            $first->title,
            $second->title
        );

        $this->assertNotEquals(
            'Changed title',
            $second->title
        );
    }

    public function test_cache_contains_expected_key(): void
    {
        Cache::flush();

        Film::factory()->create([
            'slug' => 'matrix',
        ]);

        $this->repository->firstFilm('matrix');

        $this->assertTrue(
            Cache::has('film_matrix')
        );
    }
}
