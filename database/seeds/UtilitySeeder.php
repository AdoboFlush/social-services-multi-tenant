<?php

use Illuminate\Database\Seeder;

class UtilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		//Default Settings
		DB::table('settings')->insert([
			[
			  'name' => 'mail_type',
			  'value' => 'mail'
			],
			[
			  'name' => 'timezone',
			  'value' => 'Asia/Manila'
			],
			[
				'name' => 'isMaintenance',
				'value' => ''
			]
		]);
		
    }
}
