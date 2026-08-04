<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index()
    {
        $subs = Subscription::paginate(20);
        return view('admin.subs.index', compact('subs'));
    }

    public function create()
    {
        return view('admin.subs.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:subscriptions'
        ]);

        Subscription::add($request->get('email'));

        return redirect()->route('admin.subscribers.index')->with('success', 'Підписника додано');
    }


    // Одиночне видалення через AJAX
    public function destroy($id)
    {
        $sub = Subscription::findOrFail($id);
        $sub->delete();

        return response()
            ->json([
            'success' => true,
            'message' => 'Підписника успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
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
