<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Film;
use App\Http\Resources\FilmResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class FilmController extends Controller
{
    public function show(Request $request) {

        $slug = $request->get('slug');
        $film = Film::with('comments', 'state', 'user')->where('slug', $slug)->firstOrFail();;

        return new FilmResource($film);
    }

    public function viewsIncrement(Request $request)
    {
        try {
            $slug = $request->get('slug');
            $film = Film::with('state')->where('slug', $slug)->firstOrFail();

            if (!$film->state) {
                $film->state()->create(); // Створюємо state
                $film->refresh();       // Оновлюємо модель
            }

            $film->state->increment('vviews');
            return new FilmResource($film);

        } catch (ModelNotFoundException $e) { // Ловимо помилку "not found" окремо
            return response()->json(['error' => 'Фільм не знайдено'], 404);
        } catch (\Exception $e) {
            Log::error('Помилка збільшення переглядів: ' . $e->getMessage());
            return response()->json(['error' => 'Помилка'], 500);
        }
    }

    public function likesIncrement(Request $request)
    {
        try {
            $slug = $request->get('slug');
            $film = Film::with('state')->where('slug', $slug)->firstOrFail();

            if (!$film->state) {
                $film->state()->create(); // Створюємо state
                $film->refresh();       // Оновлюємо модель
            }

            $request->get('increment') ? $film->state->increment('likes') : $film->state->decrement('likes');
            return new FilmResource($film);

        } catch (ModelNotFoundException $e) { // Ловимо помилку "not found" окремо
            return response()->json(['error' => 'Фільм не знайдено'], 404);
        } catch (\Exception $e) {
            Log::error('Помилка з лайками: ' . $e->getMessage());
            return response()->json(['error' => 'Помилка'], 500);
        }
    }

}
