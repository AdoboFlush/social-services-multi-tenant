<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Database Backup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $filename = 'DB-BACKUP-'.time().'.sql';
            $command = "mysqldump --no-tablespaces --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . " > " . storage_path() . "/app/" . $filename;

            $returnVar = NULL;
            $output  = NULL;

            exec($command, $output, $returnVar);

            Log::info("Logging Database Backup: ".$filename);
        } catch(\Exception $err) {
            Log::error("Logging Database Backup Error: ". $err->getMessage());
        }


    }
}
