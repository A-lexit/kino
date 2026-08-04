<?php

namespace App\Casts;

use App\DateTimeObjects\DurationValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class DurationCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): DurationValue
    {
        return new DurationValue((int)$value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?int
    {
        if ($value instanceof DurationValue) {
            return $value->totalMinutes;
        }

        return is_numeric($value) ? (int)$value : null;
    }
}
