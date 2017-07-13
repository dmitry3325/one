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
        if ($request->wantsJson()) {
            return response()->json(['error'=>'Not Authorized'], 403);
        }

        if (!Auth::check()) {
            return redirect('/common/auth');
        }

        return $next($request);
    }
}
