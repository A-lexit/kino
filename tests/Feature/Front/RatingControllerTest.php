<?php
namespace Tests\Feature\Front;

use App\Models\Film;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_ratings_page(): void
    {
        $ratings = Rating::factory()->count(3)->create();

        $response = $this->get(route('ratings.index'));

        $response->assertOk();
        $response->assertViewIs('ratings.index');
        $response->assertViewHas('ratings');

        $viewRatings = $response->viewData('ratings');

        $this->assertEquals(
            $ratings->pluck('id')->sort()->values()->toArray(),
            $viewRatings->pluck('id')->sort()->values()->toArray()
        );
    }


    public function test_show_displays_rating_films(): void
    {
        $rating = Rating::factory()->create([
            'slug' => 'imdb-8',
        ]);

        $film = Film::factory()->create([
            'rating_id' => $rating->id,
        ]);

        $response = $this->get(route('ratings.show', $rating->slug));

        $response->assertOk();

        $response->assertViewIs('ratings.show');

        $response->assertViewHas([
            'rating',
            'films',
        ]);

        $this->assertEquals($rating->id, $response->viewData('rating')->id);

        $films = $response->viewData('films');

        $this->assertCount(1, $films);
        $this->assertEquals($film->id, $films->first()->id);
    }


    public function test_show_returns_404_for_unknown_slug(): void
    {
        $response = $this->get(route('ratings.show', 'unknown-rating'));

        $response->assertNotFound();
    }

}
