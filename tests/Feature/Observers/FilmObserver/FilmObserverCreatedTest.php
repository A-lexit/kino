<?php
namespace Tests\Feature\Observers\FilmObserver;

use App\Models\Film;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmObserverCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function test_state_is_created_when_film_is_created(): void
    {
        $film = Film::factory()->create();

        $this->assertDatabaseHas('states', [
            'film_id' => $film->id,
        ]);
    }


    public function test_only_one_state_is_created_for_new_film(): void
    {
        $film = Film::factory()->create();

        $this->assertEquals(
            1,
            State::where('film_id', $film->id)->count()
        );
    }


    public function test_created_state_belongs_to_created_film(): void
    {
        $film = Film::factory()->create();

        $state = State::where('film_id', $film->id)->first();

        $this->assertNotNull($state);

        $this->assertEquals(
            $film->id,
            $state->film_id
        );
    }

}
