<?php
namespace App\Traits;

use App\Models\Genre;
use App\Models\Selection;
use Illuminate\Http\Request;

trait FilterableFilmsForCategory
{
    protected function getFilteredFilmsAndFiltersForCategory($category, Request $request)
    {
        $sort = $request->query('sort', 'latest');
        $genreSlug = $request->query('genre');
        $selectionSlug = $request->query('selection');
        $period = $request->query('period');

        $allowedCategories = ['filmi', 'seriali'];

        $availableYears = $category->films()
            ->published()
            ->join('years', 'films.year_id', '=', 'years.id')
            ->select('years.title')
            ->distinct()
            ->pluck('title')
            ->filter(fn($y) => is_numeric($y))
            ->map(fn($y) => (int) $y)
            ->toArray();

        $periods = [];
        foreach ($availableYears as $year) {
            if ($year >= 2020) {
                $periods[$year] = $year . ' рік';
            } else {
                $decade = floor($year / 10) * 10;
                $periods[$decade] = $decade . '-ті';
            }
        }
        krsort($periods);

        $films = $category->films()
            ->published()
            ->with('category', 'year')
            ->when($genreSlug && in_array($category->slug, $allowedCategories), function ($query) use ($genreSlug) {
                $query->whereHas('genres', function ($q) use ($genreSlug) {
                    $q->where('slug', $genreSlug);
                });
            })
            ->when($selectionSlug, function ($query) use ($selectionSlug) {
                $query->whereHas('selections', function ($q) use ($selectionSlug) {
                    $q->where('slug', $selectionSlug);
                });
            })
            ->when($period, function ($query) use ($period) {
                $query->whereHas('year', function ($q) use ($period) {
                    $period = (int) $period;
                    if ($period >= 2020) {
                        $q->where('title', $period);
                    } else {
                        $q->whereBetween('title', [$period, $period + 9]);
                    }
                });
            })
            ->applySorting($sort)
            ->paginate(40)
            ->appends($request->query());

        $genres = collect();
        if (in_array($category->slug, $allowedCategories)) {
            $genres = Genre::whereHas('films', function ($q) use ($category) {
                $q->where('category_id', $category->id)->published();
            })->orderBy('title')->get();
        }

        $selections = Selection::whereHas('films', function ($q) use ($category) {
            $q->where('category_id', $category->id)->published();
        })->orderBy('title')->get();

        return compact('films', 'genres', 'selections', 'periods');
    }

}
