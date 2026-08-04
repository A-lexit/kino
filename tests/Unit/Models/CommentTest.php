<?php
namespace Tests\Unit\Models;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_allow_sets_status_to_one(): void
    {
        $comment = Comment::factory()->create([
            'status' => 0,
        ]);

        $comment->allow();

        $this->assertEquals(
            1,
            $comment->fresh()->status
        );
    }


    public function test_disallow_sets_status_to_zero(): void
    {
        $comment = Comment::factory()->create([
            'status' => 1,
        ]);

        $comment->disAllow();

        $this->assertEquals(
            0,
            $comment->fresh()->status
        );
    }


    public function test_toggle_status_turns_zero_into_one(): void
    {
        $comment = Comment::factory()->create([
            'status' => 0,
        ]);

        $comment->toggleStatus();

        $this->assertEquals(
            1,
            $comment->fresh()->status
        );
    }


    public function test_toggle_status_turns_one_into_zero(): void
    {
        $comment = Comment::factory()->create([
            'status' => 1,
        ]);

        $comment->toggleStatus();

        $this->assertEquals(
            0,
            $comment->fresh()->status
        );
    }


    public function test_film_relation_returns_default_model_when_film_is_missing(): void
    {
        $comment = Comment::factory()->create([
            'film_id' => null,
        ]);

        $this->assertEquals(
            'Гість (фільм видалено)',
            $comment->film->title
        );
    }


    public function test_created_at_for_humans_returns_human_readable_string(): void
    {
        Carbon::setTestNow('2026-07-20 12:00:00');

        $comment = Comment::factory()->create([
            'created_at' => now()->subHour(),
        ]);

        $this->assertStringContainsString(
            'тому',
            $comment->createdAtForHumans()
        );

        Carbon::setTestNow();
    }


    public function test_comment_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(
            $comment->user->is($user)
        );
    }


    public function test_comment_belongs_to_film(): void
    {
        $film = Film::factory()->create();

        $comment = Comment::factory()->create([
            'film_id' => $film->id,
        ]);

        $this->assertTrue(
            $comment->film->is($film)
        );
    }

}
