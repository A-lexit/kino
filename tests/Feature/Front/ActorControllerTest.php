<?php
namespace Tests\Feature;

use App\Models\Actor;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActorControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест головної сторінки зі списком акторів
     */
    public function test_index_displays_actors_list()
    {
        // Створюємо кількох фейкових акторів
        Actor::factory(3)->create();

        $response = $this->get(route('actors.index'));

        $response->assertStatus(200);
        $response->assertViewIs('actors.index');
        $response->assertViewHas('actors');
    }

    /**
     * Тест сторінки конкретного актора та його фільмів
     */
    public function test_show_displays_specific_actor_with_films()
    {
        // Створюємо актора із конкретним слагом
        $actor = Actor::factory()->create([
            'slug' => 'tom-cruise'
        ]);

        // Створюємо фільм і пов'язуємо його з актором через Many-to-Many
        $film = Film::factory()->create();
        $actor->films()->attach($film);

        // Викликаємо роут, передаючи слаг (як очікує ваш метод show)
        $response = $this->get(route('actors.show', $actor->slug));

        $response->assertStatus(200);
        $response->assertViewIs('actors.show');
        $response->assertViewHas('actor');
        $response->assertViewHas('films');
    }

}
