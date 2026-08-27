<?php
namespace App\Http\Controllers;

use App\Models\Age;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class AgeController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $ages = Age::paginate(20);

        return view('tags.index', [
            'items' => $ages,
            'labelField' => 'title',
            'showRoute' => 'ages.show',
            'seoTitle' => 'Мінімальний вік',
            'seoDescription' => 'Список усіх вікових обмежень на сайті.',
            'pageTitle' => 'Вікові обмеження',
            'description' => 'Оберіть вікове обмеження, щоб переглянути всі фільми та серіали цієї категорії.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $age = Age::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($age->films(), $request);

        return view('tags.show', [
            'age' => $age,
            'seoTitle' => 'Вікове обмеження - ' . $age->title,
            'seoDescription' => 'Фільми з віковим обмеженням «' . $age->title . '».',
            'pageTitle' => 'Вікове обмеження - ' . $age->title,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Вікові обмеження', 'url' => route('ages.index')],
                ['title' => $age->title, 'url' => null],
            ],
        ], $data);
    }

}
