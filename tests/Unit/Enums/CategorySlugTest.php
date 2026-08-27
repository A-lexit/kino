<?php

namespace Tests\Unit\Enums;

use App\Enums\CategorySlug;
use Tests\TestCase;

class CategorySlugTest extends TestCase
{
    public function test_serial_types_are_detected_correctly(): void
    {
        $this->assertTrue(
            CategorySlug::SERIALS->isSerialType()
        );

        $this->assertTrue(
            CategorySlug::MULTSERIALS->isSerialType()
        );

        $this->assertFalse(
            CategorySlug::FILMS->isSerialType()
        );

        $this->assertFalse(
            CategorySlug::MULTS->isSerialType()
        );
    }
}
