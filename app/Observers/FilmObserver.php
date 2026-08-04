<?php
namespace App\Observers;

use App\Models\Film;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\State;

class FilmObserver
{
    public function creating(Film $film): void
    {
        if (Auth::check()) {
            $film->author_id = Auth::id();
        }

    }

    public function created(Film $film): void      //Likes, views, очищення кешу
    {
        State::create([
            'film_id' => $film->id
        ]);

        $this->clearCache($film);
    }

    public function updated(Film $film): void
    {
        $this->clearCache($film);
    }

    public function deleted(Film $film): void
    {
        $this->clearCache($film);
    }

    public function restored(Film $film): void
    {
        $this->clearCache($film);
    }

    public function forceDeleted(Film $film): void
    {
        $this->clearCache($film);
    }

    /**
     * Очищає кеш конкретного фільму (старий + новий slug)
     */
    protected function clearSpecificFilmCache(Film $film): void
    {
        if ($film->slug) {
            Cache::forget("film_{$film->slug}");
        }

        $originalSlug = $film->getOriginal('slug');
        if ($originalSlug && $originalSlug !== $film->slug) {
            Cache::forget("film_{$originalSlug}");
        }
    }

    protected function flushGeneralCache(): void
    {
        Cache::tags([
            'carousel',
            'featured_films',
            'home_films'
        ])->flush();
    }

    protected function clearCache(Film $film): void
    {
        $this->clearSpecificFilmCache($film);
        $this->flushGeneralCache();
    }

}
