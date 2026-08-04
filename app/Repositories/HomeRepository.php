<?php
namespace App\Repositories;

use App\Enums\CategorySlug;
use App\Models\Film;
use Illuminate\Support\Facades\Cache;

class HomeRepository
{
    public function homeFilmsByEnum(CategorySlug $category, int $limit)
    {
        return Cache::tags(['home_films'])->remember(
            "home_films_{$category->value}_{$limit}",
            now()->addHours(6),
            function () use ($category, $limit) {
                return Film::whereHas('category', function ($query) use ($category) {
                    $query->where('slug', $category->value);
                })
                    ->published()
                    ->latest()
                    ->take($limit)
                    ->get();
            }
        );
    }

}
