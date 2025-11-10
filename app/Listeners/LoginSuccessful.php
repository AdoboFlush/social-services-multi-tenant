<?php

namespace App\Listeners;

use IlluminateAuthEventsLogin;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class LoginSuccessful
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(Login $event)
    {
        if($event->user->user_type === "admin"){
            activity('Manager and Admin')
                ->performedOn($event->user)
                ->log('Log In');
        }
    }
}
