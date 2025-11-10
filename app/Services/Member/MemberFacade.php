<?php

namespace App\Services\Member;

use \Illuminate\Support\Facades\Facade;

class MemberFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Member\MemberService';
    }
}
