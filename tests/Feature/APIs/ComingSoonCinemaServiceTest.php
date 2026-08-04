<?php
namespace Tests\Feature\APIs;

use App\APIs\ComingSoonCinemaService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ComingSoonCinemaServiceTest extends TestCase
{
    public function test_upcoming_returns_five_movies_from_api(): void
    {
        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'results' => [
                    ['id' => 1, 'title' => 'Film 1'],
                    ['id' => 2, 'title' => 'Film 2'],
                    ['id' => 3, 'title' => 'Film 3'],
                    ['id' => 4, 'title' => 'Film 4'],
                    ['id' => 5, 'title' => 'Film 5'],
                    ['id' => 6, 'title' => 'Film 6'],
                ],
            ], 200),
        ]);

        Cache::forget('upcoming_movies');

        $service = new ComingSoonCinemaService();

        $movies = $service->upcoming();

        $this->assertCount(5, $movies);

        $this->assertEquals(
            'Film 1',
            $movies->first()['title']
        );
    }


    public function test_upcoming_returns_empty_collection_when_api_failed(): void
    {
        Http::fake([
            'api.themoviedb.org/*' => Http::response([], 500),
        ]);

        Cache::forget('upcoming_movies');

        $service = new ComingSoonCinemaService();

        $movies = $service->upcoming();

        $this->assertTrue(
            $movies->isEmpty()
        );
    }


    public function test_upcoming_uses_cache(): void
    {
        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'results' => [
                    ['id' => 1, 'title' => 'Cached Film'],
                ],
            ], 200),
        ]);

        Cache::forget('upcoming_movies');

        $service = new ComingSoonCinemaService();

        $first = $service->upcoming();

        $second = $service->upcoming();

        Http::assertSentCount(1);

        $this->assertEquals(
            $first,
            $second
        );
    }

}
