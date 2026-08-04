<?php
namespace Tests\Unit\Requests;

use App\Http\Requests\TitleRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TitleRequestTest extends TestCase
{
    protected function validate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $request = new TitleRequest();

        return Validator::make($data, $request->rules());
    }


    public function test_valid_title_passes(): void
    {
        $validator = $this->validate(['title' => 'Комедія']);

        $this->assertFalse($validator->fails());
    }


    public function test_title_is_required(): void
    {
        $validator = $this->validate([]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
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

}
