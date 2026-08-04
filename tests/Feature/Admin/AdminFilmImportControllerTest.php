<?php
namespace Tests\Feature\Admin;

use App\APIs\FilmImportService;
use App\APIs\MovieApiSearchService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AdminFilmImportControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_guest_cannot_access_import_routes(): void
    {
        $this->get(route('admin.films.import'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.films.search'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.films.import.store', 1))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_import_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get(route('admin.films.import'))
            ->assertStatus(404);

        $this->get(route('admin.films.search'))
            ->assertStatus(404);

        $this->get(route('admin.films.import.store', 1))
            ->assertStatus(404);
    }

    public function test_import_page_returns_view(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.films.import'))
            ->assertOk()
            ->assertViewIs('admin.films.import');
    }

    public function test_search_without_query_returns_empty_movies(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.films.search'))
            ->assertOk()
            ->assertViewIs('admin.films.import')
            ->assertViewHas('movies', [])
            ->assertViewHas('query', '');
    }

    public function test_search_calls_api_service(): void
    {
        $this->actingAs($this->admin);

        $movies = [
            [
                'id' => 100,
                'title' => 'Matrix',
                'overview' => 'Best sci-fi movie.',
                'poster_path' => '/poster.jpg',
                'release_date' => '1999-03-31',
            ],
        ];

        $service = Mockery::mock(MovieApiSearchService::class);

        $service->shouldReceive('search')
            ->once()
            ->with('Matrix')
            ->andReturn($movies);

        $this->app->instance(MovieApiSearchService::class, $service);

        $this->get(route('admin.films.search', [
            'query' => 'Matrix',
        ]))
            ->assertOk()
            ->assertViewIs('admin.films.import')
            ->assertViewHas('movies', $movies)
            ->assertViewHas('query', 'Matrix');
    }

    public function test_import_calls_import_service(): void
    {
        $this->actingAs($this->admin);

        $service = Mockery::mock(FilmImportService::class);

        $service->shouldReceive('import')
            ->once()
            ->with(123);

        $this->app->instance(FilmImportService::class, $service);

        $this->get(route('admin.films.import.store', 123))
            ->assertRedirect()
            ->assertSessionHas('success', 'Фільм імпортовано');
    }


    public function test_search_with_no_results(): void
    {
        $this->actingAs($this->admin);

        $service = Mockery::mock(MovieApiSearchService::class);
        $service->shouldReceive('search')
            ->once()
            ->with('Unknown Movie')
            ->andReturn([]);

        $this->app->instance(MovieApiSearchService::class, $service);

        $this->get(route('admin.films.search', [
            'query' => 'Unknown Movie',
        ]))
            ->assertOk()
            ->assertViewIs('admin.films.import')
            ->assertViewHas('movies', [])
            ->assertViewHas('query', 'Unknown Movie');
    }

}
