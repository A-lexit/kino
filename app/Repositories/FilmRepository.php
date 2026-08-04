<?php
namespace App\Repositories;

use App\Models\Film;
use Illuminate\Support\Facades\Cache;

class FilmRepository
{
    public function firstFilm($slug)
    {
        return Cache::remember("film_{$slug}", now()->addHours(6), function () use ($slug) {
            return Film::with('user', 'category', 'genres', 'countries', 'actors', 'directors',
                'composers', 'companies', 'producers', 'comments', 'state', 'year', 'age', 'rating',
                'season', 'status', 'quality', 'languages', 'captions')
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
            /*->where('likes', '>', 0)*/
            ->where('category_id', $category_id)
            ->orderBy('likes', 'desc')
            ->limit(5)
            ->get();
    }

    public function getSideFeaturedFilms()
    {
        return Cache::tags(['featured_films'])->remember('featured_films', now()->addHours(6), function () {
            return Film::where('is_featured', 1)->orderBy('updated_at', 'desc')->limit(5)->get();
        });
    }

    public function moreFilmsLimit($slug, $category_id)
    {
        if (!$category_id) {
            return collect();
        }

        return Film::where('slug', '!=', $slug)
            ->where('category_id', $category_id)
            ->with('category')
            ->inRandomOrder()
            ->limit(5)
            ->get();
    }

}
