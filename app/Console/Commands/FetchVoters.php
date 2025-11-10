<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class FetchVoters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'fetch:voters {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch voters information';

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
        $path = $this->arguments('path');
        \App\Services\Voter\VoterFacade::fetchVotersV2($path);
		#app('App\Http\Controllers\VoterController')->fetch();
    }
}

