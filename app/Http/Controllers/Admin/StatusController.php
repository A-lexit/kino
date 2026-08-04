<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::paginate(20);
        return view('admin.statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('admin.statuses.create');
    }

    public function store(TitleRequest $request)
    {
        Status::create($request->all());
        return redirect()->route('admin.statuses.index')->with('success', 'Статус додано');
    }

    public function edit($id)
    {
        $status = Status::findOrFail($id);
        return view('admin.statuses.edit', compact('status'));
    }

    public function update(TitleRequest $request, $id)
    {
        $status = Status::findOrFail($id);
        $status->update($request->all());
        return redirect()->route('admin.statuses.index')->with('success', 'Зміни збережені');
    }


    // Одиночне видалення через AJAX
    public function destroy(Status $status)
    {
        if ($status->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити статус «{$status->title}», оскільки він пов'язаний з фільмами!"
            ], 422);
        }

        $status->delete();

        return response()->json([
            'success' => true,
            'message' => 'Статус успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:statuses,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $status = Status::find($id);
            if ($status && $status->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Статус «{$status->title}» пов'язаний з фільмами."
                ], 422);
            }
        }

        Status::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані статуси успішно видалено.'
        ]);
    }

}
