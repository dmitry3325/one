<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{

    public static $appSetts = [
        'title' => 'Авторизация',
        'auth'  => 'none',
    ];

    public function index()
    {
        return view('auth.index');
    }
}