<?php
namespace Tests\Unit\Requests;

use App\Http\Requests\NameRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class NameRequestTest extends TestCase
{
    protected function validate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $request = new NameRequest();

        return Validator::make($data, $request->rules());
    }


    public function test_valid_name_passes(): void
    {
        $validator = $this->validate(['name' => 'Steven Spielberg']);

        $this->assertFalse($validator->fails());
    }


    public function test_lowercase_prefixed_surname_passes(): void
    {
        // van Damme, von Trier тощо — легітимні реальні прізвища
        $validator = $this->validate(['name' => 'Jean-Claude van Damme']);

        $this->assertFalse($validator->fails());
    }


    public function test_name_is_required(): void
    {
        $validator = $this->validate([]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }


    public function test_name_shorter_than_three_characters_fails(): void
    {
        $validator = $this->validate(['name' => 'Al']);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }


    public function test_name_longer_than_sixty_characters_fails(): void
    {
        $validator = $this->validate(['name' => str_repeat('A', 61)]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

}
