<?php


namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CurrentPageState.
 */
class CurrentPageState
{
    public const PAGE_NAME = 'pageFrom';

    /**
     * @var Route
     */
    private $current;

    /**
     * List of excluded route names.
     *
     * @var array
     */
    private $exclude = [
        '/common/auth/',
        '/',
        '/common/auth',
        'common/auth',
    ];

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Authenticatable
     */
    private $user;

    /**
     * CurrentPageState constructor.
     *
     * @param Session              $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param  Request  $request
     * @param  \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next)
    {
        if (! $this->session->has(static::PAGE_NAME)) {
            $this->session->put(static::PAGE_NAME, '/');
        }

        $response = $next($request);

        if (!Auth::check()) {
            $this->storeRoute($request);
        }

//        $g = $this->session->get(CurrentPageState::PAGE_NAME, '/');
//        var_dump($g);exit();

        return $response;
    }

    /**
     * @param Request $request
     */
    private function storeRoute(Request $request)
    {
        if (in_array($request->getPathInfo(), $this->exclude)) {
            return;
        }

        $this->session->put(static::PAGE_NAME, $request->getPathInfo());
    }
}
