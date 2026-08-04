<?php
namespace Tests\Unit\Models\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_avatar_does_nothing_when_image_is_null(): void
    {
        Storage::fake();

        $user = User::factory()->create([
            'avatar' => null,
        ]);

        $user->uploadAvatar(null);

        $user->refresh();

        $this->assertNull(
            $user->avatar
        );
    }


    public function test_upload_avatar_saves_avatar(): void
    {
        Storage::fake();

        $user = User::factory()->create([
            'avatar' => null,
        ]);

        $image = UploadedFile::fake()->image('avatar.jpg');

        $user->uploadAvatar($image);

        $user->refresh();

        $this->assertNotNull(
            $user->avatar
        );

        Storage::assertExists(
            'uploads/' . $user->avatar
        );
    }


    public function test_upload_avatar_removes_old_avatar_before_uploading_new_one(): void
    {
        Storage::fake();

        Storage::put(
            'uploads/old.jpg',
            'old-avatar'
        );

        $user = User::factory()->create([
            'avatar' => 'old.jpg',
        ]);

        $image = UploadedFile::fake()->image('new.jpg');

        $user->uploadAvatar($image);

        $user->refresh();

        Storage::assertMissing(
            'uploads/old.jpg'
        );

        Storage::assertExists(
            'uploads/' . $user->avatar
        );
    }


    public function test_remove_avatar_deletes_avatar_file(): void
    {
        Storage::fake();

        Storage::put(
            'uploads/avatar.jpg',
            'avatar'
        );

        $user = User::factory()->create([
            'avatar' => 'avatar.jpg',
        ]);

        $user->removeAvatar();

        Storage::assertMissing(
            'uploads/avatar.jpg'
        );
    }


    public function test_remove_avatar_does_nothing_when_avatar_is_null(): void
    {
        Storage::fake();

        $user = User::factory()->create([
            'avatar' => null,
        ]);

        $user->removeAvatar();

        $this->assertTrue(true);
    }

}
