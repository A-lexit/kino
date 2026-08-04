<?php

namespace App\Services;

use App\Models\Film;

class FilmRelationService
{
    public function sync(Film $film, array $data): void
    {
        $relations = [
            'companies',
            'composers',
            'directors',
            'actors',
            'producers',
            'genres',
            'languages',
            'countries',
            'captions',
            'selections'
        ];

        foreach ($relations as $relation) {
            $film->{$relation}()->sync($data[$relation] ?? []);
        }
    }

}
