<?php

use App\Http\Middleware\DraftOrPublicFilm;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// -------------------------------------------------------------
// ГОЛОВНІ ТА СИСТЕМНІ СТОРІНКИ
// -------------------------------------------------------------
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
Route::get('/search-suggestions', \App\Http\Controllers\Api\SearchSuggestController::class)->name('search.suggestions');



// -------------------------------------------------------------
// КОРИСТУВАЦЬКІ ДІЇ (Зв'язок, підписки, профіль)
// -------------------------------------------------------------
if (app()->environment(['local', 'testing'])) {
    Route::get('/send', [\App\Http\Controllers\ContactController::class, 'send'])
        ->name('mail.send');
}


Route::post('/subscribe', [App\Http\Controllers\SubsController::class, 'subscribe'])->name('subscribe');
Route::get('/verify/{token}', [App\Http\Controllers\SubsController::class, 'verify'])->name('subscribe.verify');

/*Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->middleware('auth')->name('profile.edit');*/
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])
    ->middleware('auth')
    ->name('profile');
Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->middleware('auth')->name('profile.update');



// -------------------------------------------------------------
// ДОВІДНИКИ ТА ХАРАКТЕРИСТИКИ ФІЛЬМІВ (За алфавітом)
// -------------------------------------------------------------

Route::resource('actors', App\Http\Controllers\ActorController::class)
    ->only(['index', 'show'])
    ->parameters(['actors' => 'slug']);

Route::resource('ages', App\Http\Controllers\AgeController::class)
    ->only(['index', 'show'])
    ->parameters(['ages' => 'slug']);

Route::resource('captions', App\Http\Controllers\CaptionController::class)
    ->only(['index', 'show'])
    ->parameters(['captions' => 'slug']);

Route::resource('companies', App\Http\Controllers\CompanyController::class)
    ->only(['index', 'show'])
    ->parameters(['companies' => 'slug']);

Route::resource('composers', App\Http\Controllers\ComposerController::class)
    ->only(['index', 'show'])
    ->parameters(['composers' => 'slug']);

Route::resource('countries', App\Http\Controllers\CountryController::class)
    ->only(['index', 'show'])
    ->parameters(['countries' => 'slug']);

Route::resource('directors', App\Http\Controllers\DirectorController::class)
    ->only(['index', 'show'])
    ->parameters(['directors' => 'slug']);



Route::resource('genres', App\Http\Controllers\GenreController::class)
    ->only(['index', 'show'])
    ->parameters(['genres' => 'slug']);

Route::resource('languages', App\Http\Controllers\LanguageController::class)
    ->only(['index', 'show'])
    ->parameters(['languages' => 'slug']);

Route::resource('producers', App\Http\Controllers\ProducerController::class)
    ->only(['index', 'show'])
    ->parameters(['producers' => 'slug']);

Route::resource('qualities', App\Http\Controllers\QualityController::class)
    ->only(['index', 'show'])
    ->parameters(['qualities' => 'slug']);

Route::resource('ratings', App\Http\Controllers\RatingController::class)
    ->only(['index', 'show'])
    ->parameters(['ratings' => 'slug']);


Route::resource('selections', App\Http\Controllers\SelectionController::class)
    ->only(['index', 'show'])
    ->parameters(['selections' => 'slug']);


Route::resource('years', App\Http\Controllers\YearController::class)
    ->only(['index', 'show'])
    ->parameters(['years' => 'slug']);



Auth::routes();

Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);



// Telegram Webhook — захищений від CSRF
Route::post('/telegram/webhook', [App\Http\Controllers\TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook')
    ->withoutMiddleware(['web', 'csrf']);   // знімаємо всю групу web


Route::get('/test-env', fn() => dd(config('services.google')));



