<?php

namespace App\Services\MemberCode;

use \Illuminate\Support\Facades\Facade;

class MemberCodeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\MemberCode\MemberCodeService';
    }
}
