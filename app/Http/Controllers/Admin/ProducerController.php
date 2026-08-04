<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NameRequest;
use App\Models\Producer;
use Illuminate\Http\Request;

class ProducerController extends Controller
{
    public function index()
    {
        $producers = Producer::paginate(20);
        return view('admin.producers.index', compact('producers'));
    }

    public function create()
    {
        return view('admin.producers.create');
    }

    public function store(NameRequest $request)
    {
        Producer::create($request->all());
        return redirect()->route('admin.producers.index')->with('success', 'Продюсера додано');
    }

    public function edit(string $id)
    {
        $producer = Producer::findOrFail($id);
        return view('admin.producers.edit', compact('producer'));
    }

    public function update(NameRequest $request, string $id)
    {
        $producer = Producer::findOrFail($id);
        $producer->update($request->all());
        return redirect()->route('admin.producers.index')->with('success', 'Зміни збережені');
    }


    // Одиночне видалення через AJAX
    public function destroy(Producer $producer)
    {
        if ($producer->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити продюсера «{$producer->name}», оскільки він пов'язаний з фільмами!"
            ], 422);
        }

        $producer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Продюсера успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:producers,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $producer = Producer::find($id);
            if ($producer && $producer->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Продюсер «{$producer->name}» пов'язаний з фільмами."
                ], 422);
            }
        }

        Producer::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибраних продюсерів успішно видалено.'
        ]);
    }

}
