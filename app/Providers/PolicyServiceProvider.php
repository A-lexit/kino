<?php
namespace App\Providers;

use App\Models\Actor;
use App\Models\Age;
use App\Models\Caption;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Composer;
use App\Models\Country;
use App\Models\Director;
use App\Models\Duration;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Menu;
use App\Models\Producer;
use App\Models\Quality;
use App\Models\Rating;
use App\Models\Season;
use App\Models\Selection;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Year;
use App\Policies\CategoryTagPolicy;
use App\Policies\ServicePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PolicyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $referenceModels = [
            Category::class,
            Actor::class,
            Age::class,
            Caption::class,
            Company::class,
            Composer::class,
            Country::class,
            Director::class,
            Duration::class,
            Genre::class,
            Language::class,
            Producer::class,
            Quality::class,
            Rating::class,
            Season::class,
            Selection::class,
            Status::class,
            Year::class,
        ];

        foreach ($referenceModels as $model) {
            Gate::policy($model, CategoryTagPolicy::class);
        }

        $serviceModels = [
            Menu::class,
            Setting::class,
            User::class,
            Subscription::class,
            Comment::class,
        ];

        foreach ($serviceModels as $model) {
            Gate::policy($model, ServicePolicy::class);
        }
    }

}
