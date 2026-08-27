<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\FilmRequest;
use App\Models\Category;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Tests\TestCase;

class FilmRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function makeRequest(array $data): FilmRequest
    {
        $request = new FilmRequest();

        $request->merge($data);

        return $request;
    }

    protected function validate(array $data): Validator
    {
        $request = $this->makeRequest($data);

        return ValidatorFacade::make(
            $request->all(),
            $request->rules()
        );
    }

    public function test_valid_data_passes_validation(): void
    {
        $category = Category::factory()->create();

        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'slug' => 'testovyi-film',
            'category_id' => $category->id,
            'year_id' => 1,
            'season_id' => 1,
            'rating_id' => 1,
            'status_id' => 1,
            'age_id' => 1,
            'quality_id' => 1,
            'duration_id' => 1,
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_title_is_required(): void
    {
        $validator = $this->validate([
            'category_id' => null,
        ]);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'title',
            $validator->errors()->toArray()
        );
    }

    public function test_title_shorter_than_two_characters_fails(): void
    {
        $validator = $this->validate([
            'title' => 'A',
        ]);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'title',
            $validator->errors()->toArray()
        );
    }

    public function test_title_longer_than_120_characters_fails(): void
    {
        $validator = $this->validate([
            'title' => str_repeat('A', 121),
        ]);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'title',
            $validator->errors()->toArray()
        );
    }

    public function test_category_id_can_be_null(): void
    {
        $validator = $this->validate([
            'title' => 'Фільм без категорії',
            'category_id' => null,
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_category_id_must_be_integer_when_provided(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'category_id' => 'not-a-number',
        ]);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'category_id',
            $validator->errors()->toArray()
        );
    }

    public function test_category_id_must_exist_when_provided(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'category_id' => 99999,
        ]);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'category_id',
            $validator->errors()->toArray()
        );
    }

    public function test_slug_can_be_null(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'slug' => null,
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_slug_longer_than_255_characters_fails(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'slug' => str_repeat('a', 256),
        ]);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'slug',
            $validator->errors()->toArray()
        );
    }

    public function test_trailer_youtube_url_accepts_null(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'trailer_youtube_url' => null,
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_trailer_youtube_url_longer_than_255_characters_fails(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'trailer_youtube_url' => str_repeat('a', 256),
        ]);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'trailer_youtube_url',
            $validator->errors()->toArray()
        );
    }

    public function test_sort_order_must_be_non_negative_integer(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'sort_order' => -1,
        ]);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'sort_order',
            $validator->errors()->toArray()
        );
    }

    public function test_likes_must_be_non_negative_integer(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'likes' => -1,
        ]);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'likes',
            $validator->errors()->toArray()
        );
    }

    public function test_views_must_be_non_negative_integer(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'views' => -1,
        ]);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'views',
            $validator->errors()->toArray()
        );
    }
}
