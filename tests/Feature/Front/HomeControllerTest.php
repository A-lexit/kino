<?php
namespace Tests\Feature\Front;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_displayed(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        $response->assertViewIs('home');

        $response->assertViewHas([
            'films',
            'serials',
            'mults',
            'multserials',
            'title',
            'description',
        ]);
    }

    /**
     * Перевірка: неавторизований (гість) користувач перенаправляється на сторінку входу
     */

    /**
     * Перевірка: авторизований користувач має доступ до сторінки dashboard
     */

}
