<?php
namespace App\Services;

use App\Constants\FilmStatus;
use App\Models\Film;

class DraftFlagService
{
    public function setDraft(Film $film): void
    {
        $film->publish_status = FilmStatus::DRAFT;
        $film->save();
    }

    public function setPublic(Film $film): void
    {
        $film->publish_status = FilmStatus::PUBLISHED;
        $film->save();
    }

    public function togglePublishStatus(Film $film, ?string $value): void
    {
        if ($value === null) {
            $this->setDraft($film);
            return;
        }
        $this->setPublic($film);
    }

}

