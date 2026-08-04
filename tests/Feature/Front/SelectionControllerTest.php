<?php
namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Film;
use App\Models\Selection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SelectionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_selections_page(): void
    {
        $selections = Selection::factory()->count(3)->create();

        $response = $this->get(route('selections.index'));

        $response->assertOk();
        $response->assertViewIs('selections.index');
        $response->assertViewHas('selections');

        $this->assertCount(3, $response->viewData('selections'));
    }


    public function test_show_displays_selection_films(): void
    {
        $selection = Selection::factory()->create([
            'slug' => 'top-films',
        ]);

        $category = Category::factory()->create([
            'slug' => 'films',
        ]);

        $film = Film::factory()->create([
            'category_id' => $category->id,
        ]);

        $selection->films()->attach($film->id);

        $response = $this->get(route('selections.show', $selection->slug));

        $response->assertOk();

        $response->assertViewIs('selections.show');

        $response->assertViewHas('selection', $selection);

        $response->assertViewHas('films', function ($films) use ($film) {
            return $films->contains($film);
        });
    }


    public function test_show_returns_404_for_unknown_slug(): void
    {
        $response = $this->get(route('selections.show', 'unknown-selection'));

        $response->assertNotFound();
    }

}
