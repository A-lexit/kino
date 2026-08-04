<?php

namespace App\DateTimeObjects;

class DurationValue
{
    public function __construct(public readonly int $totalMinutes) {}

    public function getHours(): int
    {
        return intval($this->totalMinutes / 60);
    }

    public function getMinutes(): int
    {
        return $this->totalMinutes % 60;
    }

    public function __toString(): string
    {
        return "{$this->getHours()} год {$this->getMinutes()} хв";
    }

}
