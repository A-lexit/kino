<?php
namespace App\Http\Controllers;

use App\Models\Year;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class YearController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $years = Year::paginate(20);

        return view('tags.index', [
            'items' => $years,
            'labelField' => 'title',
            'showRoute' => 'years.show',
            'seoTitle' => 'Роки випуску',
            'seoDescription' => 'Список усіх років випуску фільмів та серіалів на сайті.',
            'pageTitle' => 'Роки випуску',
            'description' => 'Оберіть рік випуску, щоб переглянути всі фільми та серіали цього року.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $year = Year::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($year->films(), $request);

        return view('tags.show', [
            'year' => $year,
            'seoTitle' => 'Рік випуску - ' . $year->title,
            'seoDescription' => 'Фільми за ' . $year->title . ' рік.',
            'pageTitle' => 'Фільми за ' . $year->title . ' рік',
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Роки випуску', 'url' => route('years.index')],
                ['title' => $year->title, 'url' => null],
            ],
        ], $data);
    }

}
