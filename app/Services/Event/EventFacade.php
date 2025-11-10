<?php

namespace App\Services\Event;

use \Illuminate\Support\Facades\Facade;

class EventFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Event\EventService';
    }
}
