<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FilmController;
use App\Http\Controllers\Admin\FilmImportController;


Route::prefix('admin/films')->name('admin.films.')->middleware(['auth', 'is.staff'])->group(function () {

    // 1. Імпорт та пошук
    Route::get('/import', [FilmImportController::class, 'index'])->name('import');
    Route::get('/search', [FilmImportController::class, 'search'])->name('search');
    Route::get('/import/{id}', [FilmImportController::class, 'store'])->name('import.store');

    // 2. Масові AJAX дії (наш новий універсальний роут + старі для безпеки)
    Route::post('/bulk-action', [FilmController::class, 'bulkAction'])->name('bulk-action');
    Route::patch('/restore-all', [FilmController::class, 'restoreAll'])->name('restoreAll');
    Route::delete('/force-delete-all', [FilmController::class, 'forceDeleteAll'])->name('forceDeleteAll');

    // 3. Одиночні специфічні дії
    Route::post('/{film}/fetch-imdb-rating', [FilmController::class, 'fetchImdbRating'])->name('fetch-imdb-rating');
    Route::patch('/{id}/restore', [FilmController::class, 'restore'])->name('restore');
    Route::delete('/{id}/forceDelete', [FilmController::class, 'forceDelete'])->name('forceDelete');

    // 4. Стандартний CRUD (Ресурс)
    Route::post('/films/bulk-action', [FilmController::class, 'bulkAction'])->name('films.bulk-action');

    /*Route::resource('/', FilmController::class)->except(['show'])->parameters(['' => 'film']);*/
    Route::resource('', FilmController::class)
        ->except(['show'])
        ->parameters(['' => 'film']);
});


