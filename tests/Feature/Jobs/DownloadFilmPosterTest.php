<?php

namespace Tests\Feature\Jobs;

use App\Jobs\DownloadFilmPoster;
use App\Media\ImageMedia;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery\MockInterface;
use Tests\TestCase;

class DownloadFilmPosterTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_downloads_poster_and_updates_film(): void
    {
        $film = Film::factory()->create([
            'thumbnail' => null,
        ]);

        Http::fake([
            'https://image.tmdb.org/t/p/original/test-poster.jpg' =>
                Http::response('fake-image-content', 200),
        ]);

        Log::spy();

        $this->mock(ImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('upload')
                ->once()
                ->withArgs(function ($file, $directory) {
                    return $file instanceof \Illuminate\Http\UploadedFile
                        && $directory === 'images/' . now()->format('Y-m-d');
                })
                ->andReturn('images/2026-08-22/test-poster.webp');
        });

        $job = new DownloadFilmPoster(
            $film->id,
            '/test-poster.jpg'
        );

        $job->handle(
            app(ImageMedia::class)
        );

        $film->refresh();

        $this->assertSame(
            'images/2026-08-22/test-poster.webp',
            $film->thumbnail
        );

        Http::assertSent(function ($request) {
            return $request->url() ===
                'https://image.tmdb.org/t/p/original/test-poster.jpg';
        });
    }

    public function test_handle_does_nothing_when_film_does_not_exist(): void
    {
        Http::fake();

        $imageMedia = $this->mock(ImageMedia::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('upload');
        });

        $job = new DownloadFilmPoster(
            999999,
            '/test-poster.jpg'
        );

        $job->handle($imageMedia);

        Http::assertNothingSent();
    }

    public function test_handle_throws_when_tmdb_returns_error(): void
    {
        $film = Film::factory()->create([
            'thumbnail' => null,
        ]);

        Http::fake([
            'https://image.tmdb.org/t/p/original/test-poster.jpg' =>
                Http::response([], 404),
        ]);

        Log::spy();

        $imageMedia = $this->mock(ImageMedia::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('upload');
        });

        $job = new DownloadFilmPoster(
            $film->id,
            '/test-poster.jpg'
        );

        $this->expectException(\Illuminate\Http\Client\RequestException::class);

        $job->handle($imageMedia);

        $film->refresh();

        $this->assertNull($film->thumbnail);
    }

    public function test_handle_throws_when_tmdb_returns_empty_image(): void
    {
        $film = Film::factory()->create([
            'thumbnail' => null,
        ]);

        Http::fake([
            'https://image.tmdb.org/t/p/original/test-poster.jpg' =>
                Http::response('', 200),
        ]);

        $imageMedia = $this->mock(ImageMedia::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('upload');
        });

        $job = new DownloadFilmPoster(
            $film->id,
            '/test-poster.jpg'
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'TMDB повернув порожнє зображення.'
        );

        $job->handle($imageMedia);

        $film->refresh();

        $this->assertNull($film->thumbnail);
    }
}
