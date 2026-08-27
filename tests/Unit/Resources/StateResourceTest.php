<?php
namespace Tests\Unit\Resources;

use App\Http\Resources\StateResource;
use App\Models\Film;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class StateResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_transforms_likes_and_views_correctly(): void
    {
        $film = Film::factory()->create();
        $state = State::create([
            'film_id' => $film->id,
            'likes' => 42,
            'views' => 777,
        ]);

        $resource = new StateResource($state);
        $array = $resource->toArray(new Request());

        $this->assertSame(42, $array['likes']);
        $this->assertSame(777, $array['views']);
    }

}
