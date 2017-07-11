<?php

namespace App\Http\Controllers;

use App\Classes\Blocks\AdminBar;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{

    public static $appSetts = [
        'title' => 'ONE',
    ];

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        AdminBar::shareMenu();
    }

    public function index()
    {
        return view('general.main');
    }

    public function page_404()
    {
        if(request()->wantsJson()){
            return response()->json(['error'=>'Page Not Found!'], 404);
        }else {
            return response()->view('general.404', [], 404);
        }
    }

    public function page_403()
    {
        return view('general.403');
    }

}
