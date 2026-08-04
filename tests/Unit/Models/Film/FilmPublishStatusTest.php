<?php
namespace Tests\Unit\Models\Film;

use App\Enums\FilmStatus;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmPublishStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_publish_status_sets_published_when_value_is_published(): void
    {
        $film = Film::factory()->create([
            'publish_status' => FilmStatus::Draft,
        ]);

        $film->togglePublishStatus(FilmStatus::Published->value);

        $film->refresh();

        $this->assertSame(
            FilmStatus::Published,
            $film->publish_status
        );
    }


    public function test_toggle_publish_status_sets_draft_when_value_is_draft(): void
    {
        $film = Film::factory()->create([
            'publish_status' => FilmStatus::Published,
        ]);

        $film->togglePublishStatus(FilmStatus::Draft->value);

        $film->refresh();

        $this->assertSame(
            FilmStatus::Draft,
            $film->publish_status
        );
    }


    public function test_toggle_publish_status_sets_draft_when_value_is_null(): void
    {
        $film = Film::factory()->create([
            'publish_status' => FilmStatus::Published,
        ]);

        $film->togglePublishStatus(null);

        $film->refresh();

        $this->assertSame(
            FilmStatus::Draft,
            $film->publish_status
        );
    }


    public function test_toggle_publish_status_sets_draft_when_value_is_invalid(): void
    {
        $film = Film::factory()->create([
            'publish_status' => FilmStatus::Published,
        ]);

        $film->togglePublishStatus('invalid');

        $film->refresh();

        $this->assertSame(
            FilmStatus::Draft,
            $film->publish_status
        );
    }


    public function test_toggle_publish_status_persists_changes_to_database(): void
    {
        $film = Film::factory()->create([
            'publish_status' => FilmStatus::Draft,
        ]);

        $film->togglePublishStatus(FilmStatus::Published->value);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'publish_status' => FilmStatus::Published->value,
        ]);
    }

}
