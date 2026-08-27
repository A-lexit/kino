<?php
namespace App\Http\Controllers;

use App\Models\Selection;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class SelectionController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $selections = Selection::paginate(20);

        return view('tags.index', [
            'items' => $selections,
            'labelField' => 'title',
            'showRoute' => 'selections.show',
            'seoTitle' => 'Добірки',
            'seoDescription' => 'Список усіх добірок фільмів та серіалів на сайті.',
            'pageTitle' => 'Добірки',
            'description' => 'Оберіть добірку, щоб переглянути всі фільми та серіали, що до неї входять.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $selection = Selection::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags(
            $selection->films(),
            $request
        );

        return view('tags.show', [
            'selection' => $selection,
            'seoTitle' => 'Добірка - ' . $selection->title,
            'seoDescription' => 'Фільми з добірки «' . $selection->title . '».',
            'pageTitle' => 'Добірка - ' . $selection->title,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Добірки', 'url' => route('selections.index')],
                ['title' => $selection->title, 'url' => null],
            ],
        ], $data);
    }

}
