<?php

namespace App\Services\DatabaseBackup;

use App\Jobs\DatabaseBackup;
use App\Services\BaseService;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class DatabaseBackupService extends BaseService
{
    public function index()
    {
        $files = Storage::disk('local')->allFiles('backup');
        return view('backend.administration.database_backup',compact('files'));
    }

    public function databaseBackup()
    {
        if(!env('QUEUE_WORKER_STARTED',false)){
            Artisan::call('database:backup');
        } else {
            DatabaseBackup::dispatch();
        }
        return back()->with('success', _lang('Database Backup is now working'));
    }
}
