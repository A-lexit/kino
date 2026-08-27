<?php
namespace App\Console\Commands;

use App\Constants\ImageSizes;
use App\Media\ImageConverter;
use Illuminate\Console\Command;

class GenerateDefaultImages extends Command
{
    protected $signature = 'media:generate-default-images';

    protected $description = 'Generate poster/thumb/search/gallery versions for default image';

    public function __construct(
        protected ImageConverter $imageConverter
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $source = public_path('defaults/fake_movie_cover.webp');

        if (! file_exists($source)) {
            $this->error("Файл не знайдено: {$source}");

            return self::FAILURE;
        }

        $this->info('Генеруємо poster/thumb/search...');

        $this->imageConverter->regenerateImageSet(
            originalPath: $source,

            posterPath: public_path('defaults/fake_movie_cover-poster.webp'),
            posterWidth: ImageSizes::POSTER_WIDTH,
            posterHeight: ImageSizes::POSTER_HEIGHT,

            thumbPath: public_path('defaults/fake_movie_cover-thumb.webp'),
            thumbWidth: ImageSizes::POSTER_THUMB_WIDTH,
            thumbHeight: ImageSizes::POSTER_THUMB_HEIGHT,

            searchPath: public_path('defaults/fake_movie_cover-search.webp'),
            searchWidth: ImageSizes::SEARCH_WIDTH,
            searchHeight: ImageSizes::SEARCH_HEIGHT,
        );

        $this->info('Генеруємо gallery...');

        $this->imageConverter->regenerateImageSet(
            originalPath: $source,

            posterPath: public_path('defaults/fake_movie_cover-gallery.webp'),
            posterWidth: ImageSizes::GALLERY_WIDTH,
            posterHeight: ImageSizes::GALLERY_HEIGHT,

            thumbPath: public_path('defaults/fake_movie_cover-gallery-thumb.webp'),
            thumbWidth: ImageSizes::GALLERY_THUMB_WIDTH,
            thumbHeight: ImageSizes::GALLERY_THUMB_HEIGHT,
        );

        $this->newLine();
        $this->info('✓ Усі варіанти успішно створено.');

        return self::SUCCESS;
    }
}
