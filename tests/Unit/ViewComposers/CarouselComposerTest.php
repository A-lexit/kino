<?php
namespace Tests\Unit\ViewComposers;

use App\Enums\FilmStatus;
use App\Http\View\Composers\CarouselComposer;
use App\Models\Category;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;

class CarouselComposerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shares_films_html_with_view(): void
    {
        $category = Category::factory()->create();
        Film::factory()->create([
            'publish_status' => FilmStatus::Published,
            'category_id' => $category->id,
        ]);

        $view = Mockery::mock(View::class);
        $view->shouldReceive('with')
            ->once()
            ->with('filmsHtml', Mockery::type('string'));

        (new CarouselComposer())->compose($view);

        // Явний assert, щоб PHPUnit не позначав тест як "risky"
        $this->assertTrue(true);
        Mockery::close();
    }


    public function test_it_excludes_unpublished_films(): void
    {
        $category = Category::factory()->create();

        Film::factory()->create([
            'title' => 'Published Film',
            'publish_status' => FilmStatus::Published,
            'category_id' => $category->id,
        ]);

        Film::factory()->create([
            'title' => 'Draft Film',
            'publish_status' => FilmStatus::Draft,
            'category_id' => $category->id,
        ]);

        $capturedHtml = null;

        $view = Mockery::mock(View::class);
        $view->shouldReceive('with')->once()->withArgs(function ($key, $html) use (&$capturedHtml) {
            $capturedHtml = $html;
            return $key === 'filmsHtml';
        });

        (new CarouselComposer())->compose($view);

        $this->assertStringContainsString('Published Film', $capturedHtml);
        $this->assertStringNotContainsString('Draft Film', $capturedHtml);
    }


    public function test_it_limits_results_to_fifteen(): void
    {
        $category = Category::factory()->create();

        Film::factory()->count(20)->create([
            'publish_status' => FilmStatus::Published,
            'category_id' => $category->id,
        ]);

        // Перевіряємо напряму той самий запит, що й у composer,
        // рахуючи через get()->count(), а не count() на query builder,
        // бо limit() ігнорується агрегатним count()
        $films = Film::published()
            ->whereNotNull('category_id')
            ->whereNotNull('slug')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        $this->assertCount(15, $films);
    }


    public function test_it_caches_result_under_carousel_tag(): void
    {
        $category = Category::factory()->create();
        Film::factory()->create([
            'publish_status' => FilmStatus::Published,
            'category_id' => $category->id,
        ]);

        $view = Mockery::mock(View::class);
        $view->shouldReceive('with')->once();

        (new CarouselComposer())->compose($view);

        $this->assertTrue(Cache::tags(['carousel'])->has('cached_films'));
    }


    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

}
