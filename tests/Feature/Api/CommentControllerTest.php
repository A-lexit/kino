<?php

namespace Tests\Feature\Api;

use App\Jobs\AddNewComment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_comment(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $film = Film::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/film-add-comment', [
                'body' => 'Дуже хороший фільм',
                'film_id' => $film->id,
            ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Коментар додано',
            ]);

        Queue::assertPushed(AddNewComment::class, function (AddNewComment $job) use ($user, $film) {
            return $job->subject === $user->name
                && $job->body === 'Дуже хороший фільм'
                && $job->status === 0
                && $job->film_id === $film->id
                && $job->user_id === $user->id;
        });
    }

    public function test_comment_can_use_custom_subject(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $film = Film::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/film-add-comment', [
                'body' => 'Коментар',
                'subject' => 'Гість',
                'status' => 1,
                'film_id' => $film->id,
            ]);

        $response->assertStatus(201);

        Queue::assertPushed(AddNewComment::class, function (AddNewComment $job) use ($user, $film) {
            return $job->subject === 'Гість'
                && $job->body === 'Коментар'
                && $job->status === 1
                && $job->film_id === $film->id
                && $job->user_id === $user->id;
        });
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
