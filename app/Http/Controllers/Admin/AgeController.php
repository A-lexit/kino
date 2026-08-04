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
        $ages = Age::paginate(20);
        return view('admin.ages.index', compact('ages'));
    }

    public function create()
    {
        return view('admin.ages.create');
    }

    public function store(TitleRequest $request)
    {
        Age::create($request->all());
        return redirect()->route('admin.ages.index')->with('success', 'Вікову категорію додано');
    }

    public function edit($id)
    {
        $age = Age::findOrFail($id);
        return view('admin.ages.edit', compact('age'));
    }

    public function update(TitleRequest $request, $id)
    {
        $age = Age::findOrFail($id);
        $age->update($request->all());
        return redirect()->route('admin.ages.index')->with('success', 'Зміни збережені');
    }


    // Одиночне видалення через AJAX
    public function destroy(Age $age)
    {
        // Перевірка зв'язків (якщо до вікового обмеження прив'язані фільми)
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

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ages,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        // Перевіряємо кожен ID перед видаленням скопом
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
