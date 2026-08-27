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
        $this->authorize('viewAny', Producer::class);

        $producers = Producer::latest('id')->paginate(20);

        return view('admin.producers.index', compact('producers'));
    }

    public function create()
    {
        $this->authorize('create', Producer::class);

        return view('admin.producers.create');
    }

    public function store(NameRequest $request)
    {
        $this->authorize('create', Producer::class);

        Producer::create($request->validated());

        return redirect()
            ->route('admin.producers.index')
            ->with('success', 'Продюсера додано');
    }

    public function edit(string $id)
    {
        $producer = Producer::findOrFail($id);

        // Viewer може зайти у форму для перегляду.
        $this->authorize('view', $producer);

        return view('admin.producers.edit', compact('producer'));
    }

    public function update(NameRequest $request, string $id)
    {
        $producer = Producer::findOrFail($id);

        // Admin та Editor можуть зберігати зміни.
        $this->authorize('update', $producer);

        $producer->update($request->validated());

        return redirect()
            ->route('admin.producers.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Producer $producer)
    {
        $this->authorize('delete', $producer);

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

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        // Масове видалення доступне лише Admin.
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:producers,id',
            'action' => 'required|string|in:delete',
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
