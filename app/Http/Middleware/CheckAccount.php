<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckAccount
{
    const CLOSED = "Closed";

    const CONTACT_US = "https://orientalwallet.com/#contact";
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check() && Auth::user()->account_status == self::CLOSED){
            $language = Auth::user()->user_information->language;
            Auth::logout();
            return redirect('/login')
                ->withCookie('language',$language)
                ->withErrors([
                    "closed_email" => "Your account has been disabled. Please <a href='{url}'>contact us</a> for further assistance.",
                    "url" => self::CONTACT_US
                ]);
        }
        return $next($request);
    }
}
