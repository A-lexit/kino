<?php

namespace App\Traits;

trait SortFilms
{
    public function scopeApplySorting($query, $sort)
    {
        return match ($sort) {
            'oldest'    => $query->oldest('films.id'),
            'popular'   => $query->join('states', 'films.id', '=', 'states.film_id')
                ->orderBy('states.likes', 'desc')
                ->orderBy('states.views', 'desc')
                ->select('films.*'),
            'title'     => $query->orderBy('films.title', 'asc'),
            'year_desc' => $query->join('years', 'films.year_id', '=', 'years.id')->orderBy('years.title', 'desc')->select('films.*'),
            'year_asc'  => $query->join('years', 'films.year_id', '=', 'years.id')->orderBy('years.title', 'asc')->select('films.*'),
            default     => $query->latest('films.id'),
        };
    }

}
