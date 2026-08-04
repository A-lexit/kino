<?php
namespace Tests\Feature\Media;

use App\Media\ImageMedia;
use App\Media\UserImageResolver;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class UserImageResolverTest extends TestCase
{
    protected UserImageResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new UserImageResolver();
    }


    public function test_returns_default_image_when_user_has_no_avatar(): void
    {
        $user = new User();
        $user->avatar = null;

        $this->assertEquals(
            asset('img/no-image.png'),
            $this->resolver->image($user)
        );
    }


    public function test_returns_image_media_url_when_avatar_exists(): void
    {
        $user = new User();
        $user->avatar = 'avatars/user.webp';

        $mock = Mockery::mock(ImageMedia::class);

        $mock->shouldReceive('url')
            ->once()
            ->with('avatars/user.webp')
            ->andReturn('https://example.com/storage/avatars/user.webp');

        $this->app->instance(ImageMedia::class, $mock);

        $this->assertEquals(
            'https://example.com/storage/avatars/user.webp',
            $this->resolver->image($user)
        );
    }


    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

}
