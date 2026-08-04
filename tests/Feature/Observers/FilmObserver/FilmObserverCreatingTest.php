<?php
namespace Tests\Feature\Observers\FilmObserver;

use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmObserverCreatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_id_is_set_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $film = Film::factory()->create([
            'author_id' => null,
        ]);

        $this->assertEquals(
            $user->id,
            $film->fresh()->author_id
        );
    }


    public function test_author_id_is_not_set_for_guest(): void
    {
        auth()->logout();

        $film = Film::factory()->create([
            'author_id' => null,
        ]);

        $this->assertNull(
            $film->fresh()->author_id
        );
    }

}
