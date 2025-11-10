<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class LogoutSuccessful
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
    public function handle(Logout $event)
    {
        if($event->user->user_type === "admin"){
            activity('Manager and Admin')
                ->performedOn($event->user)
                ->log('Log Out');
        }
    }
}
