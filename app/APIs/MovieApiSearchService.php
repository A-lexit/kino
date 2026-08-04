<?php
namespace App\APIs;

use Illuminate\Support\Facades\Http;

class MovieApiSearchService
{
    public function search(string $query): array
    {
        if (empty($query)) {
            return [];
        }

        $response = Http::get(
            'https://api.themoviedb.org/3/search/movie',
            [
                'api_key'  => config('services.tmdb.key'),
                'language' => 'uk-UA',
                'query'    => $query,
            ]
        )->json();

        $results = $response['results'] ?? [];

        usort($results, function ($a, $b) {
            return strtotime($b['release_date'] ?? '1970-01-01')
                <=> strtotime($a['release_date'] ?? '1970-01-01');
        });

        return $results;
    }

    public function upcoming(): array
    {
        $response = Http::get(
            'https://api.themoviedb.org/3/movie/upcoming',
            [
                'api_key'  => config('services.tmdb.key'),
                'language' => 'uk-UA',
            ]
        )->json();

        return $response['results'] ?? [];
    }
}
