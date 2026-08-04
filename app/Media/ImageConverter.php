<?php

namespace App\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;

use App\Constants\ImageSizes;

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

        $this->saveProcessedImage($file->getRealPath(), $tempPath, $width, $height);

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
        ?string $titleSlug = null,
        string $posterSuffix = 'poster',
        string $thumbSuffix = 'thumb',
        string $searchSuffix = 'search',
    ): array {
        $baseName = $this->generateFilenameBase($titleSlug);

        $originalTempPath = sys_get_temp_dir() . "/{$baseName}.webp";
        $this->saveOriginalImage($file->getRealPath(), $originalTempPath);

        $posterTempPath = sys_get_temp_dir() . "/{$baseName}-{$posterSuffix}.webp";
        $this->saveProcessedImage($file->getRealPath(), $posterTempPath, $origWidth, $origHeight);

        $thumbTempPath = sys_get_temp_dir() . "/{$baseName}-{$thumbSuffix}.webp";
        $this->saveProcessedImage($file->getRealPath(), $thumbTempPath, $thumbWidth, $thumbHeight);

        $paths = [
            'original' => $originalTempPath,
            'poster'   => $posterTempPath,
            'thumb'    => $thumbTempPath,
        ];

        if ($searchWidth && $searchHeight) {
            $searchTempPath = sys_get_temp_dir() . "/{$baseName}-{$searchSuffix}.webp";
            $this->saveProcessedImage($file->getRealPath(), $searchTempPath, $searchWidth, $searchHeight);
            $paths['search'] = $searchTempPath;
        }

        return $paths;
    }


    protected function saveProcessedImage(string $sourcePath, string $destinationPath, ?int $width, ?int $height): void
    {
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
        UploadedFile $file,
        ?string $titleSlug = 'favicon'
    ): array {
        $baseName = $this->generateFilenameBase($titleSlug);

        // Оригінал
        $originalPath = sys_get_temp_dir() . "/{$baseName}.webp";
        $this->saveProcessedImage(
            $file->getRealPath(),
            $originalPath,
            null,
            null
        );

        // favicon 16x16
        $favicon16Path = sys_get_temp_dir() . "/{$baseName}_16.webp";
        $this->saveProcessedImage(
            $file->getRealPath(),
            $favicon16Path,
            ImageSizes::FAVICON_16,
            ImageSizes::FAVICON_16
        );

        // favicon 32x32
        $favicon32Path = sys_get_temp_dir() . "/{$baseName}_32.webp";
        $this->saveProcessedImage(
            $file->getRealPath(),
            $favicon32Path,
            ImageSizes::FAVICON_32,
            ImageSizes::FAVICON_32
        );

        // Apple Touch Icon 180x180
        $appleTouchIconPath = sys_get_temp_dir() . "/{$baseName}_180.webp";
        $this->saveProcessedImage(
            $file->getRealPath(),
            $appleTouchIconPath,
            ImageSizes::FAVICON_180,
            ImageSizes::FAVICON_180
        );

        return [
            'original' => $originalPath,
            '16'       => $favicon16Path,
            '32'       => $favicon32Path,
            '180'      => $appleTouchIconPath,
        ];
    }


    protected function saveOriginalImage(string $sourcePath, string $destinationPath): void
    {
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
    ): void {

        // poster
        $this->saveProcessedImage(
            $originalPath,
            $posterPath,
            $posterWidth,
            $posterHeight
        );

        // thumb
        $this->saveProcessedImage(
            $originalPath,
            $thumbPath,
            $thumbWidth,
            $thumbHeight
        );

        // search
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
