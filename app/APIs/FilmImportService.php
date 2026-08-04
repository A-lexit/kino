<?php
namespace App\APIs;

use App\Models\Film;
use App\Media\ImageMedia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class FilmImportService
{
    public function __construct(
        private TelegramService $telegram,
        private ImageMedia $imageMedia   // ← додаємо
    ) {}

    public function import($tmdbId)
    {
        $movie = Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}", [
            'api_key'  => config('services.tmdb.key'),
            'language' => 'uk-UA',
        ])->json();

        if (empty($movie['id'])) {
            throw new \Exception('Фільм не знайдено в TMDB');
        }

        // Перевіряємо, чи вже є такий фільм
        $film = Film::where('tmdb_id', $movie['id'])->first();

        if ($film) {
            return $film;
        }

        $thumbnail = null;

        // Завантажуємо постер
        if (!empty($movie['poster_path'])) {
            $thumbnail = $this->downloadPoster($movie['poster_path']);
        }

        $film = Film::create([
            'tmdb_id'       => $movie['id'],
            'title'         => $movie['title'],
            'slug'          => Str::slug($movie['title'] . '-' . Str::random(5)),
            'origin_title'  => $movie['original_title'] ?? $movie['title'],
            'description'   => $movie['overview'] ?? null,
            'thumbnail'     => $thumbnail,
            'tmdb_poster'   => $movie['poster_path'],
            'datepicker'    => $movie['release_date'] ?? null,
            'publish_status'=> 'draft',
        ]);

        // Відправляємо в Telegram
        $this->telegram->sendFilm($film);

        return $film;
    }

    /**
     * Завантажує постер з TMDB і зберігає через ImageMedia (як і решта проєкту)
     */
    protected function downloadPoster(string $posterPath): ?string
    {
        try {
            $imageUrl = 'https://image.tmdb.org/t/p/original' . $posterPath;

            \Log::info('Спроба завантажити постер: ' . $imageUrl);

            $tempContent = Http::timeout(30)           // збільшуємо до 30 секунд
            ->connectTimeout(10)
            ->get($imageUrl)
            ->body();

            if (empty($tempContent)) {
                return null;
            }

            // Створюємо тимчасовий файл
            $tempFile = tempnam(sys_get_temp_dir(), 'tmdb_');
            file_put_contents($tempFile, $tempContent);

            \Log::info('Спроба завантажити постер: ' . $imageUrl);

            $uploadedFile = new UploadedFile(
                $tempFile,
                basename($posterPath),
                'image/jpeg',
                null,
                true
            );

            // Зберігаємо через твій ImageMedia (з webp)
            $path = $this->imageMedia->upload($uploadedFile, 'images/' . date('Y-m-d'));
            \Log::info('Постер успішно завантажено: ' . $path);
            @unlink($tempFile); // видаляємо тимчасовий файл

            return $path;

        } catch (\Exception $e) {

            \Log::error('Помилка завантаження постера TMDB: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return null;
        }
    }
}
