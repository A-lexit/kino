<?php
namespace Tests\Unit\Models\Film;

use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmFeaturedTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_featured_sets_featured_when_value_is_true(): void
    {
        $film = Film::factory()->create([
            'is_featured' => false,
        ]);

        $film->toggleFeatured(true);

        $film->refresh();

        $this->assertTrue($film->is_featured);
    }


    public function test_toggle_featured_sets_not_featured_when_value_is_false(): void
    {
        $film = Film::factory()->create([
            'is_featured' => true,
        ]);

        $film->toggleFeatured(false);

        $film->refresh();

        $this->assertFalse($film->is_featured);
    }


    public function test_toggle_featured_sets_not_featured_when_value_is_null(): void
    {
        $film = Film::factory()->create([
            'is_featured' => true,
        ]);

        $film->toggleFeatured(null);

        $film->refresh();

        $this->assertFalse($film->is_featured);
    }


    public function test_toggle_featured_sets_featured_when_value_is_one(): void
    {
        $film = Film::factory()->create([
            'is_featured' => false,
        ]);

        $film->toggleFeatured(1);

        $film->refresh();

        $this->assertTrue($film->is_featured);
    }


    public function test_toggle_featured_sets_not_featured_when_value_is_zero(): void
    {
        $film = Film::factory()->create([
            'is_featured' => true,
        ]);

        $film->toggleFeatured(0);

        $film->refresh();

        $this->assertFalse($film->is_featured);
    }


    public function test_toggle_featured_persists_changes_to_database(): void
    {
        $film = Film::factory()->create([
            'is_featured' => false,
        ]);

        $film->toggleFeatured(true);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'is_featured' => true,
        ]);
    }

}
