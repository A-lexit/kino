<?php

namespace App\Http\Controllers;

use App\Jobs\SendSubscribeEmail;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubsController extends Controller
{
    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:subscriptions'
        ]);

        $subs = Subscription::add($request->get('email'));
        $subs->generateToken();
        SendSubscribeEmail::dispatch($subs)->delay(now()->addSeconds(20));

        return redirect()->back()->with('status', 'Перевірте вашу пошту!');
    }

    public function verify($token)
    {
        $subs = Subscription::where('token', $token)->firstOrFail();
        $subs->token = null;
        $subs->save();

        return redirect('/')->with('status', 'Вашу пошту підтверджено! Дякуємо!');
    }

}
