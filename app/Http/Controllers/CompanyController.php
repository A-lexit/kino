<?php
namespace App\Http\Controllers;

use App\Models\Company;
use App\Traits\FilterableFilmsInTags;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use FilterableFilmsInTags;

    public function index()
    {
        $companies = Company::paginate(20);

        return view('tags.index', [
            'items' => $companies,
            'labelField' => 'title',
            'showRoute' => 'companies.show',
            'seoTitle' => 'Кінокомпанії',
            'seoDescription' => 'Список усіх кінокомпаній, представлених на сайті.',
            'pageTitle' => 'Кінокомпанії',
            'description' => 'Оберіть кінокомпанію, щоб переглянути всі фільми та серіали, вироблені нею.',
        ]);
    }

    public function show(Request $request, $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndCategoriesForTags($company->films(), $request);

        return view('tags.show', [
            'seoTitle' => $company->title,
            'seoDescription' => 'Фільми виробництва «' . $company->title . '».',
            'pageTitle' => 'Кінокомпанія - ' . $company->title,
            'filterPartial' => 'layouts.inc.body.filters.filter-category',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => 'Компанії', 'url' => route('companies.index')],
                ['title' => $company->title, 'url' => null],
            ],
        ], $data);
    }

}
