<?php
namespace Tests\Feature\Media;

use App\Media\ImageStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageStorageTest extends TestCase
{
    use RefreshDatabase;

    protected ImageStorage $storage;
    protected string $disk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disk = config('filesystems.default');

        Storage::fake($this->disk);

        $this->storage = new ImageStorage();
    }


    public function test_store_saves_file_and_returns_path(): void
    {
        $temp = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($temp, 'fake-image');

        $path = $this->storage->store($temp, 'posters');

        $this->assertEquals(
            'posters/' . basename($temp),
            $path
        );

        Storage::disk($this->disk)->assertExists($path);
    }


    public function test_store_deletes_temp_file(): void
    {
        $temp = tempnam(sys_get_temp_dir(), 'img');

        file_put_contents($temp, 'test');

        $this->storage->store($temp, 'posters');

        $this->assertFileDoesNotExist($temp);
    }


    public function test_delete_removes_existing_file(): void
    {
        Storage::disk($this->disk)->put('posters/test.webp', 'image');

        Storage::disk($this->disk)->assertExists('posters/test.webp');

        $this->storage->delete('posters/test.webp');

        Storage::disk($this->disk)->assertMissing('posters/test.webp');
    }


    public function test_delete_accepts_null(): void
    {
        $this->storage->delete(null);

        $this->assertTrue(true);
    }


    public function test_delete_accepts_empty_string(): void
    {
        $this->storage->delete('');

        $this->assertTrue(true);
    }


    public function test_delete_non_existing_file_does_not_throw(): void
    {
        $this->storage->delete('missing/file.webp');

        $this->assertTrue(true);
    }


    public function test_url_returns_default_for_null(): void
    {
        $this->assertEquals(
            asset('defaults/fake_movie_cover.webp'),
            $this->storage->url(null)
        );
    }


    public function test_url_returns_default_for_empty_string(): void
    {
        $this->assertEquals(
            asset('defaults/fake_movie_cover.webp'),
            $this->storage->url('')
        );
    }


    public function test_url_returns_http_url_without_changes(): void
    {
        $url = 'https://example.com/image.webp';

        $this->assertEquals(
            $url,
            $this->storage->url($url)
        );
    }


    public function test_url_builds_storage_url(): void
    {
        $expected = Storage::disk($this->disk)
            ->url('posters/test.webp');

        $this->assertEquals(
            $expected,
            $this->storage->url('posters/test.webp')
        );
    }


    public function test_exists_returns_true_for_existing_file(): void
    {
        Storage::disk($this->disk)->put('posters/test.webp', 'image');

        $this->assertTrue(
            $this->storage->exists('posters/test.webp')
        );
    }


    public function test_exists_returns_false_for_missing_file(): void
    {
        $this->assertFalse(
            $this->storage->exists('posters/missing.webp')
        );
    }

}
