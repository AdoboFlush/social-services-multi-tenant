<?php

namespace App\Http\Middleware;

use Closure;

class CheckLanguange
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
        if($request->has("language")){
            return $next($request)->withCookie('language',$request->language);
        }
        return $next($request);
    }
}
