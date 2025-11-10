<?php

namespace App\Http\Middleware;

use App\Setting;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Closure;
use Symfony\Component\HttpFoundation\IpUtils;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Auth;
use Carbon\Carbon;

class CheckForMaintenanceMode extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        //
        'admin/*','login', 'logout', '/'
    ];

    public function handle($request, Closure $next)
    {   
        $isMaintenance = Setting::where('name','isMaintenance')->first()->value;
        if ($isMaintenance === Setting::MAINTENANCE_ACTIVE) {
            if ($this->inExceptArray($request)) {
                return $next($request);
            }
    
            if (Auth::check() && Auth::user()->user_type == 'admin') {
                return $next($request);
            }
    
            if (Auth::check() && Auth::user()->is_admin_account) {
                return $next($request);
            }

            throw new MaintenanceModeException(strtotime(Carbon::now()->toDateTimeString()));
        }


        return $next($request);
    }

}
