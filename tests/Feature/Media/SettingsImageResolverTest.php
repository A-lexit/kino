<?php
namespace Tests\Feature\Media;

use App\Media\ImageMedia;
use App\Media\SettingsImageResolver;
use App\Models\Setting;
use Mockery;
use Tests\TestCase;

class SettingsImageResolverTest extends TestCase
{
    protected SettingsImageResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new SettingsImageResolver();
    }


    protected function mockImageMedia(string $expectedPath, string $returnUrl): void
    {
        $mock = Mockery::mock(ImageMedia::class);

        $mock->shouldReceive('url')
            ->once()
            ->with($expectedPath)
            ->andReturn($returnUrl);

        $this->app->instance(ImageMedia::class, $mock);
    }


    public function test_logo_returns_default_when_settings_are_null(): void
    {
        $this->assertEquals(
            asset('defaults/logo.webp'),
            $this->resolver->logo(null)
        );
    }


    public function test_logo_returns_default_when_logo_is_empty(): void
    {
        $settings = new Setting();
        $settings->logo = '';

        $this->assertEquals(
            asset('defaults/logo.webp'),
            $this->resolver->logo($settings)
        );
    }


    public function test_logo_returns_image_media_url(): void
    {
        $settings = new Setting();
        $settings->logo = 'logos/logo.webp';

        $this->mockImageMedia(
            'logos/logo.webp',
            'https://site.test/storage/logos/logo.webp'
        );

        $this->assertEquals(
            'https://site.test/storage/logos/logo.webp',
            $this->resolver->logo($settings)
        );
    }


    public function test_favicon_returns_default_when_settings_are_null(): void
    {
        $this->assertEquals(
            asset('default_favicon.ico'),
            $this->resolver->favicon(null)
        );
    }


    public function test_favicon_returns_default_when_path_is_empty(): void
    {
        $settings = new Setting();
        $settings->favicon = '';

        $this->assertEquals(
            asset('default_favicon.ico'),
            $this->resolver->favicon($settings)
        );
    }


    public function test_favicon_returns_image_media_url(): void
    {
        $settings = new Setting();
        $settings->favicon = 'favicons/favicon.webp';

        $this->mockImageMedia(
            'favicons/favicon.webp',
            'https://site.test/storage/favicons/favicon.webp'
        );

        $this->assertEquals(
            'https://site.test/storage/favicons/favicon.webp',
            $this->resolver->favicon($settings)
        );
    }


    public function test_favicon16_returns_correct_suffix(): void
    {
        $settings = new Setting();
        $settings->favicon = 'favicons/favicon.webp';

        $this->mockImageMedia(
            'favicons/favicon.webp',
            'https://site.test/storage/favicons/favicon.webp'
        );

        $this->assertEquals(
            'https://site.test/storage/favicons/favicon_16.webp',
            $this->resolver->favicon16($settings)
        );
    }


    public function test_favicon32_returns_correct_suffix(): void
    {
        $settings = new Setting();
        $settings->favicon = 'favicons/favicon.webp';

        $this->mockImageMedia(
            'favicons/favicon.webp',
            'https://site.test/storage/favicons/favicon.webp'
        );

        $this->assertEquals(
            'https://site.test/storage/favicons/favicon_32.webp',
            $this->resolver->favicon32($settings)
        );
    }


    public function test_apple_touch_icon_returns_correct_suffix(): void
    {
        $settings = new Setting();
        $settings->favicon = 'favicons/favicon.webp';

        $this->mockImageMedia(
            'favicons/favicon.webp',
            'https://site.test/storage/favicons/favicon.webp'
        );

        $this->assertEquals(
            'https://site.test/storage/favicons/favicon_180.webp',
            $this->resolver->appleTouchIcon($settings)
        );
    }


    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

}
