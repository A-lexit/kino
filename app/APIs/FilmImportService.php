<?php

namespace App\APIs;

use App\Jobs\DownloadFilmPoster;
use App\Jobs\SendFilmToTelegram;
use App\Models\Film;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FilmImportService
{
    public function import($tmdbId)
    {
        $movie = Http::get(
            "https://api.themoviedb.org/3/movie/{$tmdbId}",
            [
                'api_key'  => config('services.tmdb.key'),
                'language' => 'uk-UA',
            ]
        )->json();

        if (empty($movie['id'])) {
            throw new \Exception('Фільм не знайдено в TMDB');
        }

        // Перевіряємо, чи вже є такий фільм
        $film = Film::where('tmdb_id', $movie['id'])->first();

        if ($film) {
            return $film;
        }

        // Створюємо фільм одразу.
        // Постер і Telegram обробляються у фоні.
        $film = Film::create([
            'tmdb_id'        => $movie['id'],
            'title'          => $movie['title'],
            'slug'           => Str::slug($movie['title'] . '-' . Str::random(5)),
            'origin_title'   => $movie['original_title'] ?? $movie['title'],
            'description'    => $movie['overview'] ?? null,
            'thumbnail'      => null,
            'tmdb_poster'    => $movie['poster_path'] ?? null,
            'datepicker'     => $movie['release_date'] ?? null,
            'publish_status' => 'draft',
        ]);

        // Якщо є постер:
        // спочатку завантажуємо його,
        // потім відправляємо фільм у Telegram.
        if (!empty($movie['poster_path'])) {
            Bus::chain([
                new DownloadFilmPoster(
                    $film->id,
                    $movie['poster_path']
                ),

                new SendFilmToTelegram(
                    $film->id
                ),
            ])->dispatch();
        } else {
            // Якщо постера немає — Telegram можна відправити одразу.
            SendFilmToTelegram::dispatch($film->id);
        }

        return $film;
    }
}
