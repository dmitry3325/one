<?php

namespace App\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Input;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $namespace = $this->namespace . '\\';
        $class     = 'Controller';
        $method    = 'index';
        if (Input::get('method')) {
            $method = explode('?', Input::get('method'))[0];
        }

        $url     = Request::path();
        $parts   = explode('/', $url);
        $appName = [];
        foreach ($parts as $p) {
            if (!$p) {
                continue;
            }
            $appName[] = ucfirst($p);
        }

        if (count($appName)) {
            $namespace .= $appName[0] . '\\';
            if (count($appName) > 1) {
                unset($appName[0]);
            }
        }
        $appName = implode('', $appName);
        if (class_exists($namespace . $appName . 'Controller') && method_exists($namespace . $appName . 'Controller',
                $method)
        ) {
            $class = $appName . 'Controller';
        }
        else {
            $namespace = $this->namespace . '\\';
            $method    = 'page_404';
        }

        $app = $namespace . $class;

        if (isset($app::$appSetts)) {
            \View::share('app_settings', $app::$appSetts);
        }

        $params = Input::get('params');
        $route  = Route::any($url, function () use ($app, $method, $params) {
            $controller = app($app);
            return call_user_func_array([$controller, $method], $params ? $params : []);
        });

        $middleware = ['web'];
        if (!isset($app::$appSetts['auth']) || $app::$appSetts['auth'] !== 'none') {
            $middleware[] = 'auth';
        }

        $route->middleware($middleware);
    }

}
