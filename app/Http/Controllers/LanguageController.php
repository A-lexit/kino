<?php
namespace App\Http\Controllers;

use App\Models\Language;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $languages = Language::paginate(20);

        return view('tags.index', [
            'items' => $languages,
            'labelField' => 'title',
            'showRoute' => 'languages.show',
            'seoTitle' => 'Мови озвучки',
            'seoDescription' => 'Список усіх мов озвучки, доступних на сайті.',
            'pageTitle' => 'Мови озвучки',
            'description' => 'Оберіть мову озвучки, щоб переглянути всі фільми та серіали з нею.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $language = Language::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($language->films(), $request);

        return view('tags.show', [
            'language' => $language,
            'seoTitle' => 'Мова озвучки - ' . $language->title,
            'seoDescription' => 'Фільми мовою озвучки «' . $language->title . '».',
            'pageTitle' => 'Мова озвучки - ' . $language->title,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Мови озвучки', 'url' => route('languages.index')],
                ['title' => $language->title, 'url' => null],
            ],
        ], $data);
    }

}
