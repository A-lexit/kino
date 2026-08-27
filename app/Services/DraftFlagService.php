<?php
namespace App\Services;

use App\Enums\FilmStatus;
use App\Models\Film;

class DraftFlagService
{
    public function setDraft(Film $film): void
    {
        $film->togglePublishStatus(FilmStatus::Draft->value);
    }

    public function setPublic(Film $film): void
    {
        $film->togglePublishStatus(FilmStatus::Published->value);
    }

    public function togglePublishStatus(
        Film $film,
        ?string $value
    ): void {
        if ($value === null) {
            $this->setDraft($film);

            return;
        }

        $this->setPublic($film);
    }

}
