<?php

namespace App\Http\Controllers;

use App\Constants\CategoryIds;
use App\Repositories\FilmRepository;

class FilmController extends Controller
{
    public function show(FilmRepository $filmRepository, $category, $slug)
    {
        $film = $filmRepository->firstFilm($slug);
        $bestFilms = $filmRepository->getSideBestFilms($film->category_id);
        $featuredFilms = $filmRepository->getSideFeaturedFilms();
        $relatedFilms = $filmRepository->moreFilmsLimit($slug, $film->category_id);

        return view('films.show', compact('film', 'bestFilms', 'featuredFilms', 'relatedFilms'));
    }
}
