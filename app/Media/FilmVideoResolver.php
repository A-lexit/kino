<?php
namespace App\Media;

use App\Models\Film;
use Illuminate\Support\Facades\Storage;

class FilmVideoResolver
{
    protected string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default');
    }

    public function hasTrailer(Film $film): bool
    {
        return !empty($film->trailer_youtube_id) || !empty($film->trailer_file);
    }

    public function youtubeEmbedUrl(Film $film): ?string
    {
        if (empty($film->trailer_youtube_id)) {
            return null;
        }

        return 'https://www.youtube.com/embed/' . $film->trailer_youtube_id;
    }

    public function fileUrl(Film $film): ?string
    {
        if (empty($film->trailer_file)) {
            return null;
        }

        // Генеруємо правильний URL для відео
        return Storage::disk($this->disk)->url($film->trailer_file);
    }

    public static function extractYoutubeId(string $url): ?string
    {
        $url = trim($url);

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([a-zA-Z0-9_-]{11})~', $url, $m)) {
            return $m[1];
        }

        return null;
    }

}
