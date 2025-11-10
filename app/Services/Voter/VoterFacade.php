<?php

namespace App\Services\Voter;

use \Illuminate\Support\Facades\Facade;

class VoterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Voter\VoterService';
    }
}
