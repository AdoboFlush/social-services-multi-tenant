<?php

namespace App\Services\Currency;

use \Illuminate\Support\Facades\Facade;

class CurrencyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Currency\CurrencyService';
    }
}
