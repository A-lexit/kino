<?php

namespace Tests\Feature\Media;

use App\Media\ImageConverter;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageConverterTest extends TestCase
{
    protected ImageConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->converter = new ImageConverter();
    }

    public function test_convert_to_webp_creates_webp_file(): void
    {
        $file = UploadedFile::fake()->image(
            'test.jpg',
            1200,
            800
        );

        $path = $this->converter->convertToWebp(
            $file,
            null,
            null,
            'Test Film'
        );

        try {
            $this->assertFileExists($path);

            $this->assertSame(
                'webp',
                strtolower(pathinfo($path, PATHINFO_EXTENSION))
            );

            $this->assertStringContainsString(
                'test-film-',
                basename($path)
            );

            $this->assertNotFalse(getimagesize($path));
        } finally {
            $this->deleteFile($path);
        }
    }

    public function test_convert_with_thumbnail_creates_original_poster_and_thumb(): void
    {
        $file = UploadedFile::fake()->image(
            'test.jpg',
            1200,
            800
        );

        $paths = $this->converter->convertWithThumbnail(
            $file,
            300,
            200,
            150,
            100,
            null,
            null,
            'Test Film'
        );

        try {
            $this->assertCount(3, $paths);

            $this->assertArrayHasKey('original', $paths);
            $this->assertArrayHasKey('poster', $paths);
            $this->assertArrayHasKey('thumb', $paths);

            foreach ($paths as $path) {
                $this->assertFileExists($path);

                $this->assertSame(
                    'webp',
                    strtolower(pathinfo($path, PATHINFO_EXTENSION))
                );
            }

            $this->assertImageFitsWithin(
                $paths['poster'],
                300,
                200
            );

            $this->assertImageFitsWithin(
                $paths['thumb'],
                150,
                100
            );
        } finally {
            $this->deleteFiles($paths);
        }
    }

    public function test_convert_with_thumbnail_creates_search_image_when_dimensions_are_given(): void
    {
        $file = UploadedFile::fake()->image(
            'test.jpg',
            1200,
            800
        );

        $paths = $this->converter->convertWithThumbnail(
            $file,
            300,
            200,
            150,
            100,
            80,
            60,
            'Test Film'
        );

        try {
            $this->assertCount(4, $paths);

            $this->assertArrayHasKey('search', $paths);

            $this->assertFileExists($paths['search']);

            $this->assertSame(
                'webp',
                strtolower(pathinfo($paths['search'], PATHINFO_EXTENSION))
            );

            $this->assertImageFitsWithin(
                $paths['search'],
                80,
                60
            );
        } finally {
            $this->deleteFiles($paths);
        }
    }

    public function test_convert_favicon_creates_all_required_files(): void
    {
        $file = UploadedFile::fake()->image(
            'favicon.jpg',
            500,
            500
        );

        $paths = $this->converter->convertFavicon(
            $file,
            'site favicon'
        );

        try {
            $this->assertCount(4, $paths);

            $this->assertArrayHasKey('original', $paths);
            $this->assertArrayHasKey('16', $paths);
            $this->assertArrayHasKey('32', $paths);
            $this->assertArrayHasKey('180', $paths);

            foreach ($paths as $path) {
                $this->assertFileExists($path);

                $this->assertSame(
                    'webp',
                    strtolower(pathinfo($path, PATHINFO_EXTENSION))
                );
            }

            $this->assertImageDimensions(
                $paths['16'],
                16,
                16
            );

            $this->assertImageDimensions(
                $paths['32'],
                32,
                32
            );

            $this->assertImageDimensions(
                $paths['180'],
                180,
                180
            );
        } finally {
            $this->deleteFiles($paths);
        }
    }

    public function test_regenerate_image_set_creates_poster_thumbnail_and_search(): void
    {
        $file = UploadedFile::fake()->image(
            'original.jpg',
            1200,
            800
        );

        $originalPath = $this->converter->convertToWebp(
            $file,
            null,
            null,
            'Original'
        );

        $posterPath = sys_get_temp_dir()
            . '/test-regenerated-poster.webp';

        $thumbPath = sys_get_temp_dir()
            . '/test-regenerated-thumb.webp';

        $searchPath = sys_get_temp_dir()
            . '/test-regenerated-search.webp';

        try {
            $this->converter->regenerateImageSet(
                $originalPath,
                $posterPath,
                300,
                200,
                $thumbPath,
                150,
                100,
                $searchPath,
                80,
                60
            );

            $this->assertFileExists($posterPath);
            $this->assertFileExists($thumbPath);
            $this->assertFileExists($searchPath);

            $this->assertImageFitsWithin(
                $posterPath,
                300,
                200
            );

            $this->assertImageFitsWithin(
                $thumbPath,
                150,
                100
            );

            $this->assertImageFitsWithin(
                $searchPath,
                80,
                60
            );
        } finally {
            $this->deleteFile($originalPath);
            $this->deleteFile($posterPath);
            $this->deleteFile($thumbPath);
            $this->deleteFile($searchPath);
        }
    }

    protected function assertImageFitsWithin(
        string $path,
        int $maxWidth,
        int $maxHeight
    ): void {
        $size = getimagesize($path);

        $this->assertNotFalse(
            $size,
            "Не вдалося отримати розміри зображення: {$path}"
        );

        $this->assertLessThanOrEqual(
            $maxWidth,
            $size[0],
            "Ширина зображення перевищує {$maxWidth}: {$path}"
        );

        $this->assertLessThanOrEqual(
            $maxHeight,
            $size[1],
            "Висота зображення перевищує {$maxHeight}: {$path}"
        );
    }

    protected function assertImageDimensions(
        string $path,
        int $expectedWidth,
        int $expectedHeight
    ): void {
        $size = getimagesize($path);

        $this->assertNotFalse(
            $size,
            "Не вдалося отримати розміри зображення: {$path}"
        );

        $this->assertSame(
            $expectedWidth,
            $size[0],
            "Неправильна ширина зображення: {$path}"
        );

        $this->assertSame(
            $expectedHeight,
            $size[1],
            "Неправильна висота зображення: {$path}"
        );
    }

    protected function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            $this->deleteFile($path);
        }
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && is_file($path)) {
            @unlink($path);
        }
    }
}
