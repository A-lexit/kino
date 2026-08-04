<?php
namespace Tests\Unit\View\Composers;

use App\APIs\ComingSoonCinemaService;
use App\Http\View\Composers\UpcomingMoviesComposer;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;

class UpcomingMoviesComposerTest extends TestCase
{
    public function test_compose_passes_upcoming_movies_to_view(): void
    {
        Cache::flush();

        $movies = collect([
            [
                'id' => 1,
                'title' => 'Avatar 3',
            ],
            [
                'id' => 2,
                'title' => 'Dune: Messiah',
            ],
        ]);

        $service = Mockery::mock(ComingSoonCinemaService::class);

        $service->shouldReceive('upcoming')
            ->once()
            ->andReturn($movies);

        $view = Mockery::mock(View::class);

        $view->shouldReceive('with')
            ->once()
            ->with('upcomingMovies', $movies);

        $composer = new UpcomingMoviesComposer($service);

        $composer->compose($view);

        $this->assertTrue(true);
    }


    protected function tearDown(): void
    {
        Cache::flush();

        Mockery::close();

        parent::tearDown();
    }

}
