<?php
namespace App\Services;

use Illuminate\Support\Str;
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
            'selections',
            'related_films',
        ];

        foreach ($relations as $relation) {
            // Перетворюємо 'related_films' на 'relatedFilms'
            $methodName = Str::camel($relation);

            // Викликаємо camelCase метод і передаємо дані зі snake_case ключа
            $film->{$methodName}()->sync($data[$relation] ?? []);
        }
    }

}
