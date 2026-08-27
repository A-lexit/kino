<?php

namespace Tests\Feature\Repositories;

use App\Models\Category;
use App\Models\Film;
use App\Repositories\FilmRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
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

    public function test_get_side_best_films_returns_empty_collection_when_category_id_is_null(): void
    {
        $result = $this->repository->getSideBestFilms(null);

        $this->assertTrue($result->isEmpty());
    }


    public function test_get_side_best_films_returns_films_for_category_ordered_by_likes(): void
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        $lowLikes = Film::factory()->create([
            'category_id' => $category->id,
        ]);
        $lowLikes->state()->updateOrCreate(
            ['film_id' => $lowLikes->id],
            ['likes' => 1, 'views' => 0]
        );

        $highLikes = Film::factory()->create([
            'category_id' => $category->id,
        ]);
        $highLikes->state()->updateOrCreate(
            ['film_id' => $highLikes->id],
            ['likes' => 10, 'views' => 0]
        );

        $other = Film::factory()->create([
            'category_id' => $otherCategory->id,
        ]);
        $other->state()->updateOrCreate(
            ['film_id' => $other->id],
            ['likes' => 100, 'views' => 0]
        );

        $result = $this->repository->getSideBestFilms($category->id);

        $this->assertCount(2, $result);
        $this->assertSame($highLikes->id, $result->first()->id);
        $this->assertTrue(
            $result->every(fn (Film $film) => (int) $film->category_id === (int) $category->id)
        );
    }

    public function test_get_side_best_films_limits_to_five(): void
    {
        $category = Category::factory()->create();

        $films = Film::factory()->count(7)->create([
            'category_id' => $category->id,
        ]);

        foreach ($films as $index => $film) {
            $film->state()->create([
                'likes' => $index + 1,
                'views' => 0,
            ]);
        }

        $result = $this->repository->getSideBestFilms($category->id);

        $this->assertCount(5, $result);
    }

    public function test_get_side_featured_films_returns_only_featured(): void
    {
        Film::factory()->create(['is_featured' => 1]);
        Film::factory()->create(['is_featured' => 1]);
        Film::factory()->create(['is_featured' => 0]);

        $result = $this->repository->getSideFeaturedFilms();

        $this->assertCount(2, $result);
        $this->assertTrue(
            $result->every(fn (Film $film) => (int) $film->is_featured === 1)
        );
    }

    public function test_get_side_featured_films_limits_to_five(): void
    {
        Film::factory()->count(7)->create(['is_featured' => 1]);

        $result = $this->repository->getSideFeaturedFilms();

        $this->assertCount(5, $result);
    }

    public function test_get_side_featured_films_uses_cache_tags(): void
    {
        $expected = collect();

        $taggedCache = Mockery::mock();
        $taggedCache->shouldReceive('remember')
            ->once()
            ->withArgs(function ($key, $ttl, $callback) {
                return $key === 'featured_films' && is_callable($callback);
            })
            ->andReturn($expected);

        Cache::shouldReceive('tags')
            ->once()
            ->with(['featured_films'])
            ->andReturn($taggedCache);

        $result = $this->repository->getSideFeaturedFilms();

        $this->assertSame($expected, $result);
    }

    public function test_more_films_limit_returns_empty_when_category_id_is_null(): void
    {
        $result = $this->repository->moreFilmsLimit('matrix', null);

        $this->assertTrue($result->isEmpty());
    }

    public function test_more_films_limit_excludes_current_slug_and_filters_by_category(): void
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        $current = Film::factory()->create([
            'slug' => 'matrix',
            'category_id' => $category->id,
        ]);

        $sameCategory = Film::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        Film::factory()->count(2)->create([
            'category_id' => $otherCategory->id,
        ]);

        $result = $this->repository->moreFilmsLimit('matrix', $category->id);

        $this->assertCount(3, $result);
        $this->assertFalse($result->contains('id', $current->id));
        $this->assertTrue(
            $result->every(fn (Film $film) => (int) $film->category_id === (int) $category->id)
        );
    }

    public function test_more_films_limit_limits_to_six(): void
    {
        $category = Category::factory()->create();

        Film::factory()->create([
            'slug' => 'matrix',
            'category_id' => $category->id,
        ]);

        Film::factory()->count(10)->create([
            'category_id' => $category->id,
        ]);

        $result = $this->repository->moreFilmsLimit('matrix', $category->id);

        $this->assertCount(6, $result);
    }
}
