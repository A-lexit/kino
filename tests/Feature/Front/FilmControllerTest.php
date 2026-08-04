<?php
namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Film;
use App\Repositories\FilmRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class FilmControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_displays_film_page(): void
    {
        $category = Category::factory()->create([
            'slug' => 'films',
        ]);

        $film = Film::factory()->create([
            'category_id' => $category->id,
            'slug' => 'avatar',
        ]);

        $bestFilms = Film::factory()->count(2)->make();
        $featuredFilms = Film::factory()->count(2)->make();
        $relatedFilms = Film::factory()->count(2)->make();

        $repository = Mockery::mock(FilmRepository::class);

        $repository->shouldReceive('firstFilm')
            ->once()
            ->with($film->slug)
            ->andReturn($film);

        $repository->shouldReceive('getSideBestFilms')
            ->once()
            ->with($film->category_id)
            ->andReturn($bestFilms);

        $repository->shouldReceive('getSideFeaturedFilms')
            ->once()
            ->andReturn($featuredFilms);

        $repository->shouldReceive('moreFilmsLimit')
            ->once()
            ->with($film->slug, $film->category_id)
            ->andReturn($relatedFilms);

        $this->app->instance(FilmRepository::class, $repository);

        $response = $this->get(
            route('single', [
                'category' => $category->slug,
                'slug' => $film->slug,
            ])
        );

        $response->assertOk();

        $response->assertViewIs('films.show');

        $response->assertViewHas([
            'film',
            'bestFilms',
            'featuredFilms',
            'relatedFilms',
        ]);

        $this->assertSame($film, $response->viewData('film'));
        $this->assertSame($bestFilms, $response->viewData('bestFilms'));
        $this->assertSame($featuredFilms, $response->viewData('featuredFilms'));
        $this->assertSame($relatedFilms, $response->viewData('relatedFilms'));
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

}
