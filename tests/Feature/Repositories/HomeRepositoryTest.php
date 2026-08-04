<?php
namespace Tests\Feature\Repositories;

use App\Enums\CategorySlug;
use App\Models\Category;
use App\Models\Film;
use App\Repositories\HomeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class HomeRepositoryTest extends TestCase
{
    use RefreshDatabase;
    private HomeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(HomeRepository::class);
    }


    public function test_returns_only_films_from_requested_category(): void
    {
        $filmsCategory = Category::factory()->create([
            'slug' => CategorySlug::FILMS->value,
        ]);

        $serialsCategory = Category::factory()->create([
            'slug' => CategorySlug::SERIALS->value,
        ]);

        Film::factory()->count(3)->create([
            'category_id' => $filmsCategory->id,
        ]);

        Film::factory()->count(2)->create([
            'category_id' => $serialsCategory->id,
        ]);

        $films = $this->repository->homeFilmsByEnum(
            CategorySlug::FILMS,
            10
        );

        $this->assertCount(3, $films);

        $this->assertTrue(
            $films->every(
                fn (Film $film) => $film->category->slug === CategorySlug::FILMS->value
            )
        );
    }


    public function test_limit_is_respected(): void
    {
        $category = Category::factory()->create([
            'slug' => CategorySlug::FILMS->value,
        ]);

        Film::factory()->count(10)->create([
            'category_id' => $category->id,
        ]);

        $films = $this->repository->homeFilmsByEnum(
            CategorySlug::FILMS,
            5
        );

        $this->assertCount(5, $films);
    }


    public function test_returns_latest_films_first(): void
    {
        $category = Category::factory()->create([
            'slug' => CategorySlug::FILMS->value,
        ]);

        $old = Film::factory()->create([
            'category_id' => $category->id,
            'created_at' => now()->subDay(),
        ]);

        $new = Film::factory()->create([
            'category_id' => $category->id,
            'created_at' => now(),
        ]);

        $films = $this->repository->homeFilmsByEnum(
            CategorySlug::FILMS,
            10
        );

        $this->assertSame($new->id, $films->first()->id);
        $this->assertSame($old->id, $films->last()->id);
    }


    public function test_uses_cache_tags_and_expected_cache_key(): void
    {
        $expected = collect();

        $taggedCache = Mockery::mock();

        $taggedCache->shouldReceive('remember')
            ->once()
            ->withArgs(function ($key, $ttl, $callback) {
                return $key === 'home_films_filmi_5'
                    && is_callable($callback);
            })
            ->andReturn($expected);

        Cache::shouldReceive('tags')
            ->once()
            ->with(['home_films'])
            ->andReturn($taggedCache);

        $result = $this->repository->homeFilmsByEnum(
            CategorySlug::FILMS,
            5
        );

        $this->assertSame($expected, $result);
    }

}
