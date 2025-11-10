<?php

namespace App\Services\RiskManagement;

use \Illuminate\Support\Facades\Facade;

class RiskManagementFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\RiskManagement\RiskManagementService';
    }
}
