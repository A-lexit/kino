<?php
namespace App\Http\Controllers;

use App\Models\Quality;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $qualities = Quality::paginate(20);

        return view('tags.index', [
            'items' => $qualities,
            'labelField' => 'title',
            'showRoute' => 'qualities.show',
            'seoTitle' => 'Якість відео',
            'seoDescription' => 'Список усіх доступних якостей відео на сайті.',
            'pageTitle' => 'Якість відео',
            'description' => 'Оберіть якість відео, щоб переглянути всі фільми та серіали з нею.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $quality = Quality::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($quality->films(), $request);

        return view('tags.show', [
            'quality' => $quality,
            'seoTitle' => 'Якість відео - ' . $quality->title,
            'seoDescription' => 'Фільми з якістю відео «' . $quality->title . '».',
            'pageTitle' => 'Якість відео - ' . $quality->title,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Якості відео', 'url' => route('qualities.index')],
                ['title' => $quality->title, 'url' => null],
            ],
        ], $data);
    }

}
