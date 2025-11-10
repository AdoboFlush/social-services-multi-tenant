<?php

namespace App\Services\SocialServiceAssistance;

use \Illuminate\Support\Facades\Facade;

class SocialServiceAssistanceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\SocialServiceAssistance\SocialServiceAssistanceService';
    }
}
