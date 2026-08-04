<?php
namespace Tests\Feature\Front;

use App\Models\Caption;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaptionControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Перевірка відображення списку субтитрів з пагінацією
     */
    public function test_index_displays_paginated_captions_list(): void
    {
        // Створюємо 25 записів для перевірки пагінації (ліміт 20)
        Caption::factory()->count(25)->create();

        $response = $this->get(route('captions.index'));

        $response->assertOk();
        $response->assertViewIs('captions.index');
        $response->assertViewHas('captions');

        // Перевіряємо, що на першій сторінці рівно 20 записів
        $this->assertCount(20, $response->viewData('captions'));
    }

    /**
     * Перевірка відображення конкретної сторінки та пов'язаних фільмів
     */
    public function test_show_displays_caption_and_associated_films(): void
    {
        // Створюємо субтитри з конкретним слагом
        $caption = Caption::factory()->create([
            'slug' => 'ukrainian-subs'
        ]);

        // Створюємо фільм та прив'язуємо його до субтитрів
        // (використовуємо attach, припускаючи зв'язок Many-to-Many)
        $film = Film::factory()->create();
        $caption->films()->attach($film);

        $response = $this->get(route('captions.show', $caption->slug));

        $response->assertOk();
        $response->assertViewIs('captions.show');
        $response->assertViewHasAll(['caption', 'films']);

        // Перевіряємо, що передано правильний запис субтитрів та фільми підтягнулися
        $this->assertEquals($caption->id, $response->viewData('caption')->id);
        $this->assertCount(1, $response->viewData('films'));
    }

    /**
     * Перевірка повернення 404 помилки, якщо слаг не знайдено
     */
    public function test_show_returns_404_if_caption_not_found(): void
    {
        $response = $this->get(route('captions.show', 'non-existent-slug'));

        $response->assertNotFound();
    }

}
