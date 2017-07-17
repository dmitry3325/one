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
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public static $appSetts = [
        'title' => 'ONE',
    ];

    protected static $baseNamespace = 'App\Http\Controllers\\';

    public function __construct()
    {
        AdminBar::shareMenu();
    }

    public function index()
    {
        $class = get_called_class();
        return view('general.main', ['app' => $class::$appSetts]);
    }


    public function page_404()
    {
        if (request()->wantsJson()) {
            return response()->json(['error' => 'Page Not Found!'], 404);
        }
        else {
            return response()->view('general.404', [], 404);
        }
    }

    public function page_403()
    {
        return view('general.403');
    }

}
