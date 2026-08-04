<?php

namespace App\Casts;

use App\DateTimeObjects\DatePickerValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class DatePickerCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): DatePickerValue
    {
        return new DatePickerValue($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value instanceof DatePickerValue) {
            return $value->toDatabase();
        }

        return (new DatePickerValue($value))->toDatabase();
    }
}
