<?php

namespace App\Services;

use App\Models\Film;

class FeatureFlagService
{
    public function setFeatured(Film $film): void
    {
        $film->is_featured = 1;
        $film->save();
    }

    public function setStandart(Film $film): void
    {
        $film->is_featured = 0;
        $film->save();
    }

    public function toggleFeatured(Film $film, ?string $value): void
    {
        if ($value === null) {
            $this->setStandart($film);
            return;
        }

        $this->setFeatured($film);
    }

}
