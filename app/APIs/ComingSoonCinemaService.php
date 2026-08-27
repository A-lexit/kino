<?php
namespace App\APIs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ComingSoonCinemaService
{
    public function upcoming(): array
    {
        return Cache::remember(
            'tmdb_upcoming_movies',
            now()->addHours(6),
            function () {
                try {
                    $response = Http::timeout(15)
                        ->retry(3, 500)
                        ->get('https://api.themoviedb.org/3/movie/upcoming', [
                            'api_key' => config(
                                'services.tmdb.key',
                                '1d5ab2f6c0e9dcc10e579de449eeac69'
                            ),
                            'language' => 'uk-UA',
                            'region' => 'UA',
                        ]);

                    if ($response->successful()) {
                        return array_slice(
                            $response->json('results', []),
                            0,
                            5
                        );
                    }
                } catch (\Throwable $e) {
                    Log::error(
                        'TMDb API Connection Timeout/Error: ' .
                        $e->getMessage()
                    );
                }

                return [];
            }
        );
    }
}
