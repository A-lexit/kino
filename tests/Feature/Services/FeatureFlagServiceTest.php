<?php
namespace Tests\Feature\Services;

use App\Models\Film;
use App\Models\User;
use App\Services\FeatureFlagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureFlagServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FeatureFlagService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FeatureFlagService();

        // Створюємо та логінимо користувача для обходу помилки в FilmObserver
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }


    public function test_set_featured_changes_flag_to_one_and_saves_to_db(): void
    {
        $film = Film::factory()->create([
            'title' => 'Test Film Title',
            'is_featured' => 0
        ]);

        $this->service->setFeatured($film);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'is_featured' => 1,
        ]);
    }


    public function test_set_standart_changes_flag_to_zero_and_saves_to_db(): void
    {
        $film = Film::factory()->create([
            'title' => 'Test Film Title',
            'is_featured' => 1
        ]);

        $this->service->setStandart($film);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'is_featured' => 0,
        ]);
    }


    public function test_toggle_featured_sets_standart_when_value_is_null(): void
    {
        $film = Film::factory()->create([
            'title' => 'Test Film Title',
            'is_featured' => 1
        ]);

        $this->service->toggleFeatured($film, null);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'is_featured' => 0,
        ]);
    }


    public function test_toggle_featured_sets_featured_when_value_is_not_null(): void
    {
        $film = Film::factory()->create([
            'title' => 'Test Film Title',
            'is_featured' => 0
        ]);

        $this->service->toggleFeatured($film, 'on');

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'is_featured' => 1,
        ]);
    }

}
