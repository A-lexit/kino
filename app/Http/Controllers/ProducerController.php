<?php
namespace App\Http\Controllers;

use App\Models\Producer;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class ProducerController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $producers = Producer::paginate(20);

        return view('tags.index', [
            'items' => $producers,
            'labelField' => 'name',
            'showRoute' => 'producers.show',
            'seoTitle' => 'Продюсери',
            'seoDescription' => 'Список усіх продюсерів на сайті.',
            'pageTitle' => 'ТОП-Продюсери',
            'description' => 'Оберіть продюсера, щоб переглянути всі фільми та серіали за його участю.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $producer = Producer::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($producer->films(), $request);

        return view('tags.show', [
            'seoTitle' => 'Фільми за участю ' . $producer->name,
            'seoDescription' => 'Фільми за участю продюсера «' . $producer->name . '».',
            'pageTitle' => 'Продюсер - ' . $producer->name,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Продюсери', 'url' => route('producers.index')],
                ['title' => $producer->name, 'url' => null],
            ],
        ], $data);
    }

}
