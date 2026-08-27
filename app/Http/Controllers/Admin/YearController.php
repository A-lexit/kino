<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Year;
use Illuminate\Http\Request;

class YearController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Year::class);

        $years = Year::latest('id')->paginate(20);

        return view('admin.years.index', compact('years'));
    }

    public function create()
    {
        $this->authorize('create', Year::class);

        return view('admin.years.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Year::class);

        Year::create($request->validated());

        return redirect()
            ->route('admin.years.index')
            ->with('success', 'Рік додано');
    }

    public function edit($id)
    {
        $year = Year::findOrFail($id);

        $this->authorize('view', $year);

        return view('admin.years.edit', compact('year'));
    }

    public function update(TitleRequest $request, $id)
    {
        $year = Year::findOrFail($id);

        $this->authorize('update', $year);

        $year->update($request->validated());

        return redirect()
            ->route('admin.years.index')
            ->with('success', 'Зміни збережено');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Year $year)
    {
        $this->authorize('delete', $year);

        if ($year->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити рік «{$year->title}», оскільки він пов'язаний з фільмами!"
            ], 422);
        }

        $year->delete();

        return response()->json([
            'success' => true,
            'message' => 'Рік випуску успішно видалено.'
        ]);
    }

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:years,id',
            'action' => 'required|string|in:delete',
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $year = Year::find($id);

            if ($year && $year->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Рік «{$year->title}» пов'язаний з контентом."
                ], 422);
            }
        }

        Year::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані роки успішно видалено.'
        ]);
    }
}
