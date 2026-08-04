<?php

namespace App\Media;

use App\Models\Film;
use Illuminate\Http\Request;
use App\Constants\ImageSizes;
use Illuminate\Support\Facades\Storage;

class FilmImageMedia
{
    public function __construct(
        protected ImageMedia $imageMedia,
        protected ImageConverter $imageConverter
    ) {}

    public function uploadFilmImages(
        Request $request,
        array &$data,
        ?Film $film = null,
        ?string $titleSlug = null
    ): void {
        $slug = $titleSlug ?? $data['slug'] ?? $film?->slug ?? null;
        $folder = 'images/' . date('Y-m-d');

        if ($request->hasFile('thumbnail')) {
            if ($film && $film->thumbnail) {
                $this->deleteImageAndThumbs($film->thumbnail);
            }
            $uploaded = $this->imageMedia->uploadWithThumbnail(
                $request->file('thumbnail'),
                folder: $folder,
                origWidth: ImageSizes::POSTER_WIDTH,
                origHeight: ImageSizes::POSTER_HEIGHT,
                thumbWidth: ImageSizes::POSTER_THUMB_WIDTH,
                thumbHeight: ImageSizes::POSTER_THUMB_HEIGHT,
                searchWidth: ImageSizes::SEARCH_WIDTH,
                searchHeight: ImageSizes::SEARCH_HEIGHT,
                titleSlug: $slug,
                posterSuffix: 'poster',
                thumbSuffix: 'thumb',
                searchSuffix: 'search',
            );
            $data['thumbnail'] = $uploaded['original'];
        }

        $galleryFields = [
            'gal_image1',
            'gal_image2',
            'gal_image3',
            'gal_image4',
            'gal_image5',
        ];

        foreach ($galleryFields as $field) {
            if (!$request->hasFile($field)) {
                continue;
            }
            if ($film && $film->$field) {
                $this->deleteImageAndThumbs($film->$field);
            }
            $uploaded = $this->imageMedia->uploadWithThumbnail(
                $request->file($field),
                folder: $folder,
                origWidth: ImageSizes::GALLERY_WIDTH,
                origHeight: ImageSizes::GALLERY_HEIGHT,
                thumbWidth: ImageSizes::GALLERY_THUMB_WIDTH,
                thumbHeight: ImageSizes::GALLERY_THUMB_HEIGHT,
                searchWidth: null,
                searchHeight: null,
                titleSlug: $slug ? "{$slug}-{$field}" : null,
                posterSuffix: 'gallery',
                thumbSuffix: 'gallery-thumb',
            );
            $data[$field] = $uploaded['original'];
        }
    }

    public function deleteFilmImages(Film $film): void
    {
        $fields = [
            'thumbnail',
            'gal_image1',
            'gal_image2',
            'gal_image3',
            'gal_image4',
            'gal_image5',
        ];

        foreach ($fields as $field) {
            if ($film->$field) {
                $this->deleteImageAndThumbs($film->$field);
            }
        }
    }

    protected function deleteImageAndThumbs(string $path): void
    {
        $this->imageMedia->delete($path);

        // Постер-варіанти (для thumbnail)
        $this->imageMedia->delete(preg_replace('/\.webp$/i', '-poster.webp', $path));
        $this->imageMedia->delete(preg_replace('/\.webp$/i', '-thumb.webp', $path));
        $this->imageMedia->delete(preg_replace('/\.webp$/i', '-search.webp', $path));

        // Галерейні варіанти (для gal_image1..5)
        $this->imageMedia->delete(preg_replace('/\.webp$/i', '-gallery.webp', $path));
        $this->imageMedia->delete(preg_replace('/\.webp$/i', '-gallery-thumb.webp', $path));
    }

    /**
     * Апгрейд основного зображення фільму (постер/мініатюра/пошук)
     * та всіх зображень галереї до нової структури варіантів.
     */
    public function upgrade(Film $film): void
    {
        $this->upgradeThumbnail($film);
        $this->upgradeGallery($film);
    }

    protected function upgradeThumbnail(Film $film): void
    {
        if (!$film->thumbnail) {
            return;
        }

        $original = Storage::disk('public')->path($film->thumbnail);

        if (!file_exists($original)) {
            return;
        }

        $poster = preg_replace('/\.webp$/i', '-poster.webp', $original);
        $thumb  = preg_replace('/\.webp$/i', '-thumb.webp', $original);
        $search = preg_replace('/\.webp$/i', '-search.webp', $original);

        if (file_exists($poster) && file_exists($thumb) && file_exists($search)) {
            return;
        }

        $this->imageConverter->regenerateImageSet(
            originalPath: $original,
            posterPath: $poster,
            posterWidth: ImageSizes::POSTER_WIDTH,
            posterHeight: ImageSizes::POSTER_HEIGHT,
            thumbPath: $thumb,
            thumbWidth: ImageSizes::POSTER_THUMB_WIDTH,
            thumbHeight: ImageSizes::POSTER_THUMB_HEIGHT,
        );
    }

    protected function upgradeGallery(Film $film): void
    {
        $fields = ['gal_image1', 'gal_image2', 'gal_image3', 'gal_image4', 'gal_image5'];

        foreach ($fields as $field) {
            if (empty($film->{$field})) {
                continue;
            }

            $this->upgradeGalleryImage($film->{$field});
        }
    }

    protected function upgradeGalleryImage(string $relativePath): void
    {
        $original = Storage::disk('public')->path($relativePath);

        if (!file_exists($original)) {
            return;
        }

        $gallery      = preg_replace('/\.webp$/i', '-gallery.webp', $original);
        $galleryThumb = preg_replace('/\.webp$/i', '-gallery-thumb.webp', $original);

        if (file_exists($gallery) && file_exists($galleryThumb)) {
            return;
        }

        $this->imageConverter->regenerateImageSet(
            originalPath: $original,
            posterPath: $gallery,
            posterWidth: ImageSizes::GALLERY_WIDTH,
            posterHeight: ImageSizes::GALLERY_HEIGHT,
            thumbPath: $galleryThumb,
            thumbWidth: ImageSizes::GALLERY_THUMB_WIDTH,
            thumbHeight: ImageSizes::GALLERY_THUMB_HEIGHT,
            searchPath: $galleryThumb,
            searchWidth: ImageSizes::GALLERY_THUMB_WIDTH,
            searchHeight: ImageSizes::GALLERY_THUMB_HEIGHT,
        );
    }

}
