<?php
namespace Tests\Feature\Front;

use App\Media\ImageMedia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_displays_profile_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile'));

        $response->assertOk();

        $response->assertViewIs('users.edit');

        $response->assertViewHas('user');

        $this->assertEquals(
            $user->id,
            $response->viewData('user')->id
        );
    }


    public function test_update_updates_profile_without_avatar(): void
    {
        $user = User::factory()->create([
            'name' => 'Old name',
            'email' => 'old@example.com',
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'New name',
                'email' => 'new@example.com',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $user->refresh();

        $this->assertEquals('New name', $user->name);
        $this->assertEquals('new@example.com', $user->email);
    }


    public function test_update_uploads_new_avatar(): void
    {
        $user = User::factory()->create([
            'avatar' => 'avatars/old.webp',
        ]);

        $media = Mockery::mock(ImageMedia::class);

        $media->shouldReceive('delete')
            ->once()
            ->with('avatars/old.webp');

        $media->shouldReceive('upload')
            ->once()
            ->andReturn('avatars/new.webp');

        $this->app->instance(ImageMedia::class, $media);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $user->refresh();

        $this->assertEquals(
            'avatars/new.webp',
            $user->avatar
        );
    }


    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

}
