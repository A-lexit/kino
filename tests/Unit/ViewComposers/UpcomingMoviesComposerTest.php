<?php

namespace Tests\Unit\ViewComposers;

use App\APIs\ComingSoonCinemaService;
use App\Http\View\Composers\UpcomingMoviesComposer;
use Illuminate\View\View;
use Mockery\MockInterface;
use Tests\TestCase;

class UpcomingMoviesComposerTest extends TestCase
{
    public function test_compose_passes_upcoming_movies_to_view(): void
    {
        $movies = [
            ['id' => 1, 'title' => 'Film 1'],
            ['id' => 2, 'title' => 'Film 2'],
        ];

        $cinemaService = $this->mock(
            ComingSoonCinemaService::class,
            function (MockInterface $mock) use ($movies) {
                $mock->shouldReceive('upcoming')
                    ->once()
                    ->andReturn($movies);
            }
        );

        $view = $this->mock(View::class);

        $view->shouldReceive('with')
            ->once()
            ->with('upcomingMovies', $movies);

        $composer = new UpcomingMoviesComposer($cinemaService);

        $composer->compose($view);
    }
}
