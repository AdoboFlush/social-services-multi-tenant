<?php

use App\WelcomeMessage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WelcomeMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content = '<p><b>Dev Note [2022-06-29]</b></p><ul><li>Added Beneficiary Field to Voters</li><li>Added this welcome message functionality</li><li>Add new validations on Social Service Assistance Module</li></ul>';
        WelcomeMessage::create([
            'content' => $content
        ]);

    }
}