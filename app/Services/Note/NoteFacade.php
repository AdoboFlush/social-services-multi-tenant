<?php

namespace App\Services\Note;

use \Illuminate\Support\Facades\Facade;

class NoteFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Note\NoteService';
    }
}
