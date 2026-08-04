<?php
namespace Tests\Unit\Resources;

use App\Http\Resources\FilmResource;
use App\Models\Comment;
use App\Models\Film;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class FilmResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_includes_id_always(): void
    {
        $film = Film::factory()->create();

        $resource = new FilmResource($film);
        $array = $resource->toArray(new Request());

        $this->assertSame($film->id, $array['id']);
    }


    public function test_comments_are_missing_when_relation_not_loaded(): void
    {
        $film = Film::factory()->create();

        $resource = new FilmResource($film);
        $array = $resource->toArray(new Request());

        // whenLoaded повертає MissingValue, який при json_encode просто зникає з масиву
        $this->assertArrayNotHasKey('comments', $this->resourceToJsonArray($resource));
    }


    public function test_comments_are_included_when_relation_loaded(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();
        Comment::create([
            'subject' => 'Тест',
            'body' => 'Текст коментаря',
            'status' => 1,
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);

        $film->load('comments');

        $resource = new FilmResource($film);
        $array = $this->resourceToJsonArray($resource);

        $this->assertArrayHasKey('comments', $array);
        $this->assertCount(1, $array['comments']);
    }


    public function test_statistic_is_missing_when_relation_not_loaded(): void
    {
        $film = Film::factory()->create();

        $resource = new FilmResource($film);
        $array = $this->resourceToJsonArray($resource);

        $this->assertArrayNotHasKey('statistic', $array);
    }


    public function test_statistic_is_included_when_relation_loaded(): void
    {
        $film = Film::factory()->create();
        State::create(['film_id' => $film->id]);

        $film->load('state');

        $resource = new FilmResource($film);
        $array = $this->resourceToJsonArray($resource);

        $this->assertArrayHasKey('statistic', $array);
    }


    /**
     * whenLoaded() повертає спеціальний об'єкт MissingValue, коли зв'язок не завантажений.
     * Пряме порівняння через toArray() його не прибере — тому емулюємо
     * реальну поведінку через фактичний JSON-рендеринг ресурсу.
     */
    protected function resourceToJsonArray(FilmResource $resource): array
    {
        return json_decode($resource->response()->getContent(), true)['data'] ?? [];
    }

}
