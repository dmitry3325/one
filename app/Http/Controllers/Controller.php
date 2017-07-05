<?php

namespace App\Http\Controllers;

use App\Classes\Menu\AdminBar;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class Controller extends BaseController
{

    public static $appSetts = [
        'title' => 'ONE'
    ];

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        AdminBar::shareMenu();
    }

    public function index(){
        return view('general.main');
    }


    public function page_404(){
        return view('general.404');
    }

    public function page_403(){
        return view('general.403');
    }

}
