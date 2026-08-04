<?php
namespace Tests\Unit\Casts;

use App\Casts\DatePickerCast;
use App\DateTimeObjects\DatePickerValue;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class DatePickerCastTest extends TestCase
{
    private DatePickerCast $cast;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new DatePickerCast();
    }


    private function model(): Model
    {
        return new class extends Model {
        };
    }


    public function test_get_returns_date_picker_value_object(): void
    {
        $result = $this->cast->get(
            $this->model(),
            'datepicker',
            '2025-07-20',
            []
        );

        $this->assertInstanceOf(
            DatePickerValue::class,
            $result
        );
    }


    public function test_set_converts_date_picker_value_to_database_format(): void
    {
        $value = new DatePickerValue('20.07.2025');

        $result = $this->cast->set(
            $this->model(),
            'datepicker',
            $value,
            []
        );

        $this->assertSame(
            '2025-07-20',
            $result
        );
    }


    public function test_set_converts_string_to_database_format(): void
    {
        $result = $this->cast->set(
            $this->model(),
            'datepicker',
            '20.07.2025',
            []
        );

        $this->assertSame(
            '2025-07-20',
            $result
        );
    }

}
