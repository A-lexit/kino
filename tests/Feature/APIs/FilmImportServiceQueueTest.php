<?php

namespace Tests\Feature\APIs;

use App\APIs\FilmImportService;
use App\Jobs\DownloadFilmPoster;
use App\Jobs\SendFilmToTelegram;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FilmImportServiceQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_chains_poster_download_and_telegram_jobs(): void
    {
        Bus::fake();

        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'id' => 12345,
                'title' => 'Test Film',
                'original_title' => 'Test Film Original',
                'overview' => 'Test description',
                'poster_path' => '/test-poster.jpg',
                'release_date' => '2026-08-03',
            ], 200),
        ]);

        $film = app(FilmImportService::class)->import(12345);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'tmdb_id' => 12345,
            'title' => 'Test Film',
            'thumbnail' => null,
        ]);

        Bus::assertChained([
            new DownloadFilmPoster(
                $film->id,
                '/test-poster.jpg'
            ),
            new SendFilmToTelegram(
                $film->id
            ),
        ]);
    }

    public function test_import_dispatches_telegram_job_directly_when_there_is_no_poster(): void
    {
        Bus::fake();

        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'id' => 12346,
                'title' => 'Film Without Poster',
                'original_title' => 'Film Without Poster',
                'overview' => 'Description',
                'poster_path' => null,
                'release_date' => '2026-08-03',
            ], 200),
        ]);

        $film = app(FilmImportService::class)->import(12346);

        Bus::assertDispatched(
            SendFilmToTelegram::class,
            function (SendFilmToTelegram $job) use ($film) {
                return $job->filmId === $film->id;
            }
        );

        Bus::assertNotDispatched(DownloadFilmPoster::class);
    }

    public function test_import_does_not_create_duplicate_film_or_dispatch_jobs(): void
    {
        Bus::fake();

        $film = Film::factory()->create([
            'tmdb_id' => 12347,
        ]);

        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'id' => 12347,
                'title' => 'Existing Film',
                'original_title' => 'Existing Film',
                'overview' => 'Description',
                'poster_path' => '/poster.jpg',
                'release_date' => '2026-08-03',
            ], 200),
        ]);

        $result = app(FilmImportService::class)->import(12347);

        $this->assertSame($film->id, $result->id);

        Bus::assertNothingDispatched();
    }
}
