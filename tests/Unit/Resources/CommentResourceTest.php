<?php
namespace Tests\Unit\Resources;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CommentResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_transforms_comment_to_expected_structure(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();

        $comment = Comment::create([
            'subject' => 'Тестовий підпис',
            'body' => 'Текст коментаря',
            'status' => 1,
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);

        $resource = new CommentResource($comment);
        $array = $resource->toArray(new Request());

        $this->assertSame([
            'id' => $comment->id,
            'subject' => 'Тестовий підпис',
            'body' => 'Текст коментаря',
            'status' => 1,
            'created_at' => $comment->createdAtForHumans(),
        ], $array);
    }


    public function test_it_does_not_expose_user_id_or_film_id(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();

        $comment = Comment::create([
            'subject' => 'Тест',
            'body' => 'Текст',
            'status' => 1,
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);

        $resource = new CommentResource($comment);
        $array = $resource->toArray(new Request());

        $this->assertArrayNotHasKey('user_id', $array);
        $this->assertArrayNotHasKey('film_id', $array);
    }

}
