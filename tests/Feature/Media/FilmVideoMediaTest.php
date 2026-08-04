<?php
namespace Tests\Feature\Media;

use App\Media\FilmVideoMedia;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FilmVideoMediaTest extends TestCase
{
    use RefreshDatabase;

    protected FilmVideoMedia $media;
    protected string $disk;


    protected function setUp(): void
    {
        parent::setUp();

        $this->disk = config('filesystems.default');

        Storage::fake($this->disk);

        $this->media = new FilmVideoMedia();
    }


    public function test_upload_trailer_does_nothing_when_file_is_missing(): void
    {
        $request = Request::create('/', 'POST');

        $data = [
            'trailer_file' => 'old.mp4',
            'trailer_youtube_id' => 'abc123',
        ];

        $this->media->uploadTrailer($request, $data);

        $this->assertEquals('old.mp4', $data['trailer_file']);
        $this->assertEquals('abc123', $data['trailer_youtube_id']);
    }


    public function test_upload_trailer_ignores_invalid_extension(): void
    {
        $file = UploadedFile::fake()->create('video.avi', 100);

        $request = Request::create('/', 'POST', [], [], [
            'trailer_file' => $file,
        ]);

        $data = [];

        $this->media->uploadTrailer($request, $data);

        $this->assertArrayNotHasKey('trailer_file', $data);
    }


    public function test_upload_trailer_stores_video(): void
    {
        $file = UploadedFile::fake()->create('video.mp4', 100);

        $request = Request::create('/', 'POST', [], [], [
            'trailer_file' => $file,
        ]);

        $data = [
            'trailer_youtube_id' => 'youtube-id',
        ];

        $this->media->uploadTrailer($request, $data);

        $this->assertArrayHasKey('trailer_file', $data);

        Storage::disk($this->disk)
            ->assertExists($data['trailer_file']);

        $this->assertNull($data['trailer_youtube_id']);
    }


    public function test_upload_trailer_deletes_previous_file(): void
    {
        Storage::disk($this->disk)
            ->put('trailers/old.mp4', 'video');

        $film = new Film();
        $film->trailer_file = 'trailers/old.mp4';

        $file = UploadedFile::fake()->create('video.mp4', 100);

        $request = Request::create('/', 'POST', [], [], [
            'trailer_file' => $file,
        ]);

        $data = [];

        $this->media->uploadTrailer($request, $data, $film);

        Storage::disk($this->disk)
            ->assertMissing('trailers/old.mp4');

        Storage::disk($this->disk)
            ->assertExists($data['trailer_file']);
    }


    public function test_delete_removes_existing_file(): void
    {
        Storage::disk($this->disk)
            ->put('trailers/test.mp4', 'video');

        Storage::disk($this->disk)
            ->assertExists('trailers/test.mp4');

        $this->media->delete('trailers/test.mp4');

        Storage::disk($this->disk)
            ->assertMissing('trailers/test.mp4');
    }


    public function test_delete_accepts_null(): void
    {
        $this->media->delete(null);

        $this->assertTrue(true);
    }


    public function test_delete_ignores_missing_file(): void
    {
        $this->media->delete('missing/video.mp4');

        $this->assertTrue(true);
    }

}
