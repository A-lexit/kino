<?php
namespace Tests\Feature\Services;

use App\Models\Film;
use App\Models\Actor;
use App\Models\Company;
use App\Models\Producer;
use App\Models\Genre;
use App\Services\FilmRelationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmRelationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_updates_film_relations(): void
    {
        $film = Film::factory()->create();

        $actor = Actor::factory()->create();
        $company = Company::factory()->create();
        $producer = Producer::factory()->create();
        $genre = Genre::factory()->create();

        $service = new FilmRelationService();

        $service->sync($film, [

            'actors' => [
                $actor->id,
            ],

            'companies' => [
                $company->id,
            ],

            'producers' => [
                $producer->id,
            ],

            'genres' => [
                $genre->id,
            ],

        ]);

        $this->assertDatabaseHas(
            'actor_film',
            [
                'film_id' => $film->id,
                'actor_id' => $actor->id,
            ]
        );

        $this->assertDatabaseHas(
            'company_film',
            [
                'film_id' => $film->id,
                'company_id' => $company->id,
            ]
        );

        $this->assertDatabaseHas(
            'film_producer',
            [
                'film_id' => $film->id,
                'producer_id' => $producer->id,
            ]
        );

        $this->assertDatabaseHas(
            'film_genre',
            [
                'film_id' => $film->id,
                'genre_id' => $genre->id,
            ]
        );
    }


    public function test_sync_detaches_missing_relations(): void
    {
        $film = Film::factory()->create();

        $actor1 = Actor::factory()->create();
        $actor2 = Actor::factory()->create();

        $film->actors()->attach([
            $actor1->id,
            $actor2->id,
        ]);

        $service = new FilmRelationService();

        $service->sync($film, [
            'actors' => [
                $actor1->id,
            ],
        ]);

        $this->assertTrue(
            $film->actors()->where('actors.id', $actor1->id)->exists()
        );

        $this->assertFalse(
            $film->actors()->where('actors.id', $actor2->id)->exists()
        );
    }


    public function test_sync_empty_relations_detaches_all(): void
    {
        $film = Film::factory()->create();

        $actor = Actor::factory()->create();

        $film->actors()->attach($actor->id);

        $service = new FilmRelationService();

        $service->sync($film, []);

        $this->assertDatabaseMissing(
            'actor_film',
            [
                'film_id' => $film->id,
                'actor_id' => $actor->id,
            ]
        );
    }

}
