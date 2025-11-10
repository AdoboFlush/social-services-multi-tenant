<?php

namespace App\Services\Password;

use \Illuminate\Support\Facades\Facade;

class PasswordFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Password\PasswordService';
    }
}
