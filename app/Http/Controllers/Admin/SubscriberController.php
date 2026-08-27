<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Subscription::class);
        $subs = Subscription::latest('id')->paginate(20);
        return view('admin.subs.index', compact('subs'));
    }


    public function create()
    {
        $this->authorize('create', Subscription::class);
        return view('admin.subs.create');
    }


    public function store(Request $request)
    {
        $this->authorize('create', Subscription::class);
        $this->validate($request, [
            'email' => 'required|email|unique:subscriptions'
        ]);

        Subscription::add($request->get('email'));

        return redirect()->route('admin.subscribers.index')->with('success', 'Підписника додано');
    }


    public function destroy($id)
    {
        $sub = Subscription::findOrFail($id);

        $this->authorize('delete', $sub);

        $sub->delete();

        return response()->json([
            'success' => true,
            'message' => 'Підписника успішно видалено.',
        ]);
    }


    public function bulkAction(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:subscriptions,id',
            'action' => 'required|string|in:delete'
        ]);

        Subscription::destroy($request->input('ids'));

        return response()->json([
            'success' => true,
            'message' => 'Вибраних підписників успішно видалено.'
        ]);
    }
}
