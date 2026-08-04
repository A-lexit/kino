<?php
namespace Tests\Feature\Front;

use App\Models\Age;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgeControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Перевірка відображення списку вікових обмежень з пагінацією
     */
    public function test_index_displays_paginated_ages_list(): void
    {
        // Створюємо 25 записів, щоб перевірити пагінацію (ліміт 20)
        Age::factory()->count(25)->create();

        $response = $this->get(route('ages.index'));

        $response->assertOk(); // Аналог assertStatus(200)
        $response->assertViewIs('ages.index');
        $response->assertViewHas('ages');

        // Перевіряємо, що на першій сторінці рівно 20 записів
        $this->assertCount(20, $response->viewData('ages'));
    }

    /**
     * Перевірка відображення конкретної сторінки та пов'язаних фільмів
     */
    public function test_show_displays_age_and_associated_films(): void
    {
        // Створюємо вікову категорію
        $age = Age::factory()->create([
            'slug' => '18-plus'
        ]);

        // Створюємо 2 фільми і прив'язуємо їх до цієї категорії
        // (виходячи з вашого сидера, age_id знаходиться у таблиці films)
        Film::factory()->count(2)->create([
            'age_id' => $age->id
        ]);

        $response = $this->get(route('ages.show', $age->slug));

        $response->assertOk();
        $response->assertViewIs('ages.show');
        $response->assertViewHasAll(['age', 'films']);

        // Перевіряємо, що передана правильна категорія та підтягнулися її фільми
        $this->assertEquals($age->id, $response->viewData('age')->id);
        $this->assertCount(2, $response->viewData('films'));
    }

    /**
     * Перевірка повернення 404 помилки, якщо слаг не знайдено
     */
    public function test_show_returns_404_if_age_not_found(): void
    {
        $response = $this->get(route('ages.show', 'non-existent-slug'));

        $response->assertNotFound(); // Аналог assertStatus(404)
    }

}
