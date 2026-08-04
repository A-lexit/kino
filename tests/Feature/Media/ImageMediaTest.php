<?php
namespace Tests\Feature\Media;

use App\Media\ImageMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageMediaTest extends TestCase
{
    use RefreshDatabase;
    protected ImageMedia $media;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));

        $this->media = app(ImageMedia::class);
    }


    public function test_upload_saves_file_converts_to_webp_and_returns_path(): void
    {
        $file = UploadedFile::fake()->image('poster.webp', 100, 100);

        $path = $this->media->upload($file, 'test_folder');

        $this->assertStringStartsWith('test_folder/', $path);
        $this->assertStringEndsWith('.webp', $path);

        $this->assertTrue($this->media->exists($path));
    }


    public function test_delete_removes_file_physically(): void
    {
        Storage::disk(config('filesystems.default'))
            ->put('test_folder/test.webp', 'dummy');

        $this->assertTrue(
            Storage::disk(config('filesystems.default'))
                ->exists('test_folder/test.webp')
        );

        $this->media->delete('test_folder/test.webp');

        $this->assertFalse(
            Storage::disk(config('filesystems.default'))
                ->exists('test_folder/test.webp')
        );
    }


    public function test_delete_ignores_null_or_empty_path(): void
    {
        $this->media->delete(null);
        $this->media->delete('');

        $this->assertTrue(true);
    }


    public function test_url_returns_asset_path_or_default_image(): void
    {
        $this->assertEquals(
            asset('defaults/fake_movie_cover.webp'),
            $this->media->url(null)
        );

        $this->assertEquals(
            asset('defaults/fake_movie_cover.webp'),
            $this->media->url('')
        );

        $this->assertEquals(
            Storage::disk(config('filesystems.default'))
                ->url('uploads/posters/1.webp'),
            $this->media->url('uploads/posters/1.webp')
        );
    }

}
