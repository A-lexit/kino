<?php
namespace App\Http\Controllers;

use App\Models\Rating;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $ratings = Rating::paginate(20);

        return view('tags.index', [
            'items' => $ratings,
            'labelField' => 'title',
            'showRoute' => 'ratings.show',
            'seoTitle' => 'Рейтинги',
            'seoDescription' => 'Список усіх рейтингів фільмів та серіалів на сайті.',
            'pageTitle' => 'Рейтинги',
            'description' => 'Оберіть рейтинг, щоб переглянути всі фільми та серіали з ним.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $rating = Rating::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($rating->films(), $request);

        return view('tags.show', [
            'rating' => $rating,
            'seoTitle' => 'Рейтинг - ' . $rating->title,
            'seoDescription' => 'Фільми з рейтингом «' . $rating->title . '».',
            'pageTitle' => 'Рейтинг - ' . $rating->title,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Рейтинги', 'url' => route('ratings.index')],
                ['title' => $rating->title, 'url' => null],
            ],
        ], $data);
    }

}
