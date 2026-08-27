<?php

namespace Tests\Feature\Services;

use App\APIs\OmdbService;
use App\Enums\UserRole;
use App\Http\Requests\FilmRequest;
use App\Media\FilmImageMedia;
use App\Media\FilmVideoMedia;
use App\Models\Film;
use App\Models\User;
use App\Services\FilmService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class FilmServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $editor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()
            ->admin()
            ->create();

        $this->editor = User::factory()
            ->create([
                'role' => UserRole::Editor,
            ]);

        $this->actingAs($this->editor);

        Storage::fake('public');
        Cache::flush();
    }

    public function test_get_films_for_user_returns_all_films(): void
    {
        Film::factory()->createQuietly([
            'title' => 'Film 1',
            'author_id' => $this->editor->id,
        ]);

        Film::factory()->createQuietly([
            'title' => 'Film 2',
            'author_id' => $this->admin->id,
        ]);

        $service = app(FilmService::class);

        $result = $service->getFilmsForUser();

        $this->assertCount(2, $result['films']);

        $this->assertTrue(
            $result['films']->pluck('title')->contains('Film 1')
        );

        $this->assertTrue(
            $result['films']->pluck('title')->contains('Film 2')
        );
    }

    public function test_create_film_saves_data_and_creates_state(): void
    {
        $validatedData = [
            'title' => 'Inception',
            'slug' => 'inception',
            'publish_status' => 'published',
            'is_featured' => 1,
        ];

        $requestMock = $this->mock(
            FilmRequest::class,
            function (MockInterface $mock) use ($validatedData) {
                $mock->shouldReceive('validated')
                    ->andReturn($validatedData);

                $mock->shouldReceive('get')
                    ->with('publish_status')
                    ->andReturn('published');

                $mock->shouldReceive('get')
                    ->with('is_featured')
                    ->andReturn(1);

                $mock->shouldReceive('all')
                    ->andReturn($validatedData);
            }
        );

        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadFilmImages')
                ->once();
        });

        $this->mock(FilmVideoMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadTrailer')
                ->once();
        });

        $service = app(FilmService::class);

        $film = $service->createFilm($requestMock);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'title' => 'Inception',
        ]);

        $this->assertDatabaseHas('states', [
            'film_id' => $film->id,
            'likes' => 0,
            'views' => 0,
        ]);
    }


    public function test_create_film_without_category_is_saved_as_draft(): void
    {
        $validatedData = [
            'title' => 'Inception',
            'slug' => 'inception',
            'category_id' => null,
            'publish_status' => 'published',
            'is_featured' => 1,
        ];

        $requestMock = $this->mock(
            FilmRequest::class,
            function (MockInterface $mock) use ($validatedData) {
                $mock->shouldReceive('validated')
                    ->andReturn($validatedData);

                $mock->shouldReceive('get')
                    ->with('publish_status')
                    ->andReturn('published');

                $mock->shouldReceive('get')
                    ->with('is_featured')
                    ->andReturn(1);

                $mock->shouldReceive('all')
                    ->andReturn($validatedData);
            }
        );

        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadFilmImages')
                ->once();
        });

        $this->mock(FilmVideoMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadTrailer')
                ->once();
        });

        $service = app(FilmService::class);

        $film = $service->createFilm($requestMock);

        $film->refresh();

        $this->assertNull($film->category_id);

        $this->assertEquals(
            'draft',
            $film->publish_status->value
        );
    }



    public function test_editor_can_update_film_data_without_changing_slug(): void
    {
        $film = Film::factory()->create([
            'title' => 'Old Title',
            'author_id' => $this->editor->id,
            'slug' => 'old-title',
        ]);

        $validatedData = [
            'title' => 'New Title',
        ];

        $requestMock = $this->mock(
            FilmRequest::class,
            function (MockInterface $mock) use ($validatedData) {
                $mock->shouldReceive('validated')
                    ->andReturn($validatedData);

                $mock->shouldReceive('get')
                    ->with('publish_status')
                    ->andReturn(null);

                $mock->shouldReceive('get')
                    ->with('is_featured')
                    ->andReturn(null);

                $mock->shouldReceive('all')
                    ->andReturn($validatedData);
            }
        );

        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadFilmImages')
                ->once();
        });

        $this->mock(FilmVideoMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadTrailer')
                ->once();
        });

        $service = app(FilmService::class);

        $updated = $service->updateFilm(
            $film,
            $requestMock
        );

        $updated->refresh();

        $this->assertEquals('New Title', $updated->title);
        $this->assertEquals('old-title', $updated->slug);
    }

    public function test_admin_can_update_film_slug(): void
    {
        $this->actingAs($this->admin);

        $film = Film::factory()->create([
            'title' => 'Old Title',
            'slug' => 'old-title',
            'author_id' => $this->editor->id,
        ]);

        $validatedData = [
            'title' => 'Updated Film',
            'slug' => 'New Slug',
        ];

        $requestMock = $this->mock(
            FilmRequest::class,
            function (MockInterface $mock) use ($validatedData) {
                $mock->shouldReceive('validated')
                    ->andReturn($validatedData);

                $mock->shouldReceive('get')
                    ->with('publish_status')
                    ->andReturn(null);

                $mock->shouldReceive('get')
                    ->with('is_featured')
                    ->andReturn(null);

                $mock->shouldReceive('all')
                    ->andReturn($validatedData);
            }
        );

        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadFilmImages')
                ->once();
        });

        $this->mock(FilmVideoMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadTrailer')
                ->once();
        });

        $service = app(FilmService::class);

        $updated = $service->updateFilm(
            $film,
            $requestMock
        );

        $updated->refresh();

        $this->assertEquals('Updated Film', $updated->title);
        $this->assertEquals('new-slug', $updated->slug);
    }

    public function test_editor_cannot_update_film_slug(): void
    {
        $this->actingAs($this->editor);

        $film = Film::factory()->create([
            'slug' => 'old-slug',
            'author_id' => $this->editor->id,
        ]);

        $validatedData = [
            'title' => 'Updated Film',
            'slug' => 'new-slug',
        ];

        $requestMock = $this->mock(
            FilmRequest::class,
            function (MockInterface $mock) use ($validatedData) {
                $mock->shouldReceive('validated')
                    ->andReturn($validatedData);

                $mock->shouldReceive('get')
                    ->with('publish_status')
                    ->andReturn(null);

                $mock->shouldReceive('get')
                    ->with('is_featured')
                    ->andReturn(null);

                $mock->shouldReceive('all')
                    ->andReturn($validatedData);
            }
        );

        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadFilmImages')->once();
        });

        $this->mock(FilmVideoMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadTrailer')->once();
        });

        $service = app(FilmService::class);

        $updated = $service->updateFilm($film, $requestMock);

        $updated->refresh();

        $this->assertEquals('Updated Film', $updated->title);
        $this->assertEquals('old-slug', $updated->slug);
    }

    public function test_delete_film_soft_deletes_record(): void
    {
        $film = Film::factory()->create([
            'author_id' => $this->editor->id,
        ]);

        $service = app(FilmService::class);

        $service->deleteFilm($film);

        $this->assertSoftDeleted('films', [
            'id' => $film->id,
        ]);
    }

    public function test_restore_film_brings_back_soft_deleted_record(): void
    {
        $film = Film::factory()->create([
            'author_id' => $this->editor->id,
        ]);

        $film->delete();

        $service = app(FilmService::class);

        $service->restoreFilm($film);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'deleted_at' => null,
        ]);
    }

    public function test_force_delete_film_completely_removes_from_db(): void
    {
        $film = Film::factory()->create();

        $film->delete();

        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('deleteFilmImages')
                ->once();
        });

        $this->mock(FilmVideoMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('deleteTrailer')
                ->once();
        });

        $service = app(FilmService::class);

        $service->forceDeleteFilm($film);

        $this->assertDatabaseMissing('films', [
            'id' => $film->id,
        ]);
    }

    public function test_bulk_delete_soft_deletes_only_active_films(): void
    {
        $film1 = Film::factory()->create();
        $film2 = Film::factory()->create();

        $alreadyDeleted = Film::factory()->create();
        $alreadyDeleted->delete();

        $service = app(FilmService::class);

        $films = Film::withTrashed()
            ->whereIn('id', [
                $film1->id,
                $film2->id,
                $alreadyDeleted->id,
            ])
            ->get();

        $count = $service->bulkDelete($films);

        $this->assertEquals(2, $count);

        $this->assertSoftDeleted('films', [
            'id' => $film1->id,
        ]);

        $this->assertSoftDeleted('films', [
            'id' => $film2->id,
        ]);

        $this->assertSoftDeleted('films', [
            'id' => $alreadyDeleted->id,
        ]);
    }

    public function test_bulk_restore_restores_only_trashed_films(): void
    {
        $activeFilm = Film::factory()->create();

        $trashedFilm1 = Film::factory()->create();
        $trashedFilm1->delete();

        $trashedFilm2 = Film::factory()->create();
        $trashedFilm2->delete();

        $service = app(FilmService::class);

        $films = Film::withTrashed()
            ->whereIn('id', [
                $activeFilm->id,
                $trashedFilm1->id,
                $trashedFilm2->id,
            ])
            ->get();

        $count = $service->bulkRestore($films);

        $this->assertEquals(2, $count);

        $this->assertDatabaseHas('films', [
            'id' => $activeFilm->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('films', [
            'id' => $trashedFilm1->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('films', [
            'id' => $trashedFilm2->id,
            'deleted_at' => null,
        ]);
    }

    public function test_bulk_force_delete_permanently_removes_films(): void
    {
        $film1 = Film::factory()->create();
        $film2 = Film::factory()->create();

        $film1->delete();
        $film2->delete();

        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('deleteFilmImages')
                ->twice();
        });

        $this->mock(FilmVideoMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('deleteTrailer')
                ->twice();
        });

        $service = app(FilmService::class);

        $films = Film::withTrashed()
            ->whereIn('id', [
                $film1->id,
                $film2->id,
            ])
            ->get();

        $count = $service->bulkForceDelete($films);

        $this->assertEquals(2, $count);

        $this->assertDatabaseMissing('films', [
            'id' => $film1->id,
        ]);

        $this->assertDatabaseMissing('films', [
            'id' => $film2->id,
        ]);
    }

    public function test_restore_all_films_restores_all_trashed_films(): void
    {
        $film1 = Film::factory()->create([
            'title' => 'Deleted Film 1',
        ]);

        $film2 = Film::factory()->create([
            'title' => 'Deleted Film 2',
        ]);

        $film1->delete();
        $film2->delete();

        $service = app(FilmService::class);

        $count = $service->restoreAllFilms();

        $this->assertEquals(2, $count);

        $this->assertDatabaseHas('films', [
            'id' => $film1->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('films', [
            'id' => $film2->id,
            'deleted_at' => null,
        ]);
    }

    public function test_force_delete_all_films_permanently_removes_all_trashed_films(): void
    {
        $film1 = Film::factory()->create();
        $film2 = Film::factory()->create();

        $film1->delete();
        $film2->delete();

        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('deleteFilmImages')
                ->twice();
        });

        $this->mock(FilmVideoMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('deleteTrailer')
                ->twice();
        });

        $service = app(FilmService::class);

        $count = $service->forceDeleteAllFilms();

        $this->assertEquals(2, $count);

        $this->assertDatabaseMissing('films', [
            'id' => $film1->id,
        ]);

        $this->assertDatabaseMissing('films', [
            'id' => $film2->id,
        ]);
    }

    public function test_fetch_imdb_rating_updates_film(): void
    {
        $film = Film::factory()->create([
            'imdb_id' => null,
            'imdb_rating' => null,
        ]);

        $this->mock(
            OmdbService::class,
            function (MockInterface $mock) use ($film) {
                $mock->shouldReceive('fetchRating')
                    ->once()
                    ->with($film)
                    ->andReturn([
                        'imdb_id' => 'tt0133093',
                        'imdb_rating' => 8.7,
                    ]);
            }
        );

        $service = app(FilmService::class);

        $result = $service->fetchImdbRating($film);

        $this->assertNotNull($result);

        $film->refresh();

        $this->assertEquals('tt0133093', $film->imdb_id);
        $this->assertEquals(8.7, $film->imdb_rating);
    }

    public function test_fetch_imdb_rating_returns_null_when_movie_not_found(): void
    {
        $film = Film::factory()->create([
            'imdb_id' => null,
            'imdb_rating' => null,
        ]);

        $this->mock(
            OmdbService::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('fetchRating')
                    ->once()
                    ->andReturn(null);
            }
        );

        $service = app(FilmService::class);

        $result = $service->fetchImdbRating($film);

        $this->assertNull($result);

        $film->refresh();

        $this->assertNull($film->imdb_id);
        $this->assertNull($film->imdb_rating);
    }
}
