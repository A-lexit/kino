<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index()
    {
        $ratings = Rating::paginate(20);
        return view('admin.ratings.index', compact('ratings'));
    }

    public function create()
    {
        return view('admin.ratings.create');
    }

    public function store(TitleRequest $request)
    {
        Rating::create($request->all());
        return redirect()->route('admin.ratings.index')->with('success', 'Рейтинг додано');
    }

    public function edit($id)
    {
        $rating = Rating::findOrFail($id);
        return view('admin.ratings.edit', compact('rating'));
    }

    public function update(TitleRequest $request, $id)
    {
        $rating = Rating::findOrFail($id);
        $rating->update($request->all());
        return redirect()->route('admin.ratings.index')->with('success', 'Зміни збережені');
    }


    // Одиночне видалення через AJAX
    public function destroy(Rating $rating)
    {
        if ($rating->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити рейтинг «{$rating->title}», оскільки він пов'язаний з фільмами!"
            ], 422);
        }

        $rating->delete();

        return response()->json([
            'success' => true,
            'message' => 'Рейтинг успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ratings,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $rating = Rating::find($id);
            if ($rating && $rating->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Рейтинг «{$rating->title}» пов'язаний з фільмами."
                ], 422);
            }
        }

        Rating::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані рейтинги успішно видалено.'
        ]);
    }

}
