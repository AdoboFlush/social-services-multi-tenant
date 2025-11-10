<?php

namespace App\Console\Commands;

use App\Services\Voter\VoterFacade;
use App\Voter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class UpsertVoterTagDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'upsert:voter-tag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update or insert voter tag details';

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
        VoterFacade::upsertVoterTagDetails("data/voter_tagging", true);
    }
}

