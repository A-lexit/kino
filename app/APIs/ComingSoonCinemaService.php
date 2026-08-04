<?php
namespace App\APIs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class ComingSoonCinemaService
{
    public function upcoming()
    {
        return Cache::remember('upcoming_movies', 3600, function () {

            $response = Http::get(
                'https://api.themoviedb.org/3/movie/upcoming',
                [
                    'api_key' => env('TMDB_API_KEY'),
                    'language' => 'uk-UA',
                    'region' => 'UA'
                ]
            );

            if ($response->successful()) {

                return collect($response->json()['results'])
                    ->take(5);
            }

            return collect();
        });
    }
}
