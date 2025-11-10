<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use App\Voter;

class GenerateVoterReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'generate:voter-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Voter Report';

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
		Voter::chunk(5000, function ($voters) {
			foreach($voters as $data) {
				echo "\n processing : ".$data->full_name;
				$aff = $data->affiliation;
				if(!empty($aff)) {
					$file = '/var/www/html/aksyon-agad/public/csv1/affiliated-voters-'.$aff.'.csv';
					if(!file_exists($file)){
						$headers = "FullName,Brgy,Address,Gender,Affiliation,Alliance,Precinct,BirthDate\r\n";
						file_put_contents($file, $headers, FILE_APPEND);
					}
					$data_arr = [
						'"'.$data->full_name.'"',
						'"'.$data->brgy.'"',
						'"'.$data->address.'"',
						'"'.$data->gender.'"',
						'"'.$data->affiliation.'"',
						'"'.$data->alliance.'"',
						'"'.$data->precinct.'"',
						'"'.$data->birth_date.'"',
					];
					$log = implode(",", $data_arr)."\r\n";
					file_put_contents($file, $log, FILE_APPEND);
				}
			}
			
		});
    }
}