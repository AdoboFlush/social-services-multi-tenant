<?php

namespace App\Services\Profile;

use \Illuminate\Support\Facades\Facade;

class ProfileFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Profile\ProfileService';
    }
}
