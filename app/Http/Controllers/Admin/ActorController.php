<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NameRequest;
use App\Models\Actor;
use Illuminate\Http\Request;

class ActorController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Actor::class);

        $actors = Actor::latest('id')->paginate(20);

        return view('admin.actors.index', compact('actors'));
    }

    public function create()
    {
        $this->authorize('create', Actor::class);

        return view('admin.actors.create');
    }

    public function store(NameRequest $request)
    {
        $this->authorize('create', Actor::class);

        Actor::create($request->validated());

        return redirect()
            ->route('admin.actors.index')
            ->with('success', 'Актор доданий');
    }

    public function edit(string $id)
    {
        $actor = Actor::findOrFail($id);

        // Viewer також може зайти у форму перегляду.
        $this->authorize('view', $actor);

        return view('admin.actors.edit', compact('actor'));
    }

    public function update(NameRequest $request, string $id)
    {
        $actor = Actor::findOrFail($id);

        // Admin та Editor можуть зберігати зміни.
        $this->authorize('update', $actor);

        $actor->update($request->validated());

        return redirect()
            ->route('admin.actors.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення актора через AJAX.
     */
    public function destroy(Actor $actor)
    {
        $this->authorize('delete', $actor);

        if ($actor->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити актора «{$actor->name}», оскільки він прив'язаний до фільмів!"
            ], 422);
        }

        $actor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Актора успішно видалено.'
        ]);
    }

    /**
     * Масове видалення акторів через AJAX.
     */
    public function bulkAction(Request $request)
    {
        // Bulk delete доступний лише Admin.
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:actors,id',
            'action' => 'required|string|in:delete',
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $actor = Actor::find($id);

            if ($actor && $actor->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Актор «{$actor->name}» пов'язаний з фільмами."
                ], 422);
            }
        }

        Actor::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибраних акторів успішно видалено.'
        ]);
    }
}
