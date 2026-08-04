<?php
namespace Tests\Unit\Requests\Comment;

use App\Http\Requests\Comment\CreateRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CreateRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function validate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $request = new CreateRequest();

        return Validator::make($data, $request->rules());
    }


    // ===== Гість =====
    public function test_guest_must_provide_subject(): void
    {
        $validator = $this->validate([
            'body' => 'Досить довгий текст коментаря',
            'film_id' => 1,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('subject', $validator->errors()->toArray());
    }


    public function test_guest_subject_shorter_than_six_characters_fails(): void
    {
        $validator = $this->validate([
            'subject' => 'Ab',
            'body' => 'Досить довгий текст коментаря',
            'film_id' => 1,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('subject', $validator->errors()->toArray());
    }


    public function test_guest_with_valid_subject_passes(): void
    {
        $validator = $this->validate([
            'subject' => 'Гість Іван',
            'body' => 'Досить довгий текст коментаря',
            'film_id' => 1,
        ]);

        $this->assertFalse($validator->fails());
    }


    // ===== Автентифікований користувач =====

    public function test_authenticated_user_subject_is_optional(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $validator = $this->validate([
            'body' => 'Досить довгий текст коментаря',
            'film_id' => 1,
        ]);

        $this->assertFalse($validator->fails());
    }


    public function test_authenticated_user_can_still_provide_subject(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $validator = $this->validate([
            'subject' => 'Псевдонім',
            'body' => 'Досить довгий текст коментаря',
            'film_id' => 1,
        ]);

        $this->assertFalse($validator->fails());
    }


    // ===== Спільні правила (body, film_id) =====

    public function test_body_is_required(): void
    {
        $validator = $this->validate([
            'subject' => 'Гість Іван',
            'film_id' => 1,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('body', $validator->errors()->toArray());
    }


    public function test_body_shorter_than_ten_characters_fails(): void
    {
        $validator = $this->validate([
            'subject' => 'Гість Іван',
            'body' => 'Коротко',
            'film_id' => 1,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('body', $validator->errors()->toArray());
    }


    public function test_film_id_is_required(): void
    {
        $validator = $this->validate([
            'subject' => 'Гість Іван',
            'body' => 'Досить довгий текст коментаря',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('film_id', $validator->errors()->toArray());
    }

}
