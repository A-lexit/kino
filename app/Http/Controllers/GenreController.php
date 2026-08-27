<?php
namespace App\Http\Controllers;

use App\Models\Genre;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $genres = Genre::paginate(20);

        return view('tags.index', [
            'items' => $genres,
            'labelField' => 'title',
            'showRoute' => 'genres.show',
            'seoTitle' => 'Жанри',
            'seoDescription' => 'Список усіх жанрів фільмів та серіалів на сайті.',
            'pageTitle' => 'Жанри',
            'description' => 'Оберіть жанр, щоб переглянути всі фільми та серіали цього жанру.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $genre = Genre::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($genre->films(), $request);

        return view('tags.show', [
            'genre' => $genre,
            'seoTitle' => 'Жанр - ' . $genre->title,
            'seoDescription' => 'Фільми жанру «' . $genre->title . '».',
            'pageTitle' => 'Жанр - ' . $genre->title,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Жанри', 'url' => route('genres.index')],
                ['title' => $genre->title, 'url' => null],
            ],
        ], $data);
    }

}
