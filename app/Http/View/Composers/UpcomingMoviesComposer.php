<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\APIs\ComingSoonCinemaService;

class UpcomingMoviesComposer
{
    protected $cinemaService;

    public function __construct(ComingSoonCinemaService $cinemaService)
    {
        $this->cinemaService = $cinemaService;
    }

    public function compose(View $view)
    {
        $view->with(
            'upcomingMovies',
            $this->cinemaService->upcoming()
        );
    }

}
