<?php
namespace Tests\Unit\Rules;

use App\Rules\Uppercase;
use Tests\TestCase;

class UppercaseTest extends TestCase
{
    private Uppercase $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new Uppercase();
    }


    public function test_passes_for_uppercase_string(): void
    {
        $this->assertTrue(
            $this->rule->passes('name', 'HELLO')
        );
    }


    public function test_fails_for_lowercase_string(): void
    {
        $this->assertFalse(
            $this->rule->passes('name', 'hello')
        );
    }


    public function test_fails_for_mixed_case_string(): void
    {
        $this->assertFalse(
            $this->rule->passes('name', 'Hello')
        );
    }


    public function test_returns_validation_message(): void
    {
        $this->assertSame(
            'The :attribute must be uppercase.',
            $this->rule->message()
        );
    }

}
