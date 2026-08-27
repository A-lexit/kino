<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Traits\FilterableFilmsForCategory;

class CategoryController extends Controller
{
    use FilterableFilmsForCategory;

    public function show(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $data = $this->getFilteredFilmsAndFiltersForCategory($category, $request);

        return view('tags.show', array_merge([
            'seoTitle' => $category->title,
            'seoDescription' => 'Дивіться онлайн фільми у категорії «' . $category->title . '» — великий вибір, якісне зображення, українська озвучка.',
            'pageTitle' => $category->title,
            'filterPartial' => 'layouts.inc.body.filters.filter-gen-sel-per',
            'breadcrumbs' => [
                ['title' => 'Головна', 'url' => route('home')],
                ['title' => $category->title, 'url' => null],
            ],
        ], $data));
    }

}
