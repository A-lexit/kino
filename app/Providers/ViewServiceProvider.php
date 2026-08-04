<?php
namespace App\Providers;

use App\Http\View\Composers\CurrencyComposer;
use App\Http\View\Composers\SettingsComposer;
use App\Http\View\Composers\UpcomingMoviesComposer;
use App\Http\View\Composers\CarouselComposer;
use App\Http\View\Composers\MenuComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.layout', SettingsComposer::class);
        View::composer('layouts.layout', CurrencyComposer::class);
        View::composer('layouts.layout', CarouselComposer::class);
        View::composer('layouts.layout', MenuComposer::class);

        View::composer('films.inc.sidebar', UpcomingMoviesComposer::class);
    }

}
