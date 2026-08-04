<?php
namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Film;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class YearControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_years_page(): void
    {
        Year::factory()->count(25)->create();

        $response = $this->get(route('years.index'));

        $response->assertOk();

        $response->assertViewIs('years.index');

        $response->assertViewHas('years');

        $this->assertCount(
            20,
            $response->viewData('years')->items()
        );
    }


    public function test_show_displays_year_films(): void
    {
        $category = Category::factory()->create([
            'slug' => 'films',
        ]);

        $year = Year::factory()->create([
            'slug' => '2025',
        ]);

        $film = Film::factory()->create([
            'category_id' => $category->id,
            'year_id' => $year->id,
            'slug' => 'avatar',
        ]);

        $response = $this->get(
            route('years.show', $year->slug)
        );

        $response->assertOk();

        $response->assertViewIs('years.show');

        $response->assertViewHas([
            'year',
            'films',
        ]);

        $this->assertTrue(
            $response->viewData('year')->is($year)
        );

        $this->assertTrue(
            $response->viewData('films')->contains($film)
        );
    }


    public function test_show_returns_404_for_unknown_slug(): void
    {
        $response = $this->get(
            route('years.show', 'unknown-year')
        );

        $response->assertNotFound();
    }

}
