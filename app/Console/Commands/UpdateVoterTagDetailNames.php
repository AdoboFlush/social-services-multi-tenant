<?php

namespace App\Console\Commands;

use App\Services\Voter\VoterFacade;
use App\Voter;
use App\VoterTagDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class UpdateVoterTagDetailNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'update:voter-tag-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update names of all voters';

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
        VoterTagDetail::chunk(100, function ($voterTagDetails) {
            foreach ($voterTagDetails as $voterTagDetail) {
                $name_parts = parse_name($voterTagDetail->full_name);
                if ($name_parts) {
                    $voterTagDetail->first_name = $name_parts['first_name'];
                    $voterTagDetail->last_name = $name_parts['last_name'];
                    $voterTagDetail->middle_name = $name_parts['middle_name'];
                    $voterTagDetail->suffix = $name_parts['suffix'];
                    $voterTagDetail->save();
                }
            }
        });
    }
}

