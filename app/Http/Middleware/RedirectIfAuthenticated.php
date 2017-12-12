<?php

namespace App\Http\Middleware;

use App\Models\Auth\UserRole;
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
            //отдаем картинки без авторизации
        }
        elseif (!Auth::check() || !Auth::user()->hasRole(UserRole::ROLE_GOD)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Not Authorized'], 403);
            }
            return redirect('/common/auth');
        }

        return $next($request);
    }
}
