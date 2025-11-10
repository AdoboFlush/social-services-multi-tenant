<?php

namespace App\Console\Commands;

use App\AssistanceEvent;
use App\Services\SocialServiceAssistance\SocialServiceAssistanceFacade;
use App\VoterTagDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncTableFromOtherDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:table-from-other-db {target_table} {created_date_from} {created_date_to?} {execute_insert?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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

        $this->warn('WARNING ! This command is for syncing data from an external database to the local database. Ensure to check the data from local to remote DB first before running the command because this might cause record duplication.');

        $createdDateFrom = $this->argument('created_date_from');
        $createdDateTo = $this->argument('created_date_to') ?? now();
        $executeInsert = $this->argument('execute_insert');
        $targetTable = $this->argument('target_table');

        // Create a new database connection on the fly
        $externalConnection = DB::connection('sync-external-db-mysql');

        $externalData = $externalConnection->table($targetTable)->whereBetween(DB::raw('DATE(created_at)'), [$createdDateFrom, $createdDateTo])->get();

        Log::info('External data retrieved: ' . $externalData->count() . ' records found.');

        foreach ($externalData as $row) {
            $exists = DB::table($targetTable)->where('created_at', $row->created_at)->exists();
            if(!$exists) {
                $message = 'Record does not exist in local DB: ' . $row->created_at . ' TABLE : ' . $targetTable;
                $this->info($message);

                if($executeInsert) {
                    Log::info($message);
                }

                $arrayRow = collect($row)->toArray();
                unset($arrayRow['id']);
                if($executeInsert) {
                    $message  = 'Applying insert to local db - created_at : ' . $arrayRow["created_at"];
                    $this->info($message);
                    Log::info($message);
                    DB::table($targetTable)->insert($arrayRow);
                }
            }
        }

        $this->info('Sync process completed.');
    }
}

