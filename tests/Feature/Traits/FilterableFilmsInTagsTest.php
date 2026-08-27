<?php

namespace Tests\Feature\Traits;

use App\Enums\FilmStatus;
use App\Models\Category;
use App\Models\Film;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class FilterableFilmsInTagsTest extends TestCase
{
    use RefreshDatabase;

    protected function createTester()
    {
        return new class {
            use FilterableFilmsInTags;

            public function filter($relation, Request $request): array
            {
                return $this->getFilteredFilmsAndCategoriesForTags(
                    $relation,
                    $request
                );
            }
        };
    }

    protected function relation()
    {
        return Film::query();
    }

    protected function createFilm(array $attributes = []): Film
    {
        return Film::factory()->create(array_merge([
            'publish_status' => FilmStatus::Published,
        ], $attributes));
    }

    public function test_returns_only_published_films(): void
    {
        $publishedFilm = $this->createFilm([
            'title' => 'Published Film',
        ]);

        Film::factory()->create([
            'title' => 'Draft Film',
            'publish_status' => FilmStatus::Draft,
        ]);

        $request = Request::create('/films');

        $result = $this->createTester()->filter(
            $this->relation(),
            $request
        );

        $this->assertCount(1, $result['films']);
        $this->assertTrue(
            $result['films']->contains($publishedFilm)
        );
    }

    public function test_filters_films_by_category_slug(): void
    {
        $actionCategory = Category::factory()->create([
            'title' => 'Бойовик',
            'slug' => 'boiovyky',
        ]);

        $dramaCategory = Category::factory()->create([
            'title' => 'Драма',
            'slug' => 'drama',
        ]);

        $actionFilm = $this->createFilm([
            'title' => 'Action Film',
            'category_id' => $actionCategory->id,
        ]);

        $this->createFilm([
            'title' => 'Drama Film',
            'category_id' => $dramaCategory->id,
        ]);

        $request = Request::create('/films', 'GET', [
            'category' => 'boiovyky',
        ]);

        $result = $this->createTester()->filter(
            $this->relation(),
            $request
        );

        $this->assertCount(1, $result['films']);
        $this->assertTrue(
            $result['films']->contains($actionFilm)
        );

        $this->assertSame(
            'Action Film',
            $result['films']->first()->title
        );
    }


public function test_returns_unique_categories_sorted_by_title(): void
{
    $zCategory = Category::factory()->create([
        'title' => 'Захоплення',
        'slug' => 'zakhoplennia',
    ]);

    $aCategory = Category::factory()->create([
        'title' => 'Анімація',
        'slug' => 'animatsiya',
    ]);

    $mCategory = Category::factory()->create([
        'title' => 'Мелодрама',
        'slug' => 'melodrama',
    ]);

    $this->createFilm([
        'title' => 'Film 1',
        'category_id' => $zCategory->id,
    ]);

    $this->createFilm([
        'title' => 'Film 2',
        'category_id' => $zCategory->id,
    ]);

    $this->createFilm([
        'title' => 'Film 3',
        'category_id' => $aCategory->id,
    ]);

    $this->createFilm([
        'title' => 'Film 4',
        'category_id' => $mCategory->id,
    ]);

    $request = Request::create('/films');

    $result = $this->createTester()->filter(
        $this->relation(),
        $request
    );

    $categories = $result['categories'];

    $this->assertCount(3, $categories);

    $this->assertSame(
        [
            'Анімація',
            'Захоплення',
            'Мелодрама',
        ],
        $categories->pluck('title')->values()->all()
    );
}






    public function test_draft_categories_are_not_included(): void
    {
        $publishedCategory = Category::factory()->create([
            'title' => 'Опублікована',
            'slug' => 'published',
        ]);

        $draftCategory = Category::factory()->create([
            'title' => 'Чернетка',
            'slug' => 'draft',
        ]);

        $this->createFilm([
            'title' => 'Published Film',
            'category_id' => $publishedCategory->id,
        ]);

        $this->createFilm([
            'title' => 'Draft Film',
            'category_id' => $draftCategory->id,
            'publish_status' => FilmStatus::Draft,
        ]);

        $request = Request::create('/films');

        $result = $this->createTester()->filter(
            $this->relation(),
            $request
        );

        $this->assertSame(
            ['Опублікована'],
            $result['categories']->pluck('title')->values()->all()
        );
    }

    public function test_pagination_returns_twenty_films_per_page(): void
    {
        $category = Category::factory()->create([
            'title' => 'Фільми',
            'slug' => 'filmi',
        ]);

        Film::factory()
            ->count(25)
            ->create([
                'category_id' => $category->id,
                'publish_status' => FilmStatus::Published,
            ]);

        $request = Request::create('/films');

        $result = $this->createTester()->filter(
            $this->relation(),
            $request
        );

        $films = $result['films'];

        $this->assertSame(20, $films->perPage());
        $this->assertSame(25, $films->total());
        $this->assertSame(1, $films->currentPage());
    }

    public function test_pagination_appends_request_query_parameters(): void
    {
        $category = Category::factory()->create([
            'title' => 'Фільми',
            'slug' => 'filmi',
        ]);

        Film::factory()
            ->count(25)
            ->create([
                'category_id' => $category->id,
                'publish_status' => FilmStatus::Published,
            ]);

        $request = Request::create('/films', 'GET', [
            'sort' => 'latest',
            'category' => 'filmi',
        ]);

        $result = $this->createTester()->filter(
            $this->relation(),
            $request
        );

        $films = $result['films'];

        $pageUrl = $films->url(2);

        $this->assertStringContainsString(
            'sort=latest',
            $pageUrl
        );

        $this->assertStringContainsString(
            'category=filmi',
            $pageUrl
        );
    }
}
