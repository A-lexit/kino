<?php
namespace Tests\Unit\Requests;

use App\Http\Requests\FilmRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FilmRequestTest extends TestCase
{
    protected function makeRequest(array $data): FilmRequest
    {
        $request = new FilmRequest();
        $request->merge($data);

        return $request;
    }


    protected function callPrepareForValidation(FilmRequest $request): void
    {
        $method = new \ReflectionMethod($request, 'prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);
    }


    protected function validate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $request = $this->makeRequest($data);
        $this->callPrepareForValidation($request);

        return Validator::make($request->all(), $request->rules());
    }


    public function test_valid_data_passes_validation(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'category_id' => 1,
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


    public function test_empty_title_is_auto_filled_with_placeholder(): void
    {
        $request = $this->makeRequest(['title' => '']);
        $this->callPrepareForValidation($request);

        $this->assertStringStartsWith('Невідомий фільм ', $request->title);
    }


    public function test_whitespace_only_title_is_auto_filled_with_placeholder(): void
    {
        $request = $this->makeRequest(['title' => '   ']);
        $this->callPrepareForValidation($request);

        $this->assertStringStartsWith('Невідомий фільм ', $request->title);
    }


    public function test_missing_title_is_auto_filled_with_placeholder(): void
    {
        $request = $this->makeRequest([]);
        $this->callPrepareForValidation($request);

        $this->assertStringStartsWith('Невідомий фільм ', $request->title);
    }


    public function test_provided_title_is_not_overwritten(): void
    {
        $request = $this->makeRequest(['title' => 'Мій фільм']);
        $this->callPrepareForValidation($request);

        $this->assertSame('Мій фільм', $request->title);
    }


    public function test_title_shorter_than_two_characters_fails(): void
    {
        $validator = $this->validate(['title' => 'A']);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }


    public function test_title_longer_than_120_characters_fails(): void
    {
        $validator = $this->validate(['title' => str_repeat('A', 121)]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }


    public function test_non_integer_category_id_fails(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'category_id' => 'not-a-number',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
    }


    public function test_trailer_youtube_url_accepts_null(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'trailer_youtube_url' => null,
        ]);

        $this->assertFalse($validator->fails());
    }


    public function test_trailer_youtube_url_too_long_fails(): void
    {
        $validator = $this->validate([
            'title' => 'Тестовий фільм',
            'trailer_youtube_url' => str_repeat('a', 256),
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('trailer_youtube_url', $validator->errors()->toArray());
    }

}
