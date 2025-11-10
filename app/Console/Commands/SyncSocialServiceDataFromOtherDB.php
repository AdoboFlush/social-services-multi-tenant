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

class SyncSocialServiceDataFromOtherDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:social-service-data-from-other-db {created_date_from} {created_date_to?} {execute_insert?}';

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

        $createdDateFrom = $this->argument('created_date_from');
        $createdDateTo = $this->argument('created_date_to') ?? now();
        $executeInsert = $this->argument('execute_insert');

        // Create a new database connection on the fly
        $externalConnection = DB::connection('sync-external-db-mysql');

        $externalData = $externalConnection->table(env('SYNC_TARGET_TABLE'))->whereBetween(DB::raw('DATE(created_at)'), [$createdDateFrom, $createdDateTo])->get();

        Log::info('External data retrieved: ' . $externalData->count() . ' records found.');

        foreach ($externalData as $row) {
            $exists = DB::table(env('SYNC_TARGET_TABLE'))->where('created_at', $row->created_at)->where('request_type_id', $row->request_type_id)->exists();
            if(!$exists) {
                $message = 'Record does not exist in local DB: ' . $row->created_at . ' NAME: ' . $row->first_name . ' ' . $row->last_name . ' - CTRL #: ' . $row->control_number . ' Request ID: ' . $row->request_type_id;
                $this->info($message);

                if($executeInsert) {
                    Log::info($message);
                }

                $arrayRow = collect($row)->toArray();
                unset($arrayRow['id']);
                $arrayRow["control_number"] = SocialServiceAssistanceFacade::generateControlNumber($row->request_type_id);

                if($executeInsert) {
                    $message  = 'Applying insert to local db - New CTRL #: ' . $arrayRow["control_number"];
                    $this->info($message);
                    Log::info($message);
                    DB::table(env('SYNC_TARGET_TABLE'))->insert($arrayRow);
                }
            }
        }

        $this->info('Sync process completed.');
    }
}

