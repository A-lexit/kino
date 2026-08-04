<?php

namespace App\Media;

use Illuminate\Support\Facades\Storage;

class ImageStorage
{
    protected string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default');
    }

    public function store(string $tempPath, string $folder): string
    {
        $path = trim($folder, '/') . '/' . basename($tempPath);

        Storage::disk($this->disk)->put(
            $path,
            file_get_contents($tempPath)
        );

        @unlink($tempPath); // Видаляємо тимчасовий файл після збереження

        return $path;
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

    public function url(?string $path): string
    {
        if (!$path) {
            return asset('defaults/fake_movie_cover.webp');
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk($this->disk)->url($path);
    }

    public function exists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

}
