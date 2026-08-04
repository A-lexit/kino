<?php
namespace Tests\Unit\Models\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserBootTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_role_sets_is_admin_to_one_on_create(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
            'is_admin' => 0,
        ]);

        $this->assertEquals(
            1,
            $user->fresh()->is_admin
        );
    }


    public function test_non_admin_role_sets_is_admin_to_zero_on_create(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Editor,
            'is_admin' => 1,
        ]);

        $this->assertEquals(
            0,
            $user->fresh()->is_admin
        );
    }


    public function test_changing_role_to_admin_updates_is_admin(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $user->role = UserRole::Admin;
        $user->save();

        $this->assertEquals(
            1,
            $user->fresh()->is_admin
        );
    }


    public function test_changing_role_from_admin_updates_is_admin(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $user->role = UserRole::Viewer;
        $user->save();

        $this->assertEquals(
            0,
            $user->fresh()->is_admin
        );
    }

}
