<?php

namespace App\Services\UserDocument;

use \Illuminate\Support\Facades\Facade;

class UserDocumentFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\UserDocument\UserDocumentService';
    }
}
