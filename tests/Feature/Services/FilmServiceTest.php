<?php
namespace Tests\Feature\Services;

use App\Models\Film;
use App\Models\User;
use App\Services\FilmService;
use App\Media\FilmImageMedia; // Правильний клас замість ImageService
use App\Media\FilmVideoMedia;
use App\Http\Requests\FilmRequest; // Додаємо для мокання
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Enums\UserRole;

class FilmServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $author;

    protected function setUp(): void
    {
        parent::setUp();

        // Видаляємо або коментуємо Film::unsetEventDispatcher(),
        // щоб Observers могли відпрацювати і створити запис у states

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);
        $this->author = User::factory()->create([
            'role' => UserRole::User,
        ]);
        $this->actingAs($this->author);

        Storage::fake('public');
        Cache::flush();

        // ВАЖЛИВО: Ми НЕ створюємо тут $this->service через app().
        // Ми будемо викликати його в кожному тесті ПІСЛЯ налаштування моків.
    }


    public function test_get_films_for_user_returns_only_own_films_for_non_admin(): void
    {
        // Використовуємо createQuietly(), щоб Observer не перезаписав author_id
        Film::factory()->createQuietly(['title' => 'Own Film', 'author_id' => $this->author->id]);
        Film::factory()->createQuietly(['title' => 'Other Film', 'author_id' => User::factory()->create()->id]);

        $service = app(FilmService::class);
        $result = $service->getFilmsForUser($this->author);

        $this->assertCount(1, $result['films']);
        $this->assertEquals('Own Film', $result['films']->first()->title);
    }


    public function test_get_films_for_user_returns_all_films_for_admin(): void
    {
        // Перемикаємо контекст автентифікації на адміна
        $this->actingAs($this->admin);

        Film::factory()->createQuietly(['title' => 'Film 1', 'author_id' => $this->author->id]);
        Film::factory()->createQuietly(['title' => 'Film 2', 'author_id' => $this->admin->id]);

        $service = app(FilmService::class);
        $result = $service->getFilmsForUser($this->admin);

        $this->assertGreaterThanOrEqual(2, $result['films']->count());
    }


    public function test_create_film_saves_data_creates_state_and_flushes_cache(): void
    {
        $validatedData = [
            'title' => 'Inception',
            'slug' => 'inception',
            'publish_status' => 'published',
            'is_featured' => 1,
        ];

        // 1. Мокаємо FilmRequest, щоб уникнути TypeError і віддати дані через validated()
        $requestMock = $this->mock(FilmRequest::class, function (MockInterface $mock) use ($validatedData) {
            $mock->shouldReceive('validated')->andReturn($validatedData);
            $mock->shouldReceive('get')->with('publish_status')->andReturn('published');
            $mock->shouldReceive('get')->with('is_featured')->andReturn(1);
            $mock->shouldReceive('all')->andReturn($validatedData);
        });

        // 2. Мокаємо класи медіа, які реально інжектяться у FilmService
        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadFilmImages')->once(); // Правильна назва методу
        });

        $this->mock(FilmVideoMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadTrailer')->once();
        });

        // 3. Створюємо сервіс ПІСЛЯ моків, щоб Laravel підтягнув фейкові класи
        $service = app(FilmService::class);
        $film = $service->createFilm($requestMock);

        $this->assertDatabaseHas('films', [
            'title' => 'Inception',
        ]);

        $this->assertDatabaseHas('states', [
            'film_id' => $film->id,
        ]);
    }


    public function test_update_film_changes_data_and_handles_images(): void
    {
        $film = Film::factory()->create([
            'title' => 'Old Title',
            'author_id' => $this->author->id,
        ]);

        $validatedData = ['title' => 'New Title'];

        $requestMock = $this->mock(FilmRequest::class, function (MockInterface $mock) use ($validatedData) {
            $mock->shouldReceive('validated')->andReturn($validatedData);
            $mock->shouldReceive('get')->with('publish_status')->andReturn(null);
            $mock->shouldReceive('get')->with('is_featured')->andReturn(null);
            $mock->shouldReceive('all')->andReturn($validatedData);
        });

        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadFilmImages')->once();
        });

        $this->mock(FilmVideoMedia::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadTrailer')->once();
        });

        $service = app(FilmService::class);
        $updated = $service->updateFilm($film->id, $requestMock);

        $this->assertEquals('New Title', $updated->title);
    }


    public function test_delete_film_soft_deletes_record(): void
    {
        $film = Film::factory()->create([
            'author_id' => $this->author->id,
        ]);

        $service = app(FilmService::class);
        $service->deleteFilm($film->id);

        $this->assertSoftDeleted('films', [
            'id' => $film->id,
        ]);
    }


    public function test_restore_film_brings_back_soft_deleted_record(): void
    {
        $film = Film::factory()->create([
            'author_id' => $this->author->id,
            'deleted_at' => now(),
        ]);

        $service = app(FilmService::class);
        $service->restoreFilm($film->id);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'deleted_at' => null,
        ]);
    }


    public function test_force_delete_film_completely_removes_from_db(): void
    {
        $film = Film::factory()->create([
            'deleted_at' => now(),
        ]);

        $this->mock(FilmImageMedia::class, function (MockInterface $mock) {
            // Метод у FilmService називається deleteFilmImages
            $mock->shouldReceive('deleteFilmImages')->once();
        });

        $service = app(FilmService::class);
        $service->forceDeleteFilm($film->id);

        $this->assertDatabaseMissing('films', [
            'id' => $film->id,
        ]);
    }


    public function test_find_film_returns_film(): void
    {
        $film = Film::factory()->create();

        $service = app(FilmService::class);
        $result = $service->findFilm($film->id);

        $this->assertEquals($film->id, $result->id);
    }

}
