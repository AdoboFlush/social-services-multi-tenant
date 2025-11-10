<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CheckMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'check:maintenance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the users if they are paid on the maintenance fee';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(App::environment('staging')) {
            $this->signature = 'check:maintenance {user_id=0}';
        }
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(App::environment('staging')) {
            $user_id = !empty($this->argument('user_id')) ? $this->argument('user_id') : 0;
            app('App\Http\Controllers\API\v1\AccountController')->maintenance($user_id);
        }else{
            app('App\Http\Controllers\API\v1\AccountController')->maintenance();
        }
    }
}

