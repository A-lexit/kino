<?php
namespace Tests\Feature\Api;

use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_comment(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/film-add-comment', [
                'body' => 'Дуже хороший фільм',
                'film_id' => $film->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Коментар додано',
            ]);

        $this->assertDatabaseHas('comments', [
            'body' => 'Дуже хороший фільм',
            'film_id' => $film->id,
            'user_id' => $user->id,
            'subject' => $user->name,
            'status' => 0,
        ]);
    }

    public function test_comment_can_use_custom_subject(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();

        $this
            ->actingAs($user)
            ->postJson('/api/film-add-comment', [
                'body' => 'Коментар',
                'subject' => 'Гість',
                'status' => 1,
                'film_id' => $film->id,
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('comments', [
            'subject' => 'Гість',
            'status' => 1,
        ]);
    }

    public function test_comment_requires_body(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/film-add-comment', [
                'film_id' => $film->id,
            ]);

        $response->assertStatus(422);
    }

    public function test_comment_requires_existing_film(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/film-add-comment', [
                'body' => 'Тест',
                'film_id' => 999999,
            ]);

        $response->assertStatus(422);
    }

    public function test_guest_cannot_create_comment(): void
    {
        $film = Film::factory()->create();

        $response = $this->postJson('/api/film-add-comment', [
            'body' => 'Коментар',
            'film_id' => $film->id,
        ]);

        $response->assertStatus(401);
    }

}
