<?php
namespace App\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Constants\ImageSizes;

class ImageMedia
{
    public function __construct(
        protected ImageStorage $storage,
        protected ImageConverter $converter
    ) {}

    public function upload(
        UploadedFile $file,
        string $folder,
        ?int $width = null,
        ?int $height = null,
        ?string $titleSlug = null
    ): string {
        $tempPath = $this->converter->convertToWebp(
            $file,
            $width,
            $height,
            $titleSlug
        );

        return $this->storage->store($tempPath, $folder);
    }

    public function uploadWithThumbnail(
        UploadedFile $file,
        string $folder,
        int $origWidth,
        int $origHeight,
        int $thumbWidth,
        int $thumbHeight,
        ?int $searchWidth = null,
        ?int $searchHeight = null,
        ?int $largeThumbWidth = null,
        ?int $largeThumbHeight = null,
        ?string $titleSlug = null,
        string $posterSuffix = 'poster',
        string $thumbSuffix = 'thumb',
        string $searchSuffix = 'search',
        string $largeThumbSuffix = 'large-thumb',
    ): array {
        $paths = $this->converter->convertWithThumbnail(
            $file,
            $origWidth,
            $origHeight,
            $thumbWidth,
            $thumbHeight,
            $searchWidth,
            $searchHeight,
            $largeThumbWidth,
            $largeThumbHeight,
            $titleSlug,
            $posterSuffix,
            $thumbSuffix,
            $searchSuffix,
            $largeThumbSuffix,
        );

        $result = [
            'original' => $this->storage->store(
                $paths['original'],
                $folder
            ),
            'poster' => $this->storage->store(
                $paths['poster'],
                $folder
            ),
            'thumb' => $this->storage->store(
                $paths['thumb'],
                $folder
            ),
        ];

        if (isset($paths['search'])) {
            $result['search'] = $this->storage->store(
                $paths['search'],
                $folder
            );
        }

        if (isset($paths['largeThumb'])) {
            $result['largeThumb'] = $this->storage->store(
                $paths['largeThumb'],
                $folder
            );
        }

        return $result;
    }

    /**
     * Завантажує favicon під фіксованими іменами.
     */
    public function uploadFavicon(
        UploadedFile $file,
        string $folder = 'settings'
    ): array {
        $paths = $this->converter->convertFavicon($file);

        $disk = Storage::disk('public');

        $files = [
            'original' => "{$folder}/favicon.webp",
            '16'       => "{$folder}/favicon-16.webp",
            '32'       => "{$folder}/favicon-32.webp",
            '180'      => "{$folder}/favicon-180.webp",
        ];

        foreach ($files as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }

        $result = [];

        foreach ($files as $key => $destination) {
            $result[$key] = $destination;

            $disk->put(
                $destination,
                file_get_contents($paths[$key])
            );
        }

        $this->deleteTempDirectory(
            $paths['directory'] ?? null
        );

        return $result;
    }

    /**
     * Завантажує logo під фіксованим ім'ям.
     */
    public function uploadLogo(
        UploadedFile $file,
        string $folder = 'settings'
    ): string {
        $tempPath = $this->converter->convertToWebp(
            $file,
            ImageSizes::LOGO_WIDTH,
            ImageSizes::LOGO_HEIGHT,
            'logo'
        );

        $destination = "{$folder}/logo.webp";

        $disk = Storage::disk('public');

        if ($disk->exists($destination)) {
            $disk->delete($destination);
        }

        /*
         * Видаляємо старі logo-* файли,
         * які могли залишитися від старої схеми.
         */
        foreach ($disk->files($folder) as $path) {
            $filename = basename($path);

            if (
                str_starts_with($filename, 'logo-')
                && $filename !== 'logo.webp'
            ) {
                $disk->delete($path);
            }
        }

        $disk->put(
            $destination,
            file_get_contents($tempPath)
        );

        @unlink($tempPath);

        return $destination;
    }

    /**
     * Видаляє logo, яке зараз використовується.
     */
    public function deleteLogo(?string $path): void
    {
        if (!$path) {
            return;
        }

        $this->storage->delete($path);
    }

    public function deleteFavicon(?string $path): void
    {
        if (!$path) {
            return;
        }

        $this->storage->delete($path);

        $this->storage->delete(
            preg_replace('/\.webp$/i', '-16.webp', $path)
        );

        $this->storage->delete(
            preg_replace('/\.webp$/i', '-32.webp', $path)
        );

        $this->storage->delete(
            preg_replace('/\.webp$/i', '-180.webp', $path)
        );
    }

    public function delete(?string $path): void
    {
        $this->storage->delete($path);
    }

    public function url(?string $path): string
    {
        return $this->storage->url($path);
    }

    public function exists(string $path): bool
    {
        return $this->storage->exists($path);
    }

    protected function deleteTempDirectory(?string $directory): void
    {
        if (!$directory || !is_dir($directory)) {
            return;
        }

        foreach (glob($directory . '/*') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        @rmdir($directory);
    }

}
