<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\CacheController;

// -------------------------------------------------------------
// ПІДКЛЮЧЕННЯ ІЗОЛЬОВАНИХ МОДУЛІВ
// -------------------------------------------------------------
require __DIR__ . '/admin/films.php';

// -------------------------------------------------------------
// ОСНОВНА ГРУПА АДМІНІСТРАЦІЇ
// -------------------------------------------------------------
    Route::get('/admin', [MainController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware(['auth', 'is.staff']);

    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'is.admin']], function () {
        // Головна панель та системні налаштування
        // Route::get('/', ...) видалено звідси — тепер окремо вище
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/clear-cache', [CacheController::class, 'clear'])->name('cache.clear');




    // Управління контентом верхнього рівня
    Route::post('/categories/bulk-action', [\App\Http\Controllers\Admin\CategoryController::class, 'bulkAction'])->name('categories.bulk-action');
    Route::resource('/categories', CategoryController::class)->except(['show']);

    Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::post('/menu/activate', [MenuController::class, 'activateMenu'])->name('menu.activate');

    // Користувачі, коментарі та підписки
    Route::post('/users/bulk-action', [\App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('/users/toggle/{id}', [\App\Http\Controllers\Admin\UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('/users', UserController::class)->except(['show']);

    Route::post('/subscribers/bulk-action', [\App\Http\Controllers\Admin\SubscriberController::class, 'bulkAction'])->name('subscribers.bulk-action');
    Route::resource('/subscribers', SubscriberController::class)->except(['show', 'edit', 'update']);

    Route::post('/comments/bulk-action', [\App\Http\Controllers\Admin\CommentController::class, 'bulkAction'])->name('comments.bulk-action');
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::get('/comments/toggle/{id}', [CommentController::class, 'toggle'])->name('comments.toggle');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Довідники та характеристики фільмів (Словники)
    Route::post('/actors/bulk-action', [App\Http\Controllers\Admin\ActorController::class, 'bulkAction'])->name('actors.bulk-action');
    Route::resource('/actors', \App\Http\Controllers\Admin\ActorController::class)->except(['show']);

    Route::post('/ages/bulk-action', [\App\Http\Controllers\Admin\AgeController::class, 'bulkAction'])->name('ages.bulk-action');
    Route::resource('/ages', \App\Http\Controllers\Admin\AgeController::class)->except(['show']);

    Route::post('/captions/bulk-action', [\App\Http\Controllers\Admin\CaptionController::class, 'bulkAction'])->name('captions.bulk-action');
    Route::resource('/captions', \App\Http\Controllers\Admin\CaptionController::class)->except(['show']);

    Route::post('/companies/bulk-action', [\App\Http\Controllers\Admin\CompanyController::class, 'bulkAction'])->name('companies.bulk-action');
    Route::resource('/companies', \App\Http\Controllers\Admin\CompanyController::class)->except(['show']);

    Route::post('/composers/bulk-action', [\App\Http\Controllers\Admin\ComposerController::class, 'bulkAction'])->name('composers.bulk-action');
    Route::resource('/composers', \App\Http\Controllers\Admin\ComposerController::class)->except(['show']);

    Route::post('/countries/bulk-action', [\App\Http\Controllers\Admin\CountryController::class, 'bulkAction'])->name('countries.bulk-action');
    Route::resource('/countries', \App\Http\Controllers\Admin\CountryController::class)->except(['show']);

    Route::post('/directors/bulk-action', [\App\Http\Controllers\Admin\DirectorController::class, 'bulkAction'])->name('directors.bulk-action');
    Route::resource('/directors', \App\Http\Controllers\Admin\DirectorController::class)->except(['show']);

    Route::post('/durations/bulk-action', [\App\Http\Controllers\Admin\DurationController::class, 'bulkAction'])->name('durations.bulk-action');
    Route::resource('/durations', \App\Http\Controllers\Admin\DurationController::class)->except(['show']);

    Route::post('/genres/bulk-action', [\App\Http\Controllers\Admin\GenreController::class, 'bulkAction'])->name('genres.bulk-action');
    Route::resource('/genres', \App\Http\Controllers\Admin\GenreController::class)->except(['show']);

    Route::post('/languages/bulk-action', [\App\Http\Controllers\Admin\LanguageController::class, 'bulkAction'])->name('languages.bulk-action');
    Route::resource('/languages', \App\Http\Controllers\Admin\LanguageController::class)->except(['show']);

    Route::post('/producers/bulk-action', [\App\Http\Controllers\Admin\ProducerController::class, 'bulkAction'])->name('producers.bulk-action');
    Route::resource('/producers', \App\Http\Controllers\Admin\ProducerController::class)->except(['show']);

    Route::post('/qualities/bulk-action', [\App\Http\Controllers\Admin\QualityController::class, 'bulkAction'])->name('qualities.bulk-action');
    Route::resource('/qualities', \App\Http\Controllers\Admin\QualityController::class)->except(['show']);

    Route::post('/ratings/bulk-action', [\App\Http\Controllers\Admin\RatingController::class, 'bulkAction'])->name('ratings.bulk-action');
    Route::resource('/ratings', \App\Http\Controllers\Admin\RatingController::class)->except(['show']);

    Route::post('/seasons/bulk-action', [\App\Http\Controllers\Admin\SeasonController::class, 'bulkAction'])->name('seasons.bulk-action');
    Route::resource('/seasons', \App\Http\Controllers\Admin\SeasonController::class)->except(['show']);

    Route::post('/selections/bulk-action', [\App\Http\Controllers\Admin\SelectionController::class, 'bulkAction'])->name('selections.bulk-action');
    Route::resource('/selections', \App\Http\Controllers\Admin\SelectionController::class)->except(['show']);

    Route::post('/statuses/bulk-action', [\App\Http\Controllers\Admin\StatusController::class, 'bulkAction'])->name('statuses.bulk-action');
    Route::resource('/statuses', \App\Http\Controllers\Admin\StatusController::class)->except(['show']);

    Route::post('/years/bulk-action', [\App\Http\Controllers\Admin\YearController::class, 'bulkAction'])->name('years.bulk-action');
    Route::resource('/years', \App\Http\Controllers\Admin\YearController::class)->except(['show']);

});


Route::get('{category}/{slug}', [App\Http\Controllers\FilmController::class, 'show'])->name('single');

