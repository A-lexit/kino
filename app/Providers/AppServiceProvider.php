<?php
namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Models\Film;
use App\Observers\FilmObserver;
use App\Observers\SlugObserver;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('uk');

        Paginator::useBootstrap();

        // Головний обсервер фільмів
        Film::observe(FilmObserver::class);

        // Масова реєстрація SlugObserver для всіх моделей
        $slugModels = [
            \App\Models\Actor::class,
            \App\Models\Age::class,
            \App\Models\Caption::class,
            \App\Models\Category::class,
            \App\Models\Company::class,
            \App\Models\Composer::class,
            \App\Models\Country::class,
            \App\Models\Director::class,
            \App\Models\Film::class,
            \App\Models\Genre::class,
            \App\Models\Language::class,
            \App\Models\Producer::class,
            \App\Models\Quality::class,
            \App\Models\Rating::class,
            \App\Models\Selection::class,
            \App\Models\Status::class,
            \App\Models\Year::class,
        ];

        foreach ($slugModels as $model) {
            $model::observe(SlugObserver::class);
        }
    }

}
