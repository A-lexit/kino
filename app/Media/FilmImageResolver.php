<?php
namespace App\Media;

use App\Models\Film;

class FilmImageResolver
{
    public function __construct(
        protected ImageMedia $imageMedia
    ) {
    }

    public function image(Film $film): string
    {
        if (empty($film->thumbnail)) {
            return $this->defaultImage('poster');
        }

        return $this->resolve(
            $this->variant($film->thumbnail, 'poster'),
            $film->thumbnail,
            'poster'
        );
    }

    public function thumb(Film $film): string
    {
        if (empty($film->thumbnail)) {
            return $this->defaultImage('thumb');
        }

        return $this->resolve(
            $this->variant($film->thumbnail, 'thumb'),
            $film->thumbnail,
            'thumb'
        );
    }

    public function search(Film $film): string
    {
        if (empty($film->thumbnail)) {
            return $this->defaultImage('search');
        }

        return $this->resolve(
            $this->variant($film->thumbnail, 'search'),
            $film->thumbnail,
            'search'
        );
    }

    public function original(Film $film): string
    {
        if (empty($film->thumbnail)) {
            return $this->defaultImage();
        }

        return $this->resolve(
            $this->variant($film->thumbnail, 'original'),
            $film->thumbnail
        );
    }


    public function largeThumb(Film $film): string
    {
        if (empty($film->thumbnail)) {
            return $this->defaultImage('large-thumb');
        }

        return $this->resolve(
            $this->variant($film->thumbnail, 'large-thumb'),
            $film->thumbnail,
            'large-thumb'
        );
    }

    public function gallery(Film $film): array
    {
        $fields = ['gal_image1', 'gal_image2', 'gal_image3', 'gal_image4', 'gal_image5'];

        return collect($fields)
            ->map(function (string $field, int $i) use ($film) {
                if (empty($film->{$field})) {
                    return null;
                }

                $original = $film->{$field};

                return [
                    'src'   => $this->resolve($this->variant($original, 'gallery'), $original, 'gallery'),
                    'thumb' => $this->resolve($this->variant($original, 'gallery-thumb'), $original, 'gallery-thumb'),
                    'title' => 'Кадр ' . ($i + 1),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function variant(string $path, string $suffix): string
    {
        return preg_replace('/\.webp$/i', "-{$suffix}.webp", $path);
    }

    /**
     * Повертає URL картинки.
     * Якщо потрібна мініатюра не існує — повертає fallback.
     */
    protected function resolve(string $path, ?string $fallback = null, ?string $variant = null): string
    {
        if (empty($path)) {
            return $this->defaultImage($variant);
        }

        // Повний URL
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // Якщо файл існує
        if ($this->imageMedia->exists($path)) {
            return $this->imageMedia->url($path);
        }

        // fallback (оригінал)
        if ($fallback && $this->imageMedia->exists($fallback)) {
            return $this->imageMedia->url($fallback);
        }

        return $this->defaultImage($variant);
    }

    /**
     * Повертає дефолтне зображення відповідно до варіанта (thumb/poster/search/gallery).
     */
    protected function defaultImage(?string $variant = null): string
    {
        if ($variant) {
            $variantPath = "defaults/fake_movie_cover-{$variant}.webp";

            if (file_exists(public_path($variantPath))) {
                return asset($variantPath);
            }
        }

        return asset('defaults/fake_movie_cover.webp');
    }
}
