<?php
namespace App\Services;

use App\Models\Category;
use App\Models\Year;
use App\Models\Rating;
use App\Models\Duration;
use App\Models\Status;
use App\Models\Age;
use App\Models\Quality;
use App\Models\Season;
use App\Models\Composer;
use App\Models\Company;
use App\Models\Director;
use App\Models\Actor;
use App\Models\Producer;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Country;
use App\Models\Caption;
use App\Models\Selection;
use Illuminate\Support\Facades\Cache;

class FormDataService {
    public function getFormData() {
        return Cache::tags(['form_data'])->remember('film_form_lists', 86400, function () {
            $data = [];
            $data['categories'] = Category::pluck('title', 'id')->all();
            $data['years'] = Year::pluck('title', 'id')->all();
            $data['ratings'] = Rating::pluck('title', 'id')->all();
            $data['durations'] = Duration::pluck('title', 'id')->all();
            $data['statuses'] = Status::pluck('title', 'id')->all();
            $data['ages'] = Age::pluck('title', 'id')->all();
            $data['qualities'] = Quality::pluck('title', 'id')->all();
            $data['seasons'] = Season::pluck('title', 'id')->all();
            $data['composers'] = Composer::pluck('name', 'id')->all();
            $data['companies'] = Company::pluck('title', 'id')->all();
            $data['directors'] = Director::pluck('name', 'id')->all();
            $data['actors'] = Actor::pluck('name', 'id')->all();
            $data['producers'] = Producer::pluck('name', 'id')->all();
            $data['genres'] = Genre::pluck('title', 'id')->all();
            $data['languages'] = Language::pluck('title', 'id')->all();
            $data['countries'] = Country::pluck('title', 'id')->all();
            $data['captions'] = Caption::pluck('title', 'id')->all();
            $data['selections'] = Selection::pluck('title', 'id')->all();
            return $data;
        });
    }
}
