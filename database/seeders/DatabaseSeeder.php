<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\User;
use App\Models\Category;
use App\Models\Year;
use App\Models\Season;
use App\Models\Rating;
use App\Models\Status;
use App\Models\Age;
use App\Models\Quality;
use App\Models\Duration;
use App\Models\Caption;
use App\Models\Actor;
use App\Models\Country;
use App\Models\Company;
use App\Models\Director;
use App\Models\Composer;
use App\Models\Language;
use App\Models\Genre;
use App\Models\Producer;
use App\Models\Comment;
use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Очищення та створення папки для постів
        Storage::deleteDirectory('public/posts');
        Storage::makeDirectory('public/posts');
        Storage::makeDirectory('public/defaults');

        // 1. Викликаємо сідери основних довідників
        $this->call(RatingsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(DurationsTableSeeder::class);
        $this->call(SeasonsTableSeeder::class);
        $this->call(ActorsTableSeeder::class);
        $this->call(AgesTableSeeder::class);
        $this->call(CaptionsTableSeeder::class);
        $this->call(CompaniesTableSeeder::class);
        $this->call(ComposersTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(DirectorsTableSeeder::class);
        $this->call(GenresTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(ProducersTableSeeder::class);
        $this->call(QualitiesTableSeeder::class);

        if (Year::count() === 0) {
            Year::factory(94)->create();
        }

        $this->call(StatusesTableSeeder::class);
        $this->call(SelectionsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call([TelegramAdminSeeder::class,]);

        // 2. Завантажуємо всі створені довідники в пам'ять для Many-to-Many зв'язків
        $captions  = Caption::all();
        $actors    = Actor::all();
        $countries = Country::all();
        $companies = Company::all();
        $directors = Director::all();
        $composers = Composer::all();
        $languages = Language::all();
        $genres    = Genre::all();
        $producers = Producer::all();

        // 3. Створюємо фільми через фабрику та динамічно прив'язуємо зв'язки
        Film::factory(50)->create([
            'author_id'   => fn () => User::inRandomOrder()->first()?->id ?? User::factory(),
            /*'category_id' => fn () => Category::inRandomOrder()->first()?->id ?? Category::factory(),*/
            'category_id' => fn () => Category::where('slug', '!=', 'nevkazano')
                ->inRandomOrder()
                ->first()?->id ?? Category::factory(),
            'year_id'     => fn () => Year::inRandomOrder()->first()?->id ?? Year::factory(),
            'season_id'   => fn () => Season::inRandomOrder()->first()?->id ?? Season::factory(),
            'rating_id'   => fn () => Rating::inRandomOrder()->first()?->id ?? Rating::factory(),
            'status_id'   => fn () => Status::inRandomOrder()->first()?->id ?? Status::factory(),
            'age_id'      => fn () => Age::inRandomOrder()->first()?->id ?? Age::factory(),
            'quality_id'  => fn () => Quality::inRandomOrder()->first()?->id ?? Quality::factory(),
            'duration_id' => fn () => Duration::inRandomOrder()->first()?->id ?? Duration::factory(),
        ])->each(function ($film) use (
            $captions, $actors, $countries, $companies, $directors, $composers, $languages, $genres, $producers
        ) {
            // Прив'язка зв'язків Many-to-Many (випадкова кількість від 1 до 3)
            if ($captions->count() > 0)  $film->captions()->attach($captions->random(rand(1, min(3, $captions->count())))->pluck('id'));
            if ($actors->count() > 0)    $film->actors()->attach($actors->random(rand(1, min(3, $actors->count())))->pluck('id'));
            if ($countries->count() > 0) $film->countries()->attach($countries->random(rand(1, min(2, $countries->count())))->pluck('id'));
            if ($companies->count() > 0) $film->companies()->attach($companies->random(rand(1, min(2, $companies->count())))->pluck('id'));
            if ($directors->count() > 0) $film->directors()->attach($directors->random(rand(1, min(2, $directors->count())))->pluck('id'));
            if ($composers->count() > 0) $film->composers()->attach($composers->random(rand(1, min(2, $composers->count())))->pluck('id'));
            if ($languages->count() > 0) $film->languages()->attach($languages->random(rand(1, min(2, $languages->count())))->pluck('id'));
            if ($genres->count() > 0)    $film->genres()->attach($genres->random(rand(1, min(3, $genres->count())))->pluck('id'));
            if ($producers->count() > 0) $film->producers()->attach($producers->random(rand(1, min(2, $producers->count())))->pluck('id'));

            // Створення коментарів та станів для кожного фільму
            Comment::factory(3)->create([
                'film_id' => $film->id
            ]);

            State::factory(1)->create([
                'film_id' => $film->id
            ]);

            Cache::tags([
                'featured_films',
                'home_films',
                'carousel',
            ])->flush();
        });
    }
}
