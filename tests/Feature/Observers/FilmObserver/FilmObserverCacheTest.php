<?php
namespace Tests\Feature\Observers\FilmObserver;

use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class FilmObserverCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function fakeCache(): void
    {
        Cache::shouldReceive('forget')
            ->zeroOrMoreTimes();

        $taggedCache = Mockery::mock();

        $taggedCache
            ->shouldReceive('flush')
            ->atLeast()
            ->once();

        Cache::shouldReceive('tags')
            ->zeroOrMoreTimes()
            ->with([
                'carousel',
                'featured_films',
                'home_films',
            ])
            ->andReturn($taggedCache);
    }


    public function test_cache_is_flushed_after_film_creation(): void
    {
        $this->fakeCache();

        Film::factory()->create();
    }


    public function test_cache_is_flushed_after_film_update(): void
    {
        $film = Film::factory()->create();

        Cache::clearResolvedInstances();

        $this->fakeCache();

        $film->update([
            'title' => 'Updated title',
        ]);
    }


    public function test_cache_is_flushed_after_film_delete(): void
    {
        $film = Film::factory()->create();

        Cache::clearResolvedInstances();

        $this->fakeCache();

        $film->delete();
    }


    public function test_cache_is_flushed_after_film_restore(): void
    {
        $film = Film::factory()->create();

        $film->delete();

        Cache::clearResolvedInstances();

        $this->fakeCache();

        $film->restore();
    }


    public function test_cache_is_flushed_after_film_force_delete(): void
    {
        $film = Film::factory()->create();

        $film->delete();

        Cache::clearResolvedInstances();

        $this->fakeCache();

        $film->forceDelete();
    }

}
