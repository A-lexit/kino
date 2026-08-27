<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('film-json', [App\Http\Controllers\Api\FilmController::class, 'show'])->name('film-json');


Route::post('film-views-increment', [App\Http\Controllers\Api\FilmController::class, 'viewsIncrement'])->name('film-views-increment');
Route::post('film-likes-increment', [App\Http\Controllers\Api\FilmController::class, 'likesIncrement'])->name('film-likes-increment');

Route::middleware(['web', 'auth'])->post('film-add-comment', [App\Http\Controllers\Api\CommentController::class, 'store'])->name('film-add-comment');

Route::fallback(function() {
    return response()->json(['message' => 'API route not found'], 404);
});

