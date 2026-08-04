<?php
namespace Tests\Feature\Media;

use App\Media\FilmVideoResolver;
use App\Models\Film;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FilmVideoResolverTest extends TestCase
{
    protected FilmVideoResolver $resolver;
    protected string $disk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disk = config('filesystems.default');
        Storage::fake($this->disk);

        $this->resolver = new FilmVideoResolver();
    }


    /** @test */
    public function has_trailer_returns_false_when_no_trailer_exists(): void
    {
        $film = new Film();

        $this->assertFalse($this->resolver->hasTrailer($film));
    }


    /** @test */
    public function has_trailer_returns_true_for_youtube(): void
    {
        $film = new Film();
        $film->trailer_youtube_id = 'abcdefghijk';

        $this->assertTrue($this->resolver->hasTrailer($film));
    }


    /** @test */
    public function has_trailer_returns_true_for_uploaded_file(): void
    {
        $film = new Film();
        $film->trailer_file = 'trailers/test.mp4';

        $this->assertTrue($this->resolver->hasTrailer($film));
    }


    /** @test */
    public function youtube_embed_url_returns_null_when_missing(): void
    {
        $film = new Film();

        $this->assertNull($this->resolver->youtubeEmbedUrl($film));
    }


    /** @test */
    public function youtube_embed_url_returns_embed_link(): void
    {
        $film = new Film();
        $film->trailer_youtube_id = 'abcdefghijk';

        $this->assertEquals(
            'https://www.youtube.com/embed/abcdefghijk',
            $this->resolver->youtubeEmbedUrl($film)
        );
    }


    /** @test */
    public function file_url_returns_null_when_missing(): void
    {
        $film = new Film();

        $this->assertNull($this->resolver->fileUrl($film));
    }


    /** @test */
    public function file_url_returns_storage_url(): void
    {
        $film = new Film();
        $film->trailer_file = 'trailers/video.mp4';

        $this->assertEquals(
            Storage::disk($this->disk)->url('trailers/video.mp4'),
            $this->resolver->fileUrl($film)
        );
    }


    /** @test */
    public function extract_youtube_id_accepts_raw_id(): void
    {
        $id = 'abcdefghijk';

        $this->assertEquals(
            $id,
            FilmVideoResolver::extractYoutubeId($id)
        );
    }


    /** @test */
    public function extract_youtube_id_accepts_regular_youtube_url(): void
    {
        $url = 'https://www.youtube.com/watch?v=abcdefghijk';

        $this->assertEquals(
            'abcdefghijk',
            FilmVideoResolver::extractYoutubeId($url)
        );
    }


    /** @test */
    public function extract_youtube_id_accepts_short_url(): void
    {
        $url = 'https://youtu.be/abcdefghijk';

        $this->assertEquals(
            'abcdefghijk',
            FilmVideoResolver::extractYoutubeId($url)
        );
    }


    /** @test */
    public function extract_youtube_id_accepts_embed_url(): void
    {
        $url = 'https://www.youtube.com/embed/abcdefghijk';

        $this->assertEquals(
            'abcdefghijk',
            FilmVideoResolver::extractYoutubeId($url)
        );
    }


    /** @test */
    public function extract_youtube_id_accepts_shorts_url(): void
    {
        $url = 'https://youtube.com/shorts/abcdefghijk';

        $this->assertEquals(
            'abcdefghijk',
            FilmVideoResolver::extractYoutubeId($url)
        );
    }


    /** @test */
    public function extract_youtube_id_returns_null_for_invalid_string(): void
    {
        $this->assertNull(
            FilmVideoResolver::extractYoutubeId('not youtube')
        );
    }

}
