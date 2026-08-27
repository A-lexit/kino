<?php

namespace Tests\Unit\Providers;

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
use Tests\TestCase;

class PolicyServiceProviderTest extends TestCase
{
    public function test_reference_models_use_category_tag_policy(): void
    {
        $models = [
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

        foreach ($models as $model) {
            $this->assertInstanceOf(
                CategoryTagPolicy::class,
                Gate::getPolicyFor($model),
                "Для {$model} не зареєстрована CategoryTagPolicy."
            );
        }
    }

    public function test_service_models_use_service_policy(): void
    {
        $models = [
            Menu::class,
            Setting::class,
            User::class,
            Subscription::class,
            Comment::class,
        ];

        foreach ($models as $model) {
            $this->assertInstanceOf(
                ServicePolicy::class,
                Gate::getPolicyFor($model),
                "Для {$model} не зареєстрована ServicePolicy."
            );
        }
    }
}
