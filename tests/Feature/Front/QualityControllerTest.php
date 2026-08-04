<?php
namespace Tests\Feature\Front;

use App\Models\Film;
use App\Models\Quality;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QualityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_qualities_page(): void
    {
        Quality::factory()->count(3)->create();

        $response = $this->get(route('qualities.index'));

        $response->assertOk();

        $response->assertViewIs('qualities.index');

        $response->assertViewHas('qualities');

        $this->assertCount(
            3,
            $response->viewData('qualities')
        );
    }


    public function test_show_displays_quality_films(): void
    {
        $quality = Quality::factory()->create([
            'slug' => 'full-hd',
        ]);

        $film = Film::factory()->create([
            'quality_id' => $quality->id,
        ]);

        $response = $this->get(
            route('qualities.show', $quality->slug)
        );

        $response->assertOk();

        $response->assertViewIs('qualities.show');

        $response->assertViewHas([
            'quality',
            'films',
        ]);

        $this->assertEquals(
            $quality->id,
            $response->viewData('quality')->id
        );

        $this->assertCount(
            1,
            $response->viewData('films')
        );

        $this->assertEquals(
            $film->id,
            $response->viewData('films')->first()->id
        );
    }


    public function test_show_returns_404_for_unknown_slug(): void
    {
        $this->get(route('qualities.show', 'unknown-slug'))
            ->assertNotFound();
    }

}
