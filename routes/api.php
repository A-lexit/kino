<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('film-json', [App\Http\Controllers\Api\FilmController::class, 'show']);


Route::put('film-views-increment', [App\Http\Controllers\Api\FilmController::class, 'viewsIncrement']);
Route::put('film-likes-increment', [App\Http\Controllers\Api\FilmController::class, 'likesIncrement']);

Route::middleware(['web', 'auth'])->post('film-add-comment', [App\Http\Controllers\Api\CommentController::class, 'store']);


Route::fallback(function() {
    abort(404);

});

