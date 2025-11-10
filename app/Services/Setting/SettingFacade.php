<?php

namespace App\Services\Setting;

use \Illuminate\Support\Facades\Facade;

class SettingFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Setting\SettingService';
    }
}
