<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('currency')->insert([
            'name' => 'USD',
            'base_currency' => '1',
            'exchange_rate' => '1.10',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('currency')->insert([
            'name' => 'EUR',
            'base_currency' => '0',
            'exchange_rate' => '1.00',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('currency')->insert([
            'name' => 'GBP',
            'base_currency' => '0',
            'exchange_rate' => '0.89',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('currency')->insert([
            'name' => 'JPY',
            'base_currency' => '0',
            'exchange_rate' => '0.00',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('currency')->insert([
            'name' => 'PHP',
            'base_currency' => '0',
            'exchange_rate' => '0.00',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('currency')->insert([
            'name' => 'HKD',
            'base_currency' => '0',
            'exchange_rate' => '0.00',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('currency')->insert([
            'name' => 'IDR',
            'base_currency' => '0',
            'exchange_rate' => '0.00',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('currency')->insert([
            'name' => 'MYR',
            'base_currency' => '0',
            'exchange_rate' => '0.00',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('currency')->insert([
            'name' => 'THB',
            'base_currency' => '0',
            'exchange_rate' => '0.00',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('currency')->insert([
            'name' => 'VND',
            'base_currency' => '0',
            'exchange_rate' => '0.00',
            'status' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

    }
}
