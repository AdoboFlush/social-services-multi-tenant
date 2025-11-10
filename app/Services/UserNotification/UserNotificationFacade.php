<?php

namespace App\Services\UserNotification;

use \Illuminate\Support\Facades\Facade;

class UserNotificationFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\UserNotification\UserNotificationService';
    }
}
