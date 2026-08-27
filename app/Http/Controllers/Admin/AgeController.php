<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Age;
use Illuminate\Http\Request;

class AgeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Age::class);

        $ages = Age::latest('id')->paginate(20);

        return view('admin.ages.index', compact('ages'));
    }

    public function create()
    {
        $this->authorize('create', Age::class);

        return view('admin.ages.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Age::class);

        Age::create($request->validated());

        return redirect()
            ->route('admin.ages.index')
            ->with('success', 'Вікову категорію додано');
    }

    public function edit($id)
    {
        $age = Age::findOrFail($id);

        // Viewer може зайти в edit для перегляду.
        $this->authorize('view', $age);

        return view('admin.ages.edit', compact('age'));
    }

    public function update(TitleRequest $request, $id)
    {
        $age = Age::findOrFail($id);

        // Viewer сюди вже не пройде.
        $this->authorize('update', $age);

        $age->update($request->validated());

        return redirect()
            ->route('admin.ages.index')
            ->with('success', 'Зміни збережені');
    }

    public function destroy(Age $age)
    {
        $this->authorize('delete', $age);

        if ($age->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити обмеження «{$age->title}», оскільки воно використовується у фільмах!"
            ], 422);
        }

        $age->delete();

        return response()->json([
            'success' => true,
            'message' => 'Запис успішно видалено.'
        ]);
    }

    public function bulkAction(Request $request)
    {
        $this->authorize('delete', Age::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ages,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $age = Age::find($id);

            if ($age && $age->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Обмеження «{$age->title}» пов'язане з фільмами."
                ], 422);
            }
        }

        Age::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані записи успішно видалено.'
        ]);
    }
}
