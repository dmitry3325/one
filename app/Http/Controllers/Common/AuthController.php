<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Middleware\CurrentPageState;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Input;

class AuthController extends Controller
{

    use AuthenticatesUsers;

    //    protected $redirectTo = '/';

    public static $appSetts = [
        'title' => 'Авторизация',
        'auth'  => 'none',
    ];

    public function index()
    {
        return view('auth.index');
    }

    public function redirectTo()
    {
        return \Session::get(CurrentPageState::PAGE_NAME, '/');
    }

    public function login()
    {
        $this->validateLogin(request());

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts(request())) {
            $this->fireLockoutEvent(request());

            return $this->sendLockoutResponse(request());
        }

        if ($this->attemptLogin(request())) {
            return $this->sendLoginResponse(request());
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts(request());

        return $this->sendFailedLoginResponse(request());
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $this->guard()->logout();

        request()->session()->flush();

        request()->session()->regenerate();

        return redirect('/common/auth');
    }
}