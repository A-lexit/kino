<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Enums\AuthProvider;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver(AuthProvider::Google->value)->stateless()->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver(AuthProvider::Google->value)->stateless()->user();

        // 1. Спочатку шукаємо за надійним індексом (провайдер + ID)
        $user = User::where('provider', AuthProvider::Google->value)
            ->where('provider_id', $googleUser->getId())
            ->first();

        // 2. Якщо не знайшли, шукаємо за email (можливо, юзер вже є в БД)
        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        if (!$user) {
            // Реєстрація нового користувача
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'provider' => AuthProvider::Google,
                'provider_id' => $googleUser->getId(),
                'password' => null,
                'email_verified_at' => now(),
            ]);
            event(new Registered($user));
        } else {
            // Якщо користувач знайдений, але Google ще не прив'язаний
            if (!$user->provider_id) {
                $user->update([
                    'provider' => AuthProvider::Google,
                    'provider_id' => $googleUser->getId(),
                ]);
            }
        }

        Auth::login($user);

        /*return redirect()->to('http://kino2.test/');*/
        return redirect()->to(config('app.url'));
    }

}
