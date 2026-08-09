<?php

namespace App\Jobs;

use App\Media\ImageMedia;
use App\Models\Film;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class DownloadFilmPoster implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $filmId,
        public string $posterPath
    ) {}

    public function handle(ImageMedia $imageMedia): void
    {
        $film = Film::find($this->filmId);

        if (!$film) {
            return;
        }

        try {
            $imageUrl = 'https://image.tmdb.org/t/p/original'
                . $this->posterPath;

            Log::info('Завантаження постера TMDB', [
                'film_id' => $film->id,
                'url' => $imageUrl,
            ]);

            $response = Http::timeout(30)
                ->connectTimeout(10)
                ->get($imageUrl);

            $response->throw();

            $tempContent = $response->body();

            if (empty($tempContent)) {
                throw new \RuntimeException('TMDB повернув порожнє зображення.');
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'tmdb_');

            file_put_contents($tempFile, $tempContent);

            $uploadedFile = new UploadedFile(
                $tempFile,
                basename($this->posterPath),
                'image/jpeg',
                null,
                true
            );

            $path = $imageMedia->upload(
                $uploadedFile,
                'images/' . now()->format('Y-m-d')
            );

            @unlink($tempFile);

            $film->update([
                'thumbnail' => $path,
            ]);

            Log::info('Постер успішно завантажено', [
                'film_id' => $film->id,
                'path' => $path,
            ]);

        } catch (\Throwable $e) {

            Log::error('Помилка завантаження постера TMDB', [
                'film_id' => $film->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
