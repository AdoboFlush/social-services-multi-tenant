<?php

namespace App\Services\Account;

use \Illuminate\Support\Facades\Facade;

class AccountFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Account\AccountService';
    }
}
