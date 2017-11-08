<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $urlParts = explode('/', $request->path());
        if (array_get($urlParts, 0) === 'p') {
            
        }
        elseif ($request->wantsJson()) {
            if (!Auth::check()) {
                return response()->json(['error' => 'Not Authorized'], 403);
            }
        }
        else if (!Auth::check()) {
            return redirect('/common/auth');
        }

        return $next($request);
    }
}
