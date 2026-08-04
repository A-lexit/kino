<?php
namespace Tests\Feature\Services;

use App\Constants\FilmStatus;
use App\Models\Film;
use App\Models\User;
use App\Services\DraftFlagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftFlagServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DraftFlagService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DraftFlagService();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }


    public function test_set_draft_changes_status_to_draft(): void
    {
        $film = Film::factory()->create(['publish_status' => FilmStatus::PUBLISHED]);

        $this->service->setDraft($film);

        $this->assertDatabaseHas('films', [
            'id'      => $film->id,
            'publish_status' => FilmStatus::DRAFT,
        ]);
    }


    public function test_set_public_changes_status_to_published(): void
    {
        $film = Film::factory()->create(['publish_status' => FilmStatus::DRAFT]);

        $this->service->setPublic($film);

        $this->assertDatabaseHas('films', [
            'id'      => $film->id,
            'publish_status' => FilmStatus::PUBLISHED,
        ]);
    }


    public function test_toggle_status_sets_draft_when_value_is_null(): void
    {
        $film = Film::factory()->create(['publish_status' => FilmStatus::PUBLISHED]);

        $this->service->togglePublishStatus($film, null);

        $this->assertDatabaseHas('films', [
            'id'      => $film->id,
            'publish_status' => FilmStatus::DRAFT,
        ]);
    }


    public function test_toggle_status_sets_published_when_value_is_not_null(): void
    {
        $film = Film::factory()->create(['publish_status' => FilmStatus::DRAFT]);

        $this->service->togglePublishStatus($film, 'on');

        $this->assertDatabaseHas('films', [
            'id'      => $film->id,
            'publish_status' => FilmStatus::PUBLISHED,
        ]);
    }


    public function test_set_draft_does_not_change_already_draft_film(): void
    {
        $film = Film::factory()->create(['publish_status' => FilmStatus::DRAFT]);

        $this->service->setDraft($film);

        $this->assertDatabaseHas('films', [
            'id'      => $film->id,
            'publish_status' => FilmStatus::DRAFT,
        ]);
    }


    public function test_set_public_does_not_change_already_published_film(): void
    {
        $film = Film::factory()->create(['publish_status' => FilmStatus::PUBLISHED]);

        $this->service->setPublic($film);

        $this->assertDatabaseHas('films', [
            'id'      => $film->id,
            'publish_status' => FilmStatus::PUBLISHED,
        ]);
    }

}
