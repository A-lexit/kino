<?php

namespace Tests\Feature\Jobs;

use App\Jobs\AddNewComment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AddNewCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_creates_comment_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();

        $job = new AddNewComment(
            'Тема коментаря',
            'Текст коментаря',
            1,
            $film->id,
            $user->id
        );

        $job->handle();

        $this->assertDatabaseHas('comments', [
            'subject' => 'Тема коментаря',
            'body' => 'Текст коментаря',
            'status' => 1,
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_job_creates_comment_for_guest(): void
    {
        $film = Film::factory()->create();

        $job = new AddNewComment(
            'Гість',
            'Текст коментаря від гостя',
            0,
            $film->id,
            null
        );

        $job->handle();

        $this->assertDatabaseHas('comments', [
            'subject' => 'Гість',
            'body' => 'Текст коментаря від гостя',
            'status' => 0,
            'film_id' => $film->id,
            'user_id' => null,
        ]);
    }

    public function test_job_can_be_dispatched_to_queue(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $film = Film::factory()->create();

        AddNewComment::dispatch(
            $user->name,
            'Текст у черзі',
            1,
            $film->id,
            $user->id
        );

        Queue::assertPushed(AddNewComment::class);
    }
}
