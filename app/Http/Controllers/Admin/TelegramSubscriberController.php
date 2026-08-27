<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramSubscriber;

class TelegramSubscriberController extends Controller
{
    public function index()
    {
        $subscribers = TelegramSubscriber::latest()->paginate(20);
        return view('admin.telegram.index', compact('subscribers'));
    }

    /**Для коректної роботи AJAX + захист адміна від видалення*/
    public function toggleBan(TelegramSubscriber $subscriber)
    {
        if ($subscriber->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Неможливо заблокувати акаунт адміністратора!'
            ], 403);
        }

        $subscriber->update(['is_banned' => !$subscriber->is_banned]);

        return response()->json([
            'success' => true,
            'is_banned' => (int) $subscriber->is_banned,
        ]);
    }

    public function destroy(TelegramSubscriber $subscriber)
    {
        if ($subscriber->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Неможливо видалити акаунт адміністратора!'
            ], 403);
        }

        $subscriber->delete();

        return response()->json(['success' => true]);
    }

}
