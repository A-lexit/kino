<?php
namespace Tests\Feature\Observers\FilmObserver;

use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FilmObserverSlugCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgets_cache_for_current_slug_after_update(): void
    {
        $film = Film::factory()->create([
            'slug' => 'matrix',
        ]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('film_matrix');

        Cache::shouldReceive('tags')
            ->once()
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->once();

        $film->update([
            'title' => 'Updated title',
        ]);
    }


    public function test_forgets_cache_for_old_and_new_slug_when_slug_changes(): void
    {
        $film = Film::factory()->create([
            'slug' => 'matrix',
        ]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('film_matrix');

        Cache::shouldReceive('forget')
            ->once()
            ->with('film_matrix-2');

        Cache::shouldReceive('tags')
            ->once()
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->once();

        $film->update([
            'slug' => 'matrix-2',
        ]);
    }


    public function test_does_not_forget_old_slug_when_slug_has_not_changed(): void
    {
        $film = Film::factory()->create([
            'slug' => 'matrix',
        ]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('film_matrix');

        Cache::shouldReceive('tags')
            ->once()
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->once();

        $film->update([
            'description' => 'New description',
        ]);
    }


    public function test_forgets_current_slug_when_film_is_deleted(): void
    {
        $film = Film::factory()->create([
            'slug' => 'matrix',
        ]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('film_matrix');

        Cache::shouldReceive('tags')
            ->once()
            ->andReturnSelf();

        Cache::shouldReceive('flush')
            ->once();

        $film->delete();
    }

}
