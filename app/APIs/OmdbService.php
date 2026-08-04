<?php
namespace App\APIs;

use App\Models\Film;
use Illuminate\Support\Facades\Http;

class OmdbService
{
    /**
     * Шукає фільм на OMDb за оригінальною назвою + роком випуску.
     * Якщо з роком нічого не знайдено (рік у базі може бути неточним/тестовим) —
     * повторює пошук без року, перш ніж повернути null.
     *
     * @return array{imdb_id: string, imdb_rating: float}|null
     */
    public function fetchRating(Film $film): ?array
    {
        $title = $film->origin_title ?: $film->title;
        $year = $film->year?->title;

        $result = $this->search($title, $year);

        if (is_null($result) && $year) {
            // fallback: рік у базі міг бути неточним — пробуємо без нього
            $result = $this->search($title, null);
        }

        return $result;
    }

    /**
     * @return array{imdb_id: string, imdb_rating: float}|null
     */
    protected function search(string $title, ?int $year): ?array
    {
        $params = [
            'apikey' => env('OMDB_API_KEY'),
            't' => $title,
        ];

        if ($year) {
            $params['y'] = $year;
        }

        $response = Http::get('https://www.omdbapi.com/', $params);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        if (($data['Response'] ?? null) !== 'True') {
            return null;
        }

        if (empty($data['imdbRating']) || $data['imdbRating'] === 'N/A') {
            return null;
        }

        return [
            'imdb_id' => $data['imdbID'] ?? null,
            'imdb_rating' => (float) $data['imdbRating'],
        ];
    }
}
