<?php

namespace App\Services\Staff;

use \Illuminate\Support\Facades\Facade;

class StaffFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Staff\StaffService';
    }
}
