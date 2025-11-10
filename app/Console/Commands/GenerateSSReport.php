<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use App\SocialServiceAssistance;

class GenerateSSReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'generate:ss-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Social Services Report';

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
        SocialServiceAssistance::orderBy('brgy', 'asc')->get()->map(function($data){
			echo "\n RECORD ".$data->full_name;
			$append_string = $data->is_voter ? "voters" : "non-voters";
			$file = '/var/www/html/aksyon-agad/public/csv/'.strtolower(str_replace(" ","-",$data->brgy)).'-'.$append_string.'.csv';
			if(!file_exists($file)){
				$headers = "Control Number,Beneficiary Name,Requestor Name,Barangay,Address,Request Type,Purpose,Date Created,Date Filed,Date Processed,Date Released,Status,Amount\r\n";
				file_put_contents($file, $headers, FILE_APPEND);
			}
			$data_arr = [
				$data->control_number,
				$data->full_name,
				$data->requestor_full_name,
				$data->brgy,
				$data->address,
				$data->request_type,
				str_replace(",","|",$data->purpose_text),
				$data->created_at,
				$data->file_date,
				$data->processed_date,
				$data->release_date,
				$data->status,
				$data->amount,
			];
			$log = implode(",", $data_arr)."\r\n";
			file_put_contents($file, $log, FILE_APPEND);
		});
    }
}