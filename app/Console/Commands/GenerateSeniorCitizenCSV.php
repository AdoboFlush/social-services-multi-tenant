<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class GenerateSeniorCitizenCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'generate:senior-citizen-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Senior Citizen CSV';

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
        app('App\Http\Controllers\VoterController')->generateSeniorCitizenCSV();
    }
}

