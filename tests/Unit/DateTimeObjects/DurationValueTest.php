<?php
namespace Tests\Unit\DateTimeObjects;

use App\DateTimeObjects\DurationValue;
use Tests\TestCase;

class DurationValueTest extends TestCase
{
    public function test_get_hours_returns_correct_hours(): void
    {
        $duration = new DurationValue(150);

        $this->assertSame(
            2,
            $duration->getHours()
        );
    }


    public function test_get_minutes_returns_remaining_minutes(): void
    {
        $duration = new DurationValue(150);

        $this->assertSame(
            30,
            $duration->getMinutes()
        );
    }


    public function test_duration_can_be_created_with_total_minutes(): void
    {
        $duration = new DurationValue(90);

        $this->assertSame(
            90,
            $duration->totalMinutes
        );
    }


    public function test_string_conversion_returns_readable_duration(): void
    {
        $duration = new DurationValue(125);

        $this->assertSame(
            '2 год 5 хв',
            (string) $duration
        );
    }


    public function test_zero_duration_returns_zero_hours_and_minutes(): void
    {
        $duration = new DurationValue(0);

        $this->assertSame(
            0,
            $duration->getHours()
        );

        $this->assertSame(
            0,
            $duration->getMinutes()
        );

        $this->assertSame(
            '0 год 0 хв',
            (string) $duration
        );
    }


    public function test_duration_with_only_minutes_returns_zero_hours(): void
    {
        $duration = new DurationValue(45);

        $this->assertSame(
            0,
            $duration->getHours()
        );

        $this->assertSame(
            45,
            $duration->getMinutes()
        );
    }

}
