<?php

namespace Excel\Exports;

use App\Enums\FilmStatus;
use App\Excel\Exports\FilmsExport;
use App\Models\Actor;
use App\Models\Category;
use App\Models\Country;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmsExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_headings_returns_expected_columns(): void
    {
        $headings = (new FilmsExport)->headings();

        $this->assertSame([
            'id',
            'title',
            'slug',
            'origin_title',
            'other_actor',
            'note',
            'description',
            'category',
            'year',
            'duration',
            'quality',
            'season',
            'rating',
            'status',
            'age',
            'author',
            'publish_status',
            'is_featured',
            'tmdb_id',
            'imdb_id',
            'imdb_rating',
            'datepicker',
            'thumbnail',
            'tmdb_poster',
            'trailer_youtube_id',
            'trailer_file',
            'gal_image1',
            'gal_image2',
            'gal_image3',
            'gal_image4',
            'gal_image5',
            'likes',
            'views',
            'sort_order',
            'actors',
            'genres',
            'countries',
        ], $headings);
    }

    public function test_map_exports_film_data_and_relationships(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin',
        ]);

        $category = Category::factory()->create([
            'title' => 'Фільми',
        ]);

        $actor1 = Actor::factory()->create([
            'name' => 'Актор 1',
        ]);

        $actor2 = Actor::factory()->create([
            'name' => 'Актор 2',
        ]);

        $genre = Genre::factory()->create([
            'title' => 'Драма',
        ]);

        $country = Country::factory()->create([
            'title' => 'США',
        ]);

        $film = Film::factory()->create([
            'title' => 'Тестовий фільм',
            'slug' => 'testovyi-film',
            'origin_title' => 'Test Film',
            'category_id' => $category->id,
            'author_id' => $user->id,
            'publish_status' => FilmStatus::Published,
            'is_featured' => true,
            'tmdb_id' => 12345,
            'imdb_id' => 'tt1234567',
            'imdb_rating' => 8.7,
            'thumbnail' => 'films/test.webp',
            'tmdb_poster' => '/test.jpg',
            'trailer_youtube_id' => 'abc123',
            'sort_order' => 10,
        ]);

        $film->actors()->attach([
            $actor1->id,
            $actor2->id,
        ]);

        $film->genres()->attach($genre->id);
        $film->countries()->attach($country->id);

        // Створюємо State через relationship,
        // так само як це робить FilmService.
        $film->state()->updateOrCreate(
            ['film_id' => $film->id],
            [
                'likes' => 25,
                'views' => 1000,
            ]
        );

        $film->load([
            'category',
            'year',
            'duration',
            'quality',
            'season',
            'rating',
            'status',
            'age',
            'user',
            'state',
            'actors',
            'genres',
            'countries',
        ]);

        $this->assertNotNull($film->state);
        $this->assertSame(25, $film->state->likes);
        $this->assertSame(1000, $film->state->views);

        $row = (new FilmsExport)->map($film);

        $this->assertSame($film->id, $row[0]);
        $this->assertSame('Тестовий фільм', $row[1]);
        $this->assertSame('testovyi-film', $row[2]);
        $this->assertSame('Test Film', $row[3]);

        $this->assertSame('Фільми', $row[7]);
        $this->assertSame('Admin', $row[15]);

        $this->assertSame('published', $row[16]);
        $this->assertSame('1', $row[17]);

        $this->assertSame(12345, $row[18]);
        $this->assertSame('tt1234567', $row[19]);
        $this->assertSame(8.7, (float) $row[20]);

        $this->assertSame('films/test.webp', $row[22]);
        $this->assertSame('/test.jpg', $row[23]);
        $this->assertSame('abc123', $row[24]);

        $this->assertSame(25, $row[31]);
        $this->assertSame(1000, $row[32]);
        $this->assertSame(10, $row[33]);

        $this->assertSame('Актор 1, Актор 2', $row[34]);
        $this->assertSame('Драма', $row[35]);
        $this->assertSame('США', $row[36]);
    }

    public function test_map_handles_missing_optional_relationships(): void
    {
        $film = Film::factory()->create([
            'category_id' => null,
            'publish_status' => FilmStatus::Draft,
            'is_featured' => false,
        ]);

        $film->load([
            'category',
            'year',
            'duration',
            'quality',
            'season',
            'rating',
            'status',
            'age',
            'user',
            'state',
            'actors',
            'genres',
            'countries',
        ]);

        $row = (new FilmsExport)->map($film);

        $this->assertSame('Без категорії', $row[7]);
        $this->assertSame('draft', $row[16]);
        $this->assertSame('0', $row[17]);

        $this->assertSame(0, $row[31]);
        $this->assertSame(0, $row[32]);

        $this->assertSame('', $row[34]);
        $this->assertSame('', $row[35]);
        $this->assertSame('', $row[36]);
    }
}
