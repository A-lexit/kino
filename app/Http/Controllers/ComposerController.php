<?php
namespace App\Http\Controllers;

use App\Models\Composer;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class ComposerController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $composers = Composer::paginate(20);

        return view('tags.index', [
            'items' => $composers,
            'labelField' => 'name',
            'showRoute' => 'composers.show',
            'seoTitle' => 'Композитори',
            'seoDescription' => 'Список усіх композиторів на сайті.',
            'pageTitle' => 'ТОП-Композитори',
            'description' => 'Оберіть композитора, щоб переглянути всі фільми та серіали за його участю.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $composer = Composer::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($composer->films(), $request);

        return view('tags.show', [
            'seoTitle' => 'Фільми за участю ' . $composer->name,
            'seoDescription' => 'Фільми за участю композитора «' . $composer->name . '».',
            'pageTitle' => 'Композитор - ' . $composer->name,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Композитори', 'url' => route('composers.index')],
                ['title' => $composer->name, 'url' => null],
            ],
        ], $data);
    }

}
