<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchSuggestController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $films = Film::published()
            ->with('category:id,slug')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('origin_title', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'title', 'slug', 'category_id', 'thumbnail']);

        $results = $films->map(function (Film $film) {
            return [
                'title' => $film->title,
                /*'image' => $film->getImage(),*/
                /*'image' => $film->getSearchImage(),*/
                'image' => app(\App\Media\FilmImageResolver::class)->search($film),
                'url' => route('single', [
                    'category' => $film->category->slug,
                    'slug' => $film->slug,
                ]),
            ];
        });

        return response()->json($results);
    }

}
