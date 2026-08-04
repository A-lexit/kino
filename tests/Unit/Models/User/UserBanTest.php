<?php
namespace Tests\Unit\Models\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserBanTest extends TestCase
{
    use RefreshDatabase;

    public function test_ban_sets_user_as_banned(): void
    {
        $user = User::factory()->create([
            'is_banned' => 0,
        ]);

        $user->ban();

        $user->refresh();

        $this->assertEquals(
            1,
            $user->is_banned
        );
    }


    public function test_unban_sets_user_as_not_banned(): void
    {
        $user = User::factory()->create([
            'is_banned' => 1,
        ]);

        $user->unban();

        $user->refresh();

        $this->assertEquals(
            0,
            $user->is_banned
        );
    }


    public function test_toggle_ban_bans_user_when_not_banned(): void
    {
        $user = User::factory()->create([
            'is_banned' => 0,
        ]);

        $user->toggleBan();

        $user->refresh();

        $this->assertEquals(
            1,
            $user->is_banned
        );
    }


    public function test_toggle_ban_unbans_user_when_banned(): void
    {
        $user = User::factory()->create([
            'is_banned' => 1,
        ]);

        $user->toggleBan();

        $user->refresh();

        $this->assertEquals(
            0,
            $user->is_banned
        );
    }


    public function test_ban_persists_to_database(): void
    {
        $user = User::factory()->create([
            'is_banned' => 0,
        ]);

        $user->ban();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_banned' => 1,
        ]);
    }


    public function test_unban_persists_to_database(): void
    {
        $user = User::factory()->create([
            'is_banned' => 1,
        ]);

        $user->unban();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_banned' => 0,
        ]);
    }

}
