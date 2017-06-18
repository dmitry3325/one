<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

class GoodsController extends Controller{

    public static $appSetts = [
        'title' => 'Товары'
    ];

    public function index(){
        return view('general.main');
    }
}