<?php
namespace Tests\Feature\Jobs;

use App\Jobs\AddNewComment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddNewCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_creates_comment(): void
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

}
