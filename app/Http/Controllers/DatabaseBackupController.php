<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackup\DatabaseBackupFacade;

class DatabaseBackupController extends Controller
{
    public $databaseBackupFacade;

    public function __construct(DatabaseBackupFacade $databaseBackupFacade)
    {
        $this->databaseBackupFacade = $databaseBackupFacade;
    }

    public function index()
    {
        return $this->databaseBackupFacade::index();
    }

    public function backup()
    {
        return $this->databaseBackupFacade::databaseBackup();
    }
}
