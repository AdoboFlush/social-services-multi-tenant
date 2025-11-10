<?php

namespace App\Services\IdRequest;

use \Illuminate\Support\Facades\Facade;

class IdRequestFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\IdRequest\IdRequestService';
    }
}
