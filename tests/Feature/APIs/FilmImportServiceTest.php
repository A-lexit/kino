<?php
namespace Tests\Feature\APIs;

use App\APIs\FilmImportService;
use App\APIs\TelegramService;
use App\Media\ImageMedia;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class FilmImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_new_film_from_tmdb(): void
    {
        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'id' => 123,
                'title' => 'Avatar',
                'original_title' => 'Avatar',
                'overview' => 'Description',
                'poster_path' => '/avatar.jpg',
                'release_date' => '2026-01-01',
            ], 200),


            'image.tmdb.org/*' => Http::response(
                'fake-image-content',
                200
            ),
        ]);


        $telegram = Mockery::mock(TelegramService::class);

        $telegram->shouldReceive('sendFilm')
            ->once()
            ->with(Mockery::type(Film::class));

        $imageMedia = Mockery::mock(ImageMedia::class);

        $imageMedia->shouldReceive('upload')
            ->once()
            ->with(
                Mockery::type(UploadedFile::class),
                Mockery::type('string')
            )
            ->andReturn('images/2026-07-20/avatar.webp');

        $this->app->instance(
            TelegramService::class,
            $telegram
        );

        $this->app->instance(
            ImageMedia::class,
            $imageMedia
        );

        $service = app(FilmImportService::class);

        $film = $service->import(123);

        $this->assertDatabaseHas('films', [
            'tmdb_id' => 123,
            'title' => 'Avatar',
            'thumbnail' => 'images/2026-07-20/avatar.webp',
        ]);

        $this->assertInstanceOf(
            Film::class,
            $film
        );
    }


    public function test_import_returns_existing_film(): void
    {
        $film = Film::factory()->create([
            'tmdb_id' => 123,
            'title' => 'Avatar',
        ]);

        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'id' => 123,
                'title' => 'Avatar',
            ], 200),
        ]);

        $telegram = Mockery::mock(TelegramService::class);

        $telegram->shouldNotReceive('sendFilm');

        $imageMedia = Mockery::mock(ImageMedia::class);

        $imageMedia->shouldNotReceive('upload');

        $this->app->instance(
            TelegramService::class,
            $telegram
        );

        $this->app->instance(
            ImageMedia::class,
            $imageMedia
        );

        $service = app(FilmImportService::class);

        $result = $service->import(123);

        $this->assertTrue(
            $film->is($result)
        );
    }



    public function test_import_throws_exception_when_movie_not_found(): void
    {
        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'status_code' => 34,
            ], 200),
        ]);


        $telegram = Mockery::mock(TelegramService::class);

        $imageMedia = Mockery::mock(ImageMedia::class);


        $this->app->instance(
            TelegramService::class,
            $telegram
        );

        $this->app->instance(
            ImageMedia::class,
            $imageMedia
        );


        $service = app(FilmImportService::class);


        $this->expectException(\Exception::class);

        $this->expectExceptionMessage(
            'Фільм не знайдено в TMDB'
        );


        $service->import(999);
    }


    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

}
