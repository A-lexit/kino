<?php

namespace App\Media;

use Illuminate\Http\UploadedFile;

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
        $tempPath = $this->converter->convertToWebp($file, $width, $height, $titleSlug);

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
        ?string $titleSlug = null,
        string $posterSuffix = 'poster',
        string $thumbSuffix = 'thumb',
        string $searchSuffix = 'search',
    ): array {
        $paths = $this->converter->convertWithThumbnail(
            $file,
            $origWidth,
            $origHeight,
            $thumbWidth,
            $thumbHeight,
            $searchWidth,
            $searchHeight,
            $titleSlug,
            $posterSuffix,
            $thumbSuffix,
            $searchSuffix,
        );

        $result = [
            'original' => $this->storage->store($paths['original'], $folder),
            'poster'   => $this->storage->store($paths['poster'], $folder),
            'thumb'    => $this->storage->store($paths['thumb'], $folder),
        ];

        if (isset($paths['search'])) {
            $result['search'] = $this->storage->store($paths['search'], $folder);
        }

        return $result;
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


    public function uploadFavicon(
        UploadedFile $file,
        string $folder,
        ?string $titleSlug = 'favicon'
    ): array {
        $paths = $this->converter->convertFavicon(
            $file,
            $titleSlug
        );

        return [
            'original' => $this->storage->store($paths['original'], $folder),
            '16'       => $this->storage->store($paths['16'], $folder),
            '32'       => $this->storage->store($paths['32'], $folder),
            '180'      => $this->storage->store($paths['180'], $folder),
        ];
    }

}
