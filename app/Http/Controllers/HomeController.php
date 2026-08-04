<?php

namespace App\Http\Controllers;

use App\Constants\CategoryIds;
use App\Models\Setting;
use App\Repositories\HomeRepository;
use App\Enums\CategorySlug;

class HomeController extends Controller
{
    public function index(HomeRepository $homeRepository)
    {
        $setting = Setting::first();
        $title = $setting ? $setting->title : 'Default Title';
        $description = $setting ? $setting->description : 'Default Description';

        $films       = $homeRepository->homeFilmsByEnum(CategorySlug::FILMS, 10);
        $serials     = $homeRepository->homeFilmsByEnum(CategorySlug::SERIALS, 10);
        $mults       = $homeRepository->homeFilmsByEnum(CategorySlug::MULTS, 10);
        $multserials = $homeRepository->homeFilmsByEnum(CategorySlug::MULTSERIALS, 10);


        return view('home', compact(
            'films',
            'serials',
            'mults',
            'multserials',
            'title',
            'description'
        ));
    }

}
