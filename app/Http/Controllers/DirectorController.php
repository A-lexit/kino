<?php
namespace App\Http\Controllers;

use App\Models\Director;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $directors = Director::paginate(20);

        return view('tags.index', [
            'items' => $directors,
            'labelField' => 'name',
            'showRoute' => 'directors.show',
            'seoTitle' => 'Режисери',
            'seoDescription' => 'Список усіх режисерів на сайті.',
            'pageTitle' => 'ТОП-Режисери',
            'description' => 'Оберіть режисера, щоб переглянути всі фільми та серіали за його участю.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $director = Director::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($director->films(), $request);

        return view('tags.show', [
            'seoTitle' => 'Фільми за участю ' . $director->name,
            'seoDescription' => 'Фільми за участю режисера «' . $director->name . '».',
            'pageTitle' => 'Режисер - ' . $director->name,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Режисери', 'url' => route('directors.index')],
                ['title' => $director->name, 'url' => null],
            ],
        ], $data);
    }

}
