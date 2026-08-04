<?php
namespace Tests\Feature\Front;

use App\Models\Age;
use App\Models\Category;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Enums\FilmStatus;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_displays_category_with_active_films(): void
    {
        $category = Category::factory()->create([
            'slug' => 'action-films'
        ]);

        $age = Age::factory()->create();

        Film::factory()->count(3)->create([
            'category_id' => $category->id,
            'age_id' => $age->id,
            'publish_status' => FilmStatus::Published,
        ]);

        Film::factory()->create([
            'category_id' => $category->id,
            'age_id' => $age->id,
            'publish_status' => FilmStatus::Draft,
        ]);

        $response = $this->get(
            route('categories.show', $category->slug)
        );

        $response->assertOk();

        $response->assertViewIs('categories.show');

        $response->assertViewHas([
            'category',
            'films'
        ]);

        $films = $response->viewData('films');

        $this->assertCount(3, $films->items());

        foreach ($films as $film) {
            $this->assertEquals(FilmStatus::Published, $film->publish_status);
        }
    }


    public function test_show_paginates_films_by_40(): void
    {
        $category = Category::factory()->create([
            'slug' => 'comedy'
        ]);

        $age = Age::factory()->create();

        Film::factory()->count(45)->create([
            'category_id' => $category->id,
            'age_id' => $age->id,
            'publish_status' => FilmStatus::Published,
        ]);

        $response = $this->get(
            route('categories.show', $category->slug)
        );

        $response->assertOk();

        $films = $response->viewData('films');

        $this->assertEquals(45, $films->total());

        $this->assertCount(40, $films->items());
    }

}
