<?php

namespace App\Media;

use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Завантаження власного відеофайлу (наразі — трейлер).
 * Окремо від ImageMedia, бо там конвертація через spatie/image (тільки для картинок).
 */
class FilmVideoMedia
{
    protected array $allowedExtensions = ['mp4', 'webm', 'ogg'];
    protected string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default');
    }

    public function uploadTrailer(Request $request, array &$data, ?Film $film = null): void
    {
        if (!$request->hasFile('trailer_file')) {
            return;
        }

        $file = $request->file('trailer_file');

        if (!in_array(strtolower($file->getClientOriginalExtension()), $this->allowedExtensions)) {
            return;
        }

        if ($film && $film->trailer_file) {
            $this->delete($film->trailer_file);
        }

        $folder = 'trailers/' . date('Y-m-d');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Storage::putFileAs автоматично збереже файл і створить папки
        $path = Storage::disk($this->disk)->putFileAs($folder, $file, $filename);

        $data['trailer_file'] = $path;
        $data['trailer_youtube_id'] = null;
    }

    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
        }
    }

}
