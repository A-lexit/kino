<?php
namespace App\Media;

use App\Constants\ImageSizes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;

class ImageConverter
{
    public function convertToWebp(
        UploadedFile $file,
        ?int $width = null,
        ?int $height = null,
        ?string $titleSlug = null
    ): string {
        $filename = $this->generateFilename($titleSlug);
        $tempPath = sys_get_temp_dir() . '/' . $filename;

        $this->saveProcessedImage(
            $file->getRealPath(),
            $tempPath,
            $width,
            $height
        );

        return $tempPath;
    }

    public function convertWithThumbnail(
        UploadedFile $file,
        ?int $origWidth,
        ?int $origHeight,
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
        $baseName = $this->generateFilenameBase($titleSlug);

        $originalTempPath = sys_get_temp_dir() . "/{$baseName}.webp";

        $this->saveOriginalImage(
            $file->getRealPath(),
            $originalTempPath
        );

        $posterTempPath = sys_get_temp_dir()
            . "/{$baseName}-{$posterSuffix}.webp";

        $this->saveProcessedImage(
            $file->getRealPath(),
            $posterTempPath,
            $origWidth,
            $origHeight
        );

        $largeThumbTempPath = null;

        if ($largeThumbWidth && $largeThumbHeight) {
            $largeThumbTempPath = sys_get_temp_dir()
                . "/{$baseName}-{$largeThumbSuffix}.webp";

            $this->saveProcessedImage(
                $file->getRealPath(),
                $largeThumbTempPath,
                $largeThumbWidth,
                $largeThumbHeight
            );
        }

        $thumbTempPath = sys_get_temp_dir()
            . "/{$baseName}-{$thumbSuffix}.webp";

        $this->saveProcessedImage(
            $file->getRealPath(),
            $thumbTempPath,
            $thumbWidth,
            $thumbHeight
        );

        $paths = [
            'original' => $originalTempPath,
            'poster'   => $posterTempPath,
            'thumb'    => $thumbTempPath,
        ];

        if ($largeThumbTempPath) {
            $paths['largeThumb'] = $largeThumbTempPath;
        }

        if ($searchWidth && $searchHeight) {
            $searchTempPath = sys_get_temp_dir()
                . "/{$baseName}-{$searchSuffix}.webp";

            $this->saveProcessedImage(
                $file->getRealPath(),
                $searchTempPath,
                $searchWidth,
                $searchHeight
            );

            $paths['search'] = $searchTempPath;
        }

        return $paths;
    }

    protected function saveProcessedImage(
        string $sourcePath,
        string $destinationPath,
        ?int $width,
        ?int $height
    ): void {
        $image = Image::load($sourcePath)
            ->format('webp')
            ->quality(90);

        if ($width && $height) {
            $image->fit(Fit::Contain, $width, $height);
        } elseif ($width) {
            $image->width($width);
        } elseif ($height) {
            $image->height($height);
        }

        $image->save($destinationPath);
    }

    protected function generateFilenameBase(?string $titleSlug): string
    {
        $slug = $titleSlug ? Str::slug($titleSlug) : 'media';
        $timestamp = now()->format('Ymd-His');
        $random = Str::lower(Str::random(4));

        return "{$slug}-{$timestamp}-{$random}";
    }

    protected function generateFilename(?string $titleSlug): string
    {
        return $this->generateFilenameBase($titleSlug) . '.webp';
    }

    public function convertFavicon(
        UploadedFile $file
    ): array {

        $tempDirectory = sys_get_temp_dir()
            . '/favicon-' . Str::lower(Str::random(12));

        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $originalPath = $tempDirectory . '/favicon.webp';

        $this->saveProcessedImage(
            $file->getRealPath(),
            $originalPath,
            null,
            null
        );

        $favicon16Path = $tempDirectory . '/favicon-16.webp';

        $this->saveProcessedImage(
            $file->getRealPath(),
            $favicon16Path,
            ImageSizes::FAVICON_16,
            ImageSizes::FAVICON_16
        );

        $favicon32Path = $tempDirectory . '/favicon-32.webp';

        $this->saveProcessedImage(
            $file->getRealPath(),
            $favicon32Path,
            ImageSizes::FAVICON_32,
            ImageSizes::FAVICON_32
        );

        $favicon180Path = $tempDirectory . '/favicon-180.webp';

        $this->saveProcessedImage(
            $file->getRealPath(),
            $favicon180Path,
            ImageSizes::FAVICON_180,
            ImageSizes::FAVICON_180
        );

        return [
            'original'  => $originalPath,
            '16'        => $favicon16Path,
            '32'        => $favicon32Path,
            '180'       => $favicon180Path,
            'directory' => $tempDirectory,
        ];
    }

    protected function saveOriginalImage(
        string $sourcePath,
        string $destinationPath
    ): void {
        $image = Image::load($sourcePath)
            ->format('webp')
            ->quality(90);

        $width = $image->getWidth();
        $height = $image->getHeight();

        if ($width >= $height) {
            if ($width > ImageSizes::ORIGINAL_MAX_SIZE) {
                $image->width(ImageSizes::ORIGINAL_MAX_SIZE);
            }
        } else {
            if ($height > ImageSizes::ORIGINAL_MAX_SIZE) {
                $image->height(ImageSizes::ORIGINAL_MAX_SIZE);
            }
        }

        $image->save($destinationPath);
    }

    public function regenerateImageSet(
        string $originalPath,
        string $posterPath,
        int $posterWidth,
        int $posterHeight,
        string $thumbPath,
        int $thumbWidth,
        int $thumbHeight,
        ?string $searchPath = null,
        ?int $searchWidth = null,
        ?int $searchHeight = null,
        ?string $largeThumbPath = null,
        ?int $largeThumbWidth = null,
        ?int $largeThumbHeight = null,
    ): void {
        $this->saveProcessedImage(
            $originalPath,
            $posterPath,
            $posterWidth,
            $posterHeight
        );

        $this->saveProcessedImage(
            $originalPath,
            $thumbPath,
            $thumbWidth,
            $thumbHeight
        );

        if (
            $largeThumbPath
            && $largeThumbWidth
            && $largeThumbHeight
        ) {
            $this->saveProcessedImage(
                $originalPath,
                $largeThumbPath,
                $largeThumbWidth,
                $largeThumbHeight
            );
        }

        if ($searchPath && $searchWidth && $searchHeight) {
            $this->saveProcessedImage(
                $originalPath,
                $searchPath,
                $searchWidth,
                $searchHeight
            );
        }
    }

}
