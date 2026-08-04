<?php
namespace Tests\Feature\Media;

use App\Media\FilmImageResolver;
use App\Media\ImageMedia;
use App\Models\Film;
use Mockery;
use Tests\TestCase;

class FilmImageResolverTest extends TestCase
{
    protected ImageMedia $imageMedia;
    protected FilmImageResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->imageMedia = Mockery::mock(ImageMedia::class);

        $this->resolver = new FilmImageResolver(
            $this->imageMedia
        );
    }


    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }


    public function test_image_returns_poster_variant(): void
    {
        $film = new Film([
            'thumbnail' => 'images/poster.webp',
        ]);

        $this->imageMedia
            ->shouldReceive('exists')
            ->once()
            ->with('images/poster-poster.webp')
            ->andReturn(true);

        $this->imageMedia
            ->shouldReceive('url')
            ->once()
            ->with('images/poster-poster.webp')
            ->andReturn('http://site/images/poster-poster.webp');

        $this->assertEquals(
            'http://site/images/poster-poster.webp',
            $this->resolver->image($film)
        );
    }


    public function test_thumb_returns_thumb_variant(): void
    {
        $film = new Film([
            'thumbnail' => 'images/poster.webp',
        ]);

        $this->imageMedia
            ->shouldReceive('exists')
            ->once()
            ->with('images/poster-thumb.webp')
            ->andReturn(true);

        $this->imageMedia
            ->shouldReceive('url')
            ->once()
            ->with('images/poster-thumb.webp')
            ->andReturn('http://site/images/poster-thumb.webp');

        $this->assertEquals(
            'http://site/images/poster-thumb.webp',
            $this->resolver->thumb($film)
        );
    }


    public function test_search_returns_search_variant(): void
    {
        $film = new Film([
            'thumbnail' => 'images/poster.webp',
        ]);

        $this->imageMedia
            ->shouldReceive('exists')
            ->once()
            ->with('images/poster-search.webp')
            ->andReturn(true);

        $this->imageMedia
            ->shouldReceive('url')
            ->once()
            ->with('images/poster-search.webp')
            ->andReturn('http://site/images/poster-search.webp');

        $this->assertEquals(
            'http://site/images/poster-search.webp',
            $this->resolver->search($film)
        );
    }


    public function test_original_returns_original_variant(): void
    {
        $film = new Film([
            'thumbnail' => 'images/poster.webp',
        ]);

        $this->imageMedia
            ->shouldReceive('exists')
            ->once()
            ->with('images/poster-original.webp')
            ->andReturn(true);

        $this->imageMedia
            ->shouldReceive('url')
            ->once()
            ->with('images/poster-original.webp')
            ->andReturn('http://site/images/poster-original.webp');

        $this->assertEquals(
            'http://site/images/poster-original.webp',
            $this->resolver->original($film)
        );
    }


    public function test_returns_default_when_thumbnail_missing(): void
    {
        $film = new Film([
            'thumbnail' => null,
        ]);

        $default = asset('defaults/fake_movie_cover.webp');

        $this->assertEquals(
            asset('defaults/fake_movie_cover-thumb.webp'),
            $this->resolver->thumb($film)
        );

        $this->assertEquals(
            asset('defaults/fake_movie_cover-search.webp'),
            $this->resolver->search($film)
        );
    }


    public function test_fallbacks_to_original_when_variant_missing(): void
    {
        $film = new Film([
            'thumbnail' => 'images/poster.webp',
        ]);

        $this->imageMedia
            ->shouldReceive('exists')
            ->once()
            ->with('images/poster-thumb.webp')
            ->andReturn(false);

        $this->imageMedia
            ->shouldReceive('exists')
            ->once()
            ->with('images/poster.webp')
            ->andReturn(true);

        $this->imageMedia
            ->shouldReceive('url')
            ->once()
            ->with('images/poster.webp')
            ->andReturn('http://site/images/poster.webp');

        $this->assertEquals(
            'http://site/images/poster.webp',
            $this->resolver->thumb($film)
        );
    }


    public function test_returns_default_when_nothing_exists(): void
    {
        $film = new Film([
            'thumbnail' => 'images/poster.webp',
        ]);

        $this->imageMedia
            ->shouldReceive('exists')
            ->twice()
            ->andReturn(false);

        $this->assertEquals(
            asset('defaults/fake_movie_cover-thumb.webp'),
            $this->resolver->thumb($film)
        );
    }


    public function test_gallery_returns_all_existing_images(): void
    {
        $film = new Film([
            'gal_image1' => 'images/1.webp',
            'gal_image2' => 'images/2.webp',
        ]);

        $this->imageMedia
            ->shouldReceive('exists')
            ->times(4)
            ->andReturn(true);

        $this->imageMedia
            ->shouldReceive('url')
            ->times(4)
            ->andReturnUsing(fn ($path) => "http://site/{$path}");

        $gallery = $this->resolver->gallery($film);

        $this->assertCount(2, $gallery);

        $this->assertEquals(
            'http://site/images/1-gallery.webp',
            $gallery[0]['src']
        );

        $this->assertEquals(
            'http://site/images/1-gallery-thumb.webp',
            $gallery[0]['thumb']
        );

        $this->assertEquals(
            'Кадр 1',
            $gallery[0]['title']
        );
    }


    public function test_gallery_skips_empty_images(): void
    {
        $film = new Film([
            'gal_image1' => null,
            'gal_image2' => null,
            'gal_image3' => 'images/3.webp',
        ]);

        $this->imageMedia
            ->shouldReceive('exists')
            ->twice()
            ->andReturn(true);

        $this->imageMedia
            ->shouldReceive('url')
            ->twice()
            ->andReturnUsing(fn ($path) => "http://site/{$path}");

        $gallery = $this->resolver->gallery($film);

        $this->assertCount(1, $gallery);

        $this->assertEquals(
            'Кадр 3',
            $gallery[0]['title']
        );
    }

}
