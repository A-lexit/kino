<?php
namespace Tests\Unit\DateTimeObjects;

use App\DateTimeObjects\DatePickerValue;
use Tests\TestCase;

class DatePickerValueTest extends TestCase
{
    public function test_to_database_returns_null_for_empty_date(): void
    {
        $value = new DatePickerValue(null);

        $this->assertNull(
            $value->toDatabase()
        );
    }


    public function test_to_database_keeps_sql_date_format(): void
    {
        $value = new DatePickerValue('2025-07-20');

        $this->assertSame(
            '2025-07-20',
            $value->toDatabase()
        );
    }


    public function test_to_database_converts_form_date_format(): void
    {
        $value = new DatePickerValue('20.07.2025');

        $this->assertSame(
            '2025-07-20',
            $value->toDatabase()
        );
    }


    public function test_to_database_parses_other_supported_date_format(): void
    {
        $value = new DatePickerValue('2025/07/20');

        $this->assertSame(
            '2025-07-20',
            $value->toDatabase()
        );
    }


    public function test_to_database_returns_null_for_invalid_date(): void
    {
        $value = new DatePickerValue('not-a-date');

        $this->assertNull(
            $value->toDatabase()
        );
    }


    public function test_to_form_formats_date_for_user(): void
    {
        $value = new DatePickerValue('2025-07-20');

        $this->assertSame(
            '20.07.2025',
            $value->toForm()
        );
    }


    public function test_to_form_returns_null_for_empty_date(): void
    {
        $value = new DatePickerValue(null);

        $this->assertNull(
            $value->toForm()
        );
    }


    public function test_string_conversion_returns_formatted_date(): void
    {
        $value = new DatePickerValue('2025-07-20');

        $this->assertSame(
            '20.07.2025',
            (string) $value
        );
    }

}
