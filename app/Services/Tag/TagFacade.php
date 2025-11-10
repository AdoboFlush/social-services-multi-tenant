<?php

namespace App\Services\Tag;

use \Illuminate\Support\Facades\Facade;

class TagFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Tag\TagService';
    }
}
