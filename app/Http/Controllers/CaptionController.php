<?php
namespace App\Http\Controllers;

use App\Models\Caption;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class CaptionController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $captions = Caption::paginate(20);

        return view('tags.index', [
            'items' => $captions,
            'labelField' => 'title',
            'showRoute' => 'captions.show',
            'seoTitle' => 'Субтитри',
            'seoDescription' => 'Список усіх доступних субтитрів на сайті.',
            'pageTitle' => 'Субтитри',
            'description' => 'Оберіть субтитри, щоб переглянути всі фільми та серіали з ними.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $caption = Caption::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($caption->films(), $request);

        return view('tags.show', [
            'caption' => $caption,
            'seoTitle' => 'Субтитри - ' . $caption->title,
            'seoDescription' => 'Фільми з субтитрами «' . $caption->title . '».',
            'pageTitle' => 'Субтитри - ' . $caption->title,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Субтитри', 'url' => route('captions.index')],
                ['title' => $caption->title, 'url' => null],
            ],
        ], $data);
    }

}
