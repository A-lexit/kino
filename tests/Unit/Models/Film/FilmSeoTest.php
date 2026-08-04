<?php
namespace Tests\Unit\Models\Film;

use App\Models\Category;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_trimmed_description_when_description_exists(): void
    {
        $film = Film::factory()->create([
            'title' => 'Матрикс',
            'description' => str_repeat('Текст ', 60),
        ]);

        $result = $film->seoDescription();

        $this->assertNotEmpty($result);

        $this->assertLessThanOrEqual(
            163,
            mb_strlen($result)
        );
    }


    public function test_builds_description_when_description_is_empty(): void
    {
        $category = Category::factory()->create([
            'title' => 'Фільми',
        ]);

        $year = Year::factory()->create([
            'title' => 2025,
        ]);

        $genre = Genre::factory()->create([
            'title' => 'Бойовик',
        ]);

        $film = Film::factory()->create([
            'title' => 'Матрикс',
            'description' => null,
            'category_id' => $category->id,
            'year_id' => $year->id,
        ]);

        $film->genres()->attach($genre);

        $text = $film->fresh()->seoDescription();

        $this->assertStringContainsString(
            'Матрикс',
            $text
        );

        $this->assertStringContainsString(
            'Фільми',
            $text
        );

        $this->assertStringContainsString(
            '2025',
            $text
        );

        $this->assertStringContainsString(
            'Бойовик',
            $text
        );
    }


    public function test_builds_description_without_missing_parts(): void
    {
        $film = Film::factory()->create([
            'title' => 'Матрикс',
            'description' => null,
        ]);

        $text = $film->seoDescription();

        $this->assertStringContainsString(
            'Матрикс',
            $text
        );

        $this->assertStringContainsString(
            'Дивіться онлайн',
            $text
        );
    }


    public function test_generated_description_is_not_longer_than_163_symbols(): void
    {
        $category = Category::factory()->create([
            'title' => str_repeat('Категорія ', 10),
        ]);

        $year = Year::factory()->create([
            'title' => 2025,
        ]);

        $film = Film::factory()->create([
            'title' => str_repeat('Матрикс ', 20),
            'description' => null,
            'category_id' => $category->id,
            'year_id' => $year->id,
        ]);

        $result = $film->seoDescription();

        $this->assertLessThanOrEqual(
            163,
            mb_strlen($result)
        );
    }

}
