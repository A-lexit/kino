<?php
namespace Tests\Feature\Media;

use App\Constants\ImageSizes;
use App\Media\FilmImageMedia;
use App\Media\ImageConverter;
use App\Media\ImageMedia;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class FilmImageMediaTest extends TestCase
{
    protected ImageMedia $imageMedia;
    protected ImageConverter $imageConverterMock;
    protected FilmImageMedia $service;


    protected function setUp(): void
    {
        parent::setUp();

        $this->imageMedia = Mockery::mock(ImageMedia::class);

        $this->imageConverterMock = Mockery::mock(ImageConverter::class);

        $this->service = new FilmImageMedia(
            $this->imageMedia,
            $this->imageConverterMock
        );
    }


    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    protected function fakeRequest(array $files = []): Request
    {
        return Request::create(
            '/',
            'POST',
            [],
            [],
            $files
        );
    }


    public function test_upload_thumbnail_image(): void
    {
        $file = UploadedFile::fake()->image('poster.jpg');

        $request = $this->fakeRequest([
            'thumbnail' => $file,
        ]);

        $data = [
            'slug' => 'avatar-film',
        ];

        $this->imageMedia
            ->shouldReceive('uploadWithThumbnail')
            ->once()
            ->with(
                $file,
                Mockery::type('string'),
                ImageSizes::POSTER_WIDTH,
                ImageSizes::POSTER_HEIGHT,
                ImageSizes::POSTER_THUMB_WIDTH,
                ImageSizes::POSTER_THUMB_HEIGHT,
                ImageSizes::SEARCH_WIDTH,
                ImageSizes::SEARCH_HEIGHT,
                'avatar-film',
                'poster',
                'thumb',
                'search'
            )
            ->andReturn([
                'original' => 'images/poster.webp',
                'poster'   => 'images/poster-poster.webp',
                'thumb'    => 'images/poster-thumb.webp',
                'search'   => 'images/poster-search.webp',
            ]);

        $this->service->uploadFilmImages(
            $request,
            $data,
            null,
            null
        );

        $this->assertEquals(
            'images/poster.webp',
            $data['thumbnail']
        );
    }


    public function test_upload_single_gallery_image(): void
    {
        $file = UploadedFile::fake()->image('gallery.jpg');

        $request = $this->fakeRequest([
            'gal_image1' => $file,
        ]);

        $data = [
            'slug' => 'my-film',
        ];

        $this->imageMedia
            ->shouldReceive('uploadWithThumbnail')
            ->once()
            ->with(
                $file,
                Mockery::type('string'),
                ImageSizes::GALLERY_WIDTH,
                ImageSizes::GALLERY_HEIGHT,
                ImageSizes::GALLERY_THUMB_WIDTH,
                ImageSizes::GALLERY_THUMB_HEIGHT,
                null,
                null,
                'my-film-gal_image1',
                'gallery',
                'gallery-thumb'
            )
            ->andReturn([
                'original' => 'images/gallery1.webp',
                'poster'   => 'images/gallery1-gallery.webp',
                'thumb'    => 'images/gallery1-gallery-thumb.webp',
            ]);

        $this->service->uploadFilmImages(
            $request,
            $data
        );

        $this->assertEquals(
            'images/gallery1.webp',
            $data['gal_image1']
        );
    }


    public function test_upload_multiple_gallery_images(): void
    {
        $request = $this->fakeRequest([
            'gal_image1' => UploadedFile::fake()->image('1.jpg'),
            'gal_image2' => UploadedFile::fake()->image('2.jpg'),
            'gal_image3' => UploadedFile::fake()->image('3.jpg'),
        ]);

        $data = [
            'slug' => 'matrix',
        ];

        $this->imageMedia
            ->shouldReceive('uploadWithThumbnail')
            ->times(3)
            ->andReturnUsing(function (
                UploadedFile $file,
                string $folder,
                int $width,
                int $height,
                int $thumbWidth,
                int $thumbHeight,
                ?int $searchWidth,
                ?int $searchHeight,
                ?string $slug,
                string $posterSuffix,
                string $thumbSuffix
            ) {
                return [
                    'original' => "{$slug}.webp",
                    'poster'   => "{$slug}-gallery.webp",
                    'thumb'    => "{$slug}-gallery-thumb.webp",
                ];
            });

        $this->service->uploadFilmImages(
            $request,
            $data
        );

        $this->assertEquals(
            'matrix-gal_image1.webp',
            $data['gal_image1']
        );

        $this->assertEquals(
            'matrix-gal_image2.webp',
            $data['gal_image2']
        );

        $this->assertEquals(
            'matrix-gal_image3.webp',
            $data['gal_image3']
        );

        $this->assertArrayNotHasKey('gal_image4', $data);
        $this->assertArrayNotHasKey('gal_image5', $data);
    }


    public function test_upload_thumbnail_deletes_old_images_before_upload(): void
    {
        $file = UploadedFile::fake()->image('new-poster.jpg');

        $request = $this->fakeRequest([
            'thumbnail' => $file,
        ]);

        $film = new Film([
            'thumbnail' => 'images/old-poster.webp',
        ]);

        $data = [
            'slug' => 'new-film',
        ];

        $this->imageMedia
            ->shouldReceive('delete')
            ->times(6)
            ->withAnyArgs();

        $this->imageMedia
            ->shouldReceive('uploadWithThumbnail')
            ->once()
            ->andReturn([
                'original' => 'images/new-poster.webp',
                'poster'   => 'images/new-poster-poster.webp',
                'thumb'    => 'images/new-poster-thumb.webp',
                'search'   => 'images/new-poster-search.webp',
            ]);

        $this->service->uploadFilmImages(
            $request,
            $data,
            $film
        );

        $this->assertEquals(
            'images/new-poster.webp',
            $data['thumbnail']
        );
    }


    public function test_upload_gallery_deletes_old_gallery_images_before_upload(): void
    {
        $file = UploadedFile::fake()->image('new-gallery.jpg');

        $request = $this->fakeRequest([
            'gal_image1' => $file,
        ]);

        $film = new Film([
            'gal_image1' => 'images/old-gallery.webp',
        ]);

        $data = [
            'slug' => 'new-film',
        ];

        $this->imageMedia
            ->shouldReceive('delete')
            ->times(6)
            ->withAnyArgs();

        $this->imageMedia
            ->shouldReceive('uploadWithThumbnail')
            ->once()
            ->andReturn([
                'original' => 'images/new-gallery.webp',
                'poster'   => 'images/new-gallery-gallery.webp',
                'thumb'    => 'images/new-gallery-gallery-thumb.webp',
            ]);

        $this->service->uploadFilmImages(
            $request,
            $data,
            $film
        );

        $this->assertEquals(
            'images/new-gallery.webp',
            $data['gal_image1']
        );
    }


    /*
|--------------------------------------------------------------------------
| Upgrade images
|--------------------------------------------------------------------------
*/
    public function test_upgrade_creates_thumbnail_variants(): void
    {
        Storage::fake('public');

        $film = new Film([
            'thumbnail' => 'images/2026-01-01/test.webp',
        ]);

        Storage::disk('public')->put(
            $film->thumbnail,
            'fake-image'
        );

        $this->imageConverterMock
            ->expects('regenerateImageSet')
            ->once();

        $this->service->upgrade($film);

        $this->assertTrue(true);
    }


    public function test_upgrade_skips_thumbnail_when_original_file_missing(): void
    {
        Storage::fake('public');

        $film = new Film([
            'thumbnail' => 'images/2026-01-01/not-found.webp',
        ]);

        $this->imageConverterMock
            ->expects('regenerateImageSet')
            ->never();

        $this->service->upgrade($film);

        $this->assertTrue(true);
    }


    public function test_upgrade_skips_thumbnail_when_variants_exist(): void
    {
        Storage::fake('public');

        $film = new Film([
            'thumbnail' => 'images/2026-01-01/movie.webp',
        ]);

        Storage::disk('public')->put(
            'images/2026-01-01/movie.webp',
            'original'
        );

        Storage::disk('public')->put(
            'images/2026-01-01/movie-poster.webp',
            'poster'
        );

        Storage::disk('public')->put(
            'images/2026-01-01/movie-thumb.webp',
            'thumb'
        );

        Storage::disk('public')->put(
            'images/2026-01-01/movie-search.webp',
            'search'
        );

        $this->imageConverterMock
            ->expects('regenerateImageSet')
            ->never();

        $this->service->upgrade($film);

        $this->assertTrue(true);
    }


    public function test_upgrade_gallery_creates_gallery_variants(): void
    {
        Storage::fake('public');

        $film = new Film([
            'gal_image1' => 'images/2026-01-01/gallery.webp',
        ]);

        Storage::disk('public')->put(
            $film->gal_image1,
            'gallery-image'
        );

        $this->imageConverterMock
            ->expects('regenerateImageSet')
            ->once();

        $this->service->upgrade($film);

        $this->assertTrue(true);
    }


    public function test_upgrade_ignores_empty_gallery_images(): void
    {
        Storage::fake('public');

        $film = new Film([
            'gal_image1' => null,
            'gal_image2' => null,
            'gal_image3' => null,
            'gal_image4' => null,
            'gal_image5' => null,
        ]);


        $this->imageConverterMock
            ->expects('regenerateImageSet')
            ->never();

        $this->service->upgrade($film);

        $this->assertTrue(true);
    }
}

