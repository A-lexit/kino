<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */



    protected function redirectTo()
    {
        if (auth()->user()->isStaff()) {
            return route('admin.dashboard');
        }

        return route('profile');
    }


    // 1. Скільки разів можна помилитися (наприклад, 3)
    protected $maxAttempts = 3;

    // 2. На скільки хвилин заблокувати вхід (наприклад, на 10 хвилин)
    protected $decayMinutes = 1;



    public function __construct()
    {
        /*$this->middleware('banuser')->only('login');*/
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');

    }




}


