<?php
namespace Tests\Feature\Front;

use App\Models\Film;
use App\Models\Producer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProducerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_producers_page(): void
    {
        Producer::factory()->count(3)->create();

        $response = $this->get(route('producers.index'));

        $response->assertOk();

        $response->assertViewIs('producers.index');

        $response->assertViewHas('producers');

        $this->assertCount(
            3,
            $response->viewData('producers')
        );
    }


    public function test_show_displays_producer_films(): void
    {
        $producer = Producer::factory()->create([
            'slug' => 'jerry-bruckheimer',
        ]);

        $film = Film::factory()->create();

        $film->producers()->attach($producer);

        $response = $this->get(
            route('producers.show', $producer->slug)
        );

        $response->assertOk();

        $response->assertViewIs('producers.show');

        $response->assertViewHas([
            'producer',
            'films',
        ]);

        $this->assertEquals(
            $producer->id,
            $response->viewData('producer')->id
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
        $this->get(route('producers.show', 'unknown-slug'))
            ->assertNotFound();
    }

}
