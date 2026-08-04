<?php
namespace Tests\Feature\Front;

use App\Models\Composer;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComposerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_composers_list(): void
    {
        Composer::factory()->count(3)->create();

        $response = $this->get(route('composers.index'));

        $response->assertOk();
        $response->assertViewIs('composers.index');
        $response->assertViewHas('composers');

        $this->assertCount(3, $response->viewData('composers'));
    }


    public function test_show_displays_composer_with_films(): void
    {
        $composer = Composer::factory()->create();

        $films = Film::factory()->count(2)->create();

        foreach ($films as $film) {
            $film->composers()->attach($composer);
        }

        $response = $this->get(route('composers.show', $composer->slug));

        $response->assertOk();
        $response->assertViewIs('composers.show');
        $response->assertViewHasAll(['composer', 'films']);

        $this->assertEquals(
            $composer->id,
            $response->viewData('composer')->id
        );

        $this->assertCount(
            2,
            $response->viewData('films')
        );
    }


    public function test_show_returns_404_for_unknown_composer(): void
    {
        $response = $this->get(
            route('composers.show', 'not-exists')
        );

        $response->assertNotFound();
    }


    public function test_show_paginates_films_by_20(): void
    {
        $composer = Composer::factory()->create();

        $films = Film::factory()->count(25)->create();

        foreach ($films as $film) {
            $film->composers()->attach($composer);
        }

        $response = $this->get(
            route('composers.show', $composer->slug)
        );

        $response->assertOk();

        $this->assertCount(
            20,
            $response->viewData('films')
        );
    }

}
