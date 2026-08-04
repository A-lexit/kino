<?php
namespace Tests\Unit\Casts;

use App\Casts\DurationCast;
use App\DateTimeObjects\DurationValue;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class DurationCastTest extends TestCase
{
    private DurationCast $cast;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new DurationCast();
    }


    private function model(): Model
    {
        return new class extends Model {
        };
    }


    public function test_get_returns_duration_value_object(): void
    {
        $result = $this->cast->get(
            $this->model(),
            'duration',
            125,
            []
        );

        $this->assertInstanceOf(
            DurationValue::class,
            $result
        );
    }


    public function test_get_converts_value_to_integer(): void
    {
        $result = $this->cast->get(
            $this->model(),
            'duration',
            '90',
            []
        );

        $this->assertInstanceOf(
            DurationValue::class,
            $result
        );

        $this->assertSame(
            90,
            $result->totalMinutes
        );
    }


    public function test_set_returns_total_minutes_from_duration_value(): void
    {
        $value = new DurationValue(150);

        $result = $this->cast->set(
            $this->model(),
            'duration',
            $value,
            []
        );

        $this->assertSame(
            150,
            $result
        );
    }


    public function test_set_converts_numeric_string_to_integer(): void
    {
        $result = $this->cast->set(
            $this->model(),
            'duration',
            '200',
            []
        );

        $this->assertSame(
            200,
            $result
        );
    }


    public function test_set_returns_null_for_invalid_value(): void
    {
        $result = $this->cast->set(
            $this->model(),
            'duration',
            'abc',
            []
        );

        $this->assertNull($result);
    }

}
