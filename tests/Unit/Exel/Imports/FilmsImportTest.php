<?php

namespace Tests\Unit\Imports;

use App\Enums\FilmStatus;
use App\Excel\Imports\FilmsImport;
use App\Models\Actor;
use App\Models\Age;
use App\Models\Category;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Quality;
use App\Models\Rating;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmsImportTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Year $year;
    protected Age $age;
    protected Quality $quality;
    protected Rating $rating;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create([
            'title' => 'Фільми',
        ]);

        $this->year = Year::factory()->create([
            'title' => '2026',
        ]);

        $this->age = Age::factory()->create([
            'title' => '16+',
        ]);

        $this->quality = Quality::factory()->create([
            'title' => 'HD',
        ]);

        $this->rating = Rating::factory()->create([
            'title' => '8.0',
        ]);
    }

    protected function validRow(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Тестовий фільм',
            'origin_title' => 'Test Film',
            'description' => 'Опис тестового фільму',
            'category' => 'Фільми',
            'year' => '2026',
            'age' => '16+',
            'quality' => 'HD',
            'rating' => '8.0',
            'actors' => '',
            'genres' => '',
            'countries' => '',
        ], $overrides);
    }

    public function test_new_film_is_created_as_draft(): void
    {
        $import = new FilmsImport('soft');

        $film = $import->model(
            $this->validRow()
        );

        $this->assertInstanceOf(Film::class, $film);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'title' => 'Тестовий фільм',
            'origin_title' => 'Test Film',
            'description' => 'Опис тестового фільму',
            'category_id' => $this->category->id,
            'year_id' => $this->year->id,
            'age_id' => $this->age->id,
            'quality_id' => $this->quality->id,
            'rating_id' => $this->rating->id,
            'publish_status' => FilmStatus::Draft->value,
        ]);

        $this->assertNotEmpty($film->slug);

        $this->assertSame(1, $import->successCount);
        $this->assertSame(0, $import->updatedCount);
        $this->assertSame(0, $import->skippedCount);
        $this->assertSame(0, $import->failCount);
    }

    public function test_new_film_without_category_is_created_as_draft(): void
    {
        $import = new FilmsImport('soft');

        $film = $import->model(
            $this->validRow([
                'category' => '',
            ])
        );

        $this->assertInstanceOf(Film::class, $film);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'category_id' => null,
            'publish_status' => FilmStatus::Draft->value,
        ]);

        $this->assertSame(1, $import->successCount);
    }

    public function test_existing_film_is_skipped_in_soft_mode(): void
    {
        $film = Film::factory()->create([
            'title' => 'Тестовий фільм',
            'description' => 'Старий опис',
        ]);

        $import = new FilmsImport('soft');

        $result = $import->model(
            $this->validRow([
                'description' => 'Новий опис',
            ])
        );

        $this->assertNull($result);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'description' => 'Старий опис',
        ]);

        $this->assertSame(0, $import->successCount);
        $this->assertSame(0, $import->updatedCount);
        $this->assertSame(1, $import->skippedCount);
    }

    public function test_existing_film_is_fully_updated_in_update_mode(): void
    {
        $film = Film::factory()->create([
            'title' => 'Тестовий фільм',
            'origin_title' => 'Old Original',
            'description' => 'Old description',
            'category_id' => null,
            'year_id' => null,
            'age_id' => null,
            'quality_id' => null,
            'rating_id' => null,
        ]);

        $import = new FilmsImport('update_only');

        $result = $import->model(
            $this->validRow([
                'origin_title' => 'New Original',
                'description' => 'New description',
            ])
        );

        $this->assertSame($film->id, $result->id);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'origin_title' => 'New Original',
            'description' => 'New description',
            'category_id' => $this->category->id,
            'year_id' => $this->year->id,
            'age_id' => $this->age->id,
            'quality_id' => $this->quality->id,
            'rating_id' => $this->rating->id,
        ]);

        $this->assertSame(1, $import->updatedCount);
        $this->assertSame(0, $import->successCount);
    }

    public function test_existing_film_is_updated_in_update_merge_mode_without_overwriting_existing_values(): void
    {
        $existingCategory = Category::factory()->create([
            'title' => 'Існуюча категорія',
        ]);

        $film = Film::factory()->create([
            'title' => 'Тестовий фільм',
            'origin_title' => 'Existing Original',
            'description' => 'Existing description',
            'category_id' => $existingCategory->id,
            'year_id' => $this->year->id,
            'age_id' => null,
            'quality_id' => $this->quality->id,
            'rating_id' => null,
        ]);

        $import = new FilmsImport('update_merge');

        $result = $import->model(
            $this->validRow([
                'origin_title' => 'Imported Original',
                'description' => 'Imported description',
            ])
        );

        $this->assertSame($film->id, $result->id);

        $film->refresh();

        // Існуючі значення не перезаписуються.
        $this->assertSame('Existing Original', $film->origin_title);
        $this->assertSame('Existing description', $film->description);
        $this->assertSame($existingCategory->id, $film->category_id);
        $this->assertSame($this->year->id, $film->year_id);
        $this->assertSame($this->quality->id, $film->quality_id);

        // Порожні значення заповнюються з імпорту.
        $this->assertSame($this->age->id, $film->age_id);
        $this->assertSame($this->rating->id, $film->rating_id);

        $this->assertSame(1, $import->updatedCount);
    }

    public function test_new_film_is_skipped_in_update_only_mode(): void
    {
        $import = new FilmsImport('update_only');

        $result = $import->model(
            $this->validRow([
                'title' => 'Новий фільм',
            ])
        );

        $this->assertNull($result);

        $this->assertDatabaseMissing('films', [
            'title' => 'Новий фільм',
        ]);

        $this->assertSame(1, $import->skippedCount);
        $this->assertSame(0, $import->successCount);
    }

    public function test_new_film_is_skipped_in_update_merge_mode(): void
    {
        $import = new FilmsImport('update_merge');

        $result = $import->model(
            $this->validRow([
                'title' => 'Новий фільм',
            ])
        );

        $this->assertNull($result);

        $this->assertDatabaseMissing('films', [
            'title' => 'Новий фільм',
        ]);

        $this->assertSame(1, $import->skippedCount);
    }

    public function test_missing_title_increases_fail_count(): void
    {
        $import = new FilmsImport('soft');

        $result = $import->model(
            $this->validRow([
                'title' => '',
            ])
        );

        $this->assertNull($result);

        $this->assertSame(1, $import->failCount);
        $this->assertSame(0, $import->successCount);
    }

    public function test_whitespace_only_title_increases_fail_count(): void
    {
        $import = new FilmsImport('soft');

        $result = $import->model(
            $this->validRow([
                'title' => '   ',
            ])
        );

        $this->assertNull($result);

        $this->assertSame(1, $import->failCount);
    }

    public function test_unknown_reference_is_allowed_in_soft_mode_and_generates_warning(): void
    {
        $import = new FilmsImport('soft');

        $film = $import->model(
            $this->validRow([
                'category' => 'Категорія якої немає',
            ])
        );

        $this->assertInstanceOf(Film::class, $film);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'category_id' => null,
        ]);

        $this->assertSame(1, $import->successCount);
        $this->assertNotEmpty($import->warnings);

        $this->assertTrue(
            collect($import->warnings)
                ->contains(
                    'Не знайдено категорія: "Категорія якої немає" — поле залишено порожнім.'
                )
        );
    }

    public function test_unknown_reference_in_strict_mode_fails_import(): void
    {
        $import = new FilmsImport('strict');

        $result = $import->model(
            $this->validRow([
                'category' => 'Категорія якої немає',
            ])
        );

        $this->assertNull($result);

        $this->assertDatabaseMissing('films', [
            'title' => 'Тестовий фільм',
        ]);

        $this->assertSame(1, $import->failCount);
        $this->assertNotEmpty($import->warnings);
    }

    public function test_actors_genres_and_countries_are_created_and_attached(): void
    {
        $import = new FilmsImport('soft');

        $film = $import->model(
            $this->validRow([
                'actors' => 'Леонардо Ді Капріо, Том Гарді',
                'genres' => 'Драма, Трилер',
                'countries' => 'США, Велика Британія',
            ])
        );

        $this->assertInstanceOf(Film::class, $film);

        $this->assertDatabaseHas('actors', [
            'name' => 'Леонардо Ді Капріо',
        ]);

        $this->assertDatabaseHas('actors', [
            'name' => 'Том Гарді',
        ]);

        $this->assertDatabaseHas('genres', [
            'title' => 'Драма',
        ]);

        $this->assertDatabaseHas('genres', [
            'title' => 'Трилер',
        ]);

        $this->assertDatabaseHas('countries', [
            'title' => 'США',
        ]);

        $this->assertDatabaseHas('countries', [
            'title' => 'Велика Британія',
        ]);

        $this->assertCount(2, $film->actors);
        $this->assertCount(2, $film->genres);
        $this->assertCount(2, $film->countries);
    }

    public function test_merge_mode_keeps_existing_relationships_and_adds_new_ones(): void
    {
        $existingActor = Actor::factory()->create([
            'name' => 'Старий актор',
        ]);

        $existingGenre = Genre::factory()->create([
            'title' => 'Стара драма',
        ]);

        $film = Film::factory()->create([
            'title' => 'Тестовий фільм',
        ]);

        $film->actors()->attach($existingActor->id);
        $film->genres()->attach($existingGenre->id);

        $import = new FilmsImport('update_merge');

        $import->model(
            $this->validRow([
                'actors' => 'Новий актор',
                'genres' => 'Новий жанр',
                'countries' => '',
            ])
        );

        $film->load('actors', 'genres');

        $this->assertTrue(
            $film->actors->contains('name', 'Старий актор')
        );

        $this->assertTrue(
            $film->actors->contains('name', 'Новий актор')
        );

        $this->assertTrue(
            $film->genres->contains('title', 'Стара драма')
        );

        $this->assertTrue(
            $film->genres->contains('title', 'Новий жанр')
        );

        $this->assertCount(2, $film->actors);
        $this->assertCount(2, $film->genres);
    }

    public function test_non_merge_mode_replaces_existing_relationships(): void
    {
        $existingActor = Actor::factory()->create([
            'name' => 'Старий актор',
        ]);

        $film = Film::factory()->create([
            'title' => 'Тестовий фільм',
        ]);

        $film->actors()->attach($existingActor->id);

        $import = new FilmsImport('update_only');

        $import->model(
            $this->validRow([
                'actors' => 'Новий актор',
                'genres' => '',
                'countries' => '',
            ])
        );

        $film->load('actors');

        $this->assertFalse(
            $film->actors->contains('name', 'Старий актор')
        );

        $this->assertTrue(
            $film->actors->contains('name', 'Новий актор')
        );

        $this->assertCount(1, $film->actors);
    }

    public function test_insert_update_mode_creates_new_film(): void
    {
        $import = new FilmsImport('insert_update');

        $film = $import->model(
            $this->validRow([
                'title' => 'Новий фільм',
            ])
        );

        $this->assertInstanceOf(Film::class, $film);

        $this->assertDatabaseHas('films', [
            'title' => 'Новий фільм',
            'publish_status' => FilmStatus::Draft->value,
        ]);

        $this->assertSame(1, $import->successCount);
    }

    public function test_insert_update_mode_updates_existing_film(): void
    {
        $film = Film::factory()->create([
            'title' => 'Тестовий фільм',
            'description' => 'Старий опис',
        ]);

        $import = new FilmsImport('insert_update');

        $result = $import->model(
            $this->validRow([
                'description' => 'Новий опис',
            ])
        );

        $this->assertSame($film->id, $result->id);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'description' => 'Новий опис',
        ]);

        $this->assertSame(1, $import->updatedCount);
    }

    public function test_insert_update_merge_mode_creates_new_film(): void
    {
        $import = new FilmsImport('insert_update_merge');

        $film = $import->model(
            $this->validRow([
                'title' => 'Новий merge фільм',
                'actors' => 'Новий актор',
            ])
        );

        $this->assertInstanceOf(Film::class, $film);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'publish_status' => FilmStatus::Draft->value,
        ]);

        $this->assertDatabaseHas('actors', [
            'name' => 'Новий актор',
        ]);

        $this->assertSame(1, $import->successCount);
    }

    public function test_empty_optional_relationship_fields_do_not_create_records(): void
    {
        $import = new FilmsImport('soft');

        $film = $import->model(
            $this->validRow([
                'actors' => '',
                'genres' => '',
                'countries' => '',
            ])
        );

        $this->assertInstanceOf(Film::class, $film);

        $this->assertCount(0, $film->actors);
        $this->assertCount(0, $film->genres);
        $this->assertCount(0, $film->countries);
    }
}


