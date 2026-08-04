<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Film;

class CarouselComposer
{
    public function compose(View $view)
    {
        $html = Cache::tags(['carousel'])->remember('cached_films', now()->addHours(6), function () {
            $films = Film::with('category')
                ->published()
                ->whereNotNull('category_id')
                ->whereNotNull('slug')
                ->orderBy('created_at', 'desc')
                ->limit(15)
                ->get();

            return view('layouts.inc.partials.carousels', compact('films'))->render();
        });

        $view->with('filmsHtml', $html);
    }

}
