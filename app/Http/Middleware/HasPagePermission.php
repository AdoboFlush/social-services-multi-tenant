<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Closure;
use Auth;

class HasPagePermission
{
    use AuthenticatesUsers;

    public function handle($request, Closure $next)
    {
        if (!$this->hasPermissions($request)) {
            return abort(401, 'Access Denied');
        }
        return $next($request);
    }

    private function hasPermissions($request)
    {
        $permissions = [];
        $route   = $request->route();
        $actions = $route->getAction(); 

        $user = Auth::user();
        $permission = $user->getAllPermissions()->pluck('name')->toArray(); 

        if (array_key_exists('permission', $actions)) {
            return in_array($actions['permission'], $permission);
        }

        return true;
    }
}
