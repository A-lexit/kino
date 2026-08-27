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
        $this->authorize('viewAny', Rating::class);

        $ratings = Rating::latest('id')->paginate(20);

        return view('admin.ratings.index', compact('ratings'));
    }

    public function create()
    {
        $this->authorize('create', Rating::class);

        return view('admin.ratings.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Rating::class);

        Rating::create($request->validated());

        return redirect()
            ->route('admin.ratings.index')
            ->with('success', 'Рейтинг додано');
    }

    public function edit($id)
    {
        $rating = Rating::findOrFail($id);

        $this->authorize('view', $rating);

        return view('admin.ratings.edit', compact('rating'));
    }

    public function update(TitleRequest $request, $id)
    {
        $rating = Rating::findOrFail($id);

        $this->authorize('update', $rating);

        $rating->update($request->validated());

        return redirect()
            ->route('admin.ratings.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Rating $rating)
    {
        $this->authorize('delete', $rating);

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

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        // Масове видалення доступне лише Admin.
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ratings,id',
            'action' => 'required|string|in:delete',
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
