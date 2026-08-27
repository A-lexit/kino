<?php
namespace App\Http\Controllers;

use App\Models\Country;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $countries = Country::paginate(20);

        return view('tags.index', [
            'items' => $countries,
            'labelField' => 'title',
            'showRoute' => 'countries.show',
            'seoTitle' => 'Країни',
            'seoDescription' => 'Список усіх країн, представлених на сайті.',
            'pageTitle' => 'Країни',
            'description' => 'Оберіть країну, щоб переглянути всі фільми та серіали, пов’язані з нею.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $country = Country::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($country->films(), $request);

        return view('tags.show', [
            'country' => $country,
            'seoTitle' => 'Країна - ' . $country->title,
            'seoDescription' => 'Фільми виробництва «' . $country->title . '».',
            'pageTitle' => 'Країна - ' . $country->title,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Країни', 'url' => route('countries.index')],
                ['title' => $country->title, 'url' => null],
            ],
        ], $data);
    }

}
