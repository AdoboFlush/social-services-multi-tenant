<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class BusinessAccountsOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->account_type == "business") {
            return $next($request);
        }
        return redirect('dashboard');
    }
}
