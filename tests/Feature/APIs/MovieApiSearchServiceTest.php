<?php
namespace Tests\Feature\APIs;

use App\APIs\MovieApiSearchService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MovieApiSearchServiceTest extends TestCase
{
    public function test_search_returns_movies_from_tmdb(): void
    {
        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'results' => [
                    [
                        'id' => 1,
                        'title' => 'Old Movie',
                        'release_date' => '2020-01-01',
                    ],
                    [
                        'id' => 2,
                        'title' => 'New Movie',
                        'release_date' => '2025-01-01',
                    ],
                ]
            ], 200)
        ]);

        $service = new MovieApiSearchService();

        $result = $service->search('Avatar');

        $this->assertCount(2, $result);

        $this->assertEquals(
            'New Movie',
            $result[0]['title']
        );

        $this->assertEquals(
            'Old Movie',
            $result[1]['title']
        );
    }


    public function test_search_returns_empty_array_when_query_is_empty(): void
    {
        Http::fake();

        $service = new MovieApiSearchService();

        $result = $service->search('');

        $this->assertEquals([], $result);

        Http::assertNothingSent();
    }


    public function test_search_returns_empty_array_when_api_has_no_results(): void
    {
        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'results' => []
            ], 200)
        ]);

        $service = new MovieApiSearchService();

        $result = $service->search('unknown');

        $this->assertEquals([], $result);
    }


    public function test_upcoming_returns_movies(): void
    {
        Http::fake([
            'api.themoviedb.org/*' => Http::response([
                'results' => [
                    [
                        'id' => 10,
                        'title' => 'Upcoming Film'
                    ]
                ]
            ], 200)
        ]);

        $service = new MovieApiSearchService();

        $result = $service->upcoming();

        $this->assertCount(1, $result);

        $this->assertEquals(
            'Upcoming Film',
            $result[0]['title']
        );
    }


    public function test_upcoming_returns_empty_array_when_results_missing(): void
    {
        Http::fake([
            'api.themoviedb.org/*' => Http::response([], 200)
        ]);

        $service = new MovieApiSearchService();

        $result = $service->upcoming();

        $this->assertEquals([], $result);
    }

}
