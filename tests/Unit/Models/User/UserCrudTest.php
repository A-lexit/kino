<?php
namespace Tests\Unit\Models\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_creates_new_user(): void
    {
        $user = User::add([
            'name' => 'Alex',
            'email' => 'alex@example.com',
            'password' => 'password',
        ]);

        $this->assertInstanceOf(
            User::class,
            $user
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Alex',
            'email' => 'alex@example.com',
        ]);
    }


    public function test_edit_updates_user_fields(): void
    {
        $user = User::factory()->create([
            'name' => 'Old name',
            'email' => 'old@example.com',
        ]);

        $user->edit([
            'name' => 'New name',
            'email' => 'new@example.com',
        ]);

        $user->refresh();

        $this->assertSame(
            'New name',
            $user->name
        );

        $this->assertSame(
            'new@example.com',
            $user->email
        );
    }


    public function test_remove_deletes_user_without_avatar(): void
    {
        $user = User::factory()->create([
            'avatar' => null,
        ]);

        $id = $user->id;

        $user->remove();

        $this->assertDatabaseMissing('users', [
            'id' => $id,
        ]);
    }


    public function test_remove_deletes_user_and_avatar(): void
    {
        Storage::fake();

        Storage::put(
            'uploads/avatar.jpg',
            'avatar'
        );

        $user = User::factory()->create([
            'avatar' => 'avatar.jpg',
        ]);

        $id = $user->id;

        $user->remove();

        Storage::assertMissing(
            'uploads/avatar.jpg'
        );

        $this->assertDatabaseMissing('users', [
            'id' => $id,
        ]);
    }

}
