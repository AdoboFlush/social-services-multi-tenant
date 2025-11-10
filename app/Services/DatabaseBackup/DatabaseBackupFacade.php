<?php

namespace App\Services\DatabaseBackup;

use \Illuminate\Support\Facades\Facade;

class DatabaseBackupFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\DatabaseBackup\DatabaseBackupService';
    }
}
