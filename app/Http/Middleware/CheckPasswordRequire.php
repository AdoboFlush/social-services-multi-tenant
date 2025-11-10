<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Log;
use DB;

class CheckPasswordRequire
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
        $forceChangePassword = env('FORCE_CHANGE_PASSWORD');

        $uri = app()->router->getCurrentRoute()->uri;

        if ($uri != 'user/update-change-password') {            
            if(Auth::check() && Auth::user()->user_type == 'user' && $forceChangePassword){
               $now = Carbon::now();
               $createDate = Carbon::parse(Auth::user()->created_at);

               $password_reset = DB::table('password_resets')
                    ->where('email', Auth::user()->email)
                    ->first();

                if ($password_reset) {
                    $createDate = Carbon::parse($password_reset->created_at);                    
                }

               $diff = $createDate->diffInDays($now); // 90 days only

               if (!Auth::user()->change_password && !$request->ajax() && $uri != 'require-change-password' && $diff > 90) {
                    return redirect('/require-change-password');
               }                        
            }
        }
        return $next($request);
    }
}
