<?php
namespace Tests\Unit\Enums;

use App\Enums\FilmStatus;
use PHPUnit\Framework\TestCase;

class FilmStatusTest extends TestCase
{
    public function test_enum_contains_expected_values(): void
    {
        $this->assertSame('draft', FilmStatus::Draft->value);
        $this->assertSame('published', FilmStatus::Published->value);
    }


    public function test_try_from_returns_correct_enum(): void
    {
        $this->assertSame(
            FilmStatus::Draft,
            FilmStatus::tryFrom('draft')
        );

        $this->assertSame(
            FilmStatus::Published,
            FilmStatus::tryFrom('published')
        );
    }


    public function test_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(
            FilmStatus::tryFrom('invalid')
        );
    }

}
