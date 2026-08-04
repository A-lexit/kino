<?php
namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Перевірка відображення списку жанрів
     */
    public function test_index_displays_paginated_genres_list(): void
    {
        Genre::factory()->count(25)->create();

        $response = $this->get(route('genres.index'));

        $response->assertOk();
        $response->assertViewIs('genres.index');
        $response->assertViewHas('genres');
        $this->assertCount(20, $response->viewData('genres'));
    }

    /**
     * Перевірка відображення сторінки жанру та пов'язаних фільмів
     */
    public function test_show_displays_genre_and_associated_films(): void
    {
        $genre = Genre::factory()->create(['slug' => 'action']);
        $category = Category::factory()->create();

        Film::factory()->count(5)->create([
            'category_id' => $category->id
        ])->each(function ($film) use ($genre) {
            $genre->films()->attach($film);
        });

        $response = $this->get(route('genres.show', $genre->slug));

        $response->assertOk();
        $response->assertViewIs('genres.show');
        $response->assertViewHasAll(['genre', 'films']);
        $this->assertEquals($genre->id, $response->viewData('genre')->id);
        $this->assertCount(5, $response->viewData('films'));
    }

    /**
     * Перевірка 404 помилки
     */
    public function test_show_returns_404_if_genre_not_found(): void
    {
        $response = $this->get(route('genres.show', 'non-existent-genre'));

        $response->assertNotFound();
    }

}
