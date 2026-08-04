<?php
namespace Tests\Unit\Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_user_state(): void
    {
        $user = User::factory()->make();

        $this->assertSame(
            UserRole::User,
            $user->role
        );

        $this->assertSame(
            0,
            $user->is_admin
        );

        $this->assertSame(
            0,
            $user->is_banned
        );

        $this->assertNull(
            $user->avatar
        );
    }


    public function test_admin_state(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertSame(
            UserRole::Admin,
            $user->role
        );

        // booted() автоматично синхронізує is_admin
        $this->assertSame(
            1,
            $user->is_admin
        );
    }


    public function test_editor_state(): void
    {
        $user = User::factory()->editor()->create();

        $this->assertSame(
            UserRole::Editor,
            $user->role
        );

        $this->assertSame(
            0,
            $user->is_admin
        );
    }


    public function test_viewer_state(): void
    {
        $user = User::factory()->viewer()->create();

        $this->assertSame(
            UserRole::Viewer,
            $user->role
        );

        $this->assertSame(
            0,
            $user->is_admin
        );
    }


    public function test_banned_state(): void
    {
        $user = User::factory()->banned()->create();

        $this->assertSame(
            1,
            $user->is_banned
        );
    }


    public function test_states_can_be_combined(): void
    {
        $user = User::factory()
            ->admin()
            ->banned()
            ->create();

        $this->assertSame(
            UserRole::Admin,
            $user->role
        );

        $this->assertSame(
            1,
            $user->is_admin
        );

        $this->assertSame(
            1,
            $user->is_banned
        );
    }

}
