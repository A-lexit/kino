<?php
namespace App\Http\Controllers;

use App\Models\Actor;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class ActorController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $actors = Actor::paginate(20);

        return view('tags.index', [
            'items' => $actors,
            'labelField' => 'name',
            'showRoute' => 'actors.show',
            'seoTitle' => 'Актори',
            'seoDescription' => 'Список усіх акторів на сайті.',
            'pageTitle' => 'ТОП-Актори',
            'description' => 'Оберіть актора, щоб переглянути всі фільми та серіали за його участю.',
        ]);
    }


    public function show(Request $request, $slug)
    {
        $actor = Actor::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($actor->films(), $request);

        return view('tags.show', [
            'seoTitle' => 'Фільми за участю ' . $actor->name,
            'seoDescription' => 'Фільми за участю актора «' . $actor->name . '».',
            'pageTitle' => 'Актор - ' . $actor->name,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Актори', 'url' => route('actors.index')],
                ['title' => $actor->name, 'url' => null],
            ],
        ], $data);
    }
}
