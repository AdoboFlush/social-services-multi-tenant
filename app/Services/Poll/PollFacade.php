<?php

namespace App\Services\Poll;

use \Illuminate\Support\Facades\Facade;

class PollFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Poll\PollService';
    }
}
