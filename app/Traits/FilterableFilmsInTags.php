<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait FilterableFilmsInTags
{
    protected function getFilteredFilmsAndCategoriesForTags($relation, Request $request)
    {
        $sort = $request->query('sort', 'latest');
        $categorySlug = $request->query('category');

        $filmsQuery = $relation->published()
            ->with('category', 'year');

        if ($categorySlug) {
            $filmsQuery->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $films = $filmsQuery
            ->applySorting($sort)
            ->paginate(20)
            ->appends($request->query());

        $categories = $relation->published()
            ->with('category')
            ->get()
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->sortBy('title')
            ->values();

        return compact('films', 'categories');
    }

}
