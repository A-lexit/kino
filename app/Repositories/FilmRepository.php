<?php
namespace App\Repositories;

use App\Models\Film;
use Illuminate\Support\Facades\Cache;

class FilmRepository
{
    public function firstFilm($slug)
    {
        return Cache::remember("film_{$slug}", now()->addHours(6), function () use ($slug) {
            return Film::with([
                'user', 'category', 'genres', 'countries', 'actors', 'directors',
                'composers', 'companies', 'producers', 'comments', 'state', 'year', 'age', 'rating',
                'season', 'status', 'quality', 'languages', 'captions',
                'relatedFilms' => function ($query) {
                    $query->leftJoin('years', 'films.year_id', '=', 'years.id')
                        ->published()
                        ->orderBy('years.title', 'asc')
                        ->select('films.*');
                }
            ])
                ->published() // Застосування scope для головного запиту
                ->where('slug', $slug)
                ->firstOrFail();
        });
    }

    public function getSideBestFilms($category_id)
    {
        if (!$category_id) {
            return collect();
        }

        return Film::join('states', 'films.id', '=', 'states.film_id')
            ->with('category', 'state')
            ->published() // Застосування scope
            /*->where('likes', '>', 0)*/
            ->where('category_id', $category_id)
            ->orderBy('likes', 'desc')
            ->limit(5)
            ->get();
    }

    public function getSideFeaturedFilms()
    {
        return Cache::tags(['featured_films'])->remember('featured_films', now()->addHours(6), function () {
            return Film::published() // Застосування scope
            ->where('is_featured', 1)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();
        });
    }

    public function moreFilmsLimit($film, $category_id)
    {
        if (!$category_id) {
            return collect();
        }
        $slug = is_object($film) ? $film->slug : $film;
        $excludedIds = [];
        if (is_object($film)) {
            $excludedIds = $film->relationLoaded('relatedFilms')
                ? $film->relatedFilms->pluck('id')->toArray()
                : $film->relatedFilms()->pluck('films.id')->toArray();
        } else {
            $currentFilm = Film::where('slug', $slug)->first();
            if ($currentFilm) {
                $excludedIds = $currentFilm->relatedFilms()->pluck('films.id')->toArray();
            }
        }
        $excludeIds = array_merge([is_object($film) ? $film->id : null], $excludedIds);
        $excludeIds = array_filter($excludeIds);

        return Film::published()
            ->whereNotIn('id', $excludeIds)
            ->where('slug', '!=', $slug)
            ->where('category_id', $category_id)
            ->with('category')
            ->inRandomOrder()
            ->limit(6)
            ->get();
    }

}
