<?php

use Illuminate\Database\Seeder;
use App\Tag;


class TenantTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param array $parameters Command line parameters
     * @return void
     */
    public function run()
    {
        // Purposes
        Tag::updateOrCreate(['name' => 'MEDICAL ASSISTANCE', 'parent_id' => 0], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'MEDICAL EQUIPMENT', 'parent_id' => 0], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'SOLICITATION', 'parent_id' => 0], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'BURIAL', 'parent_id' => 0], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);

        Tag::updateOrCreate(['name' => 'HOSPITAL BILL', 'parent_id' => 1], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'MEDICINE', 'parent_id' => 1], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'OPERATION / SURGERY', 'parent_id' => 1], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'MEDICINE / LABORATORY', 'parent_id' => 1], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'DENTAL', 'parent_id' => 1], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);

        Tag::updateOrCreate(['name' => 'WHEELCHAIR', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'CRUTCH', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'CANE', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'WALKER', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'NEBULIZER', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'THERMOMETER', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'BLOOD PRESSURE MONITOR', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'BLOOD SUGAR APPARATUS', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'MEDKITS', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'ASCORBIC ACID / VIT C', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'MULTIVITAMINS', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'AMLODIPHINE', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'METFORMIN', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'LOSARTAN', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'ATORVASTATIN', 'parent_id' => 2], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
     
        Tag::updateOrCreate(['name' => 'FINANCIAL', 'parent_id' => 3], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'SPORTS', 'parent_id' => 3], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'TEAM BUILDING', 'parent_id' => 3], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'FESTIVAL', 'parent_id' => 3], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'EDUCATIONAL', 'parent_id' => 3], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'SCHOOL GRADUATION', 'parent_id' => 3], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);

        Tag::updateOrCreate(['name' => 'FINANCIAL', 'parent_id' => 4], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'FLOWERS', 'parent_id' => 4], ['status'=>1, 'type'=>'purpose', 'custom_field' => '']);
        
        // Civil Status
        Tag::updateOrCreate(['name' => 'SINGLE', 'parent_id' => 0], ['status'=>1, 'type'=>'civil_status', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'MARRIED', 'parent_id' => 0], ['status'=>1, 'type'=>'civil_status', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'WINDOW', 'parent_id' => 0], ['status'=>1, 'type'=>'civil_status', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'WIDOWER', 'parent_id' => 0], ['status'=>1, 'type'=>'civil_status', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'SEPARATED', 'parent_id' => 0], ['status'=>1, 'type'=>'civil_status', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'ANNULED', 'parent_id' => 0], ['status'=>1, 'type'=>'civil_status', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'DIVORCED', 'parent_id' => 0], ['status'=>1, 'type'=>'civil_status', 'custom_field' => '']);

        // Religion
        Tag::updateOrCreate(['name' => 'ROMAN CATHOLIC', 'parent_id' => 0], ['status'=>1, 'type'=>'religion', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'MUSLIM/ISLAM', 'parent_id' => 0], ['status'=>1, 'type'=>'religion', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'BORN AGAIN CHRISTIAN', 'parent_id' => 0], ['status'=>1, 'type'=>'religion', 'custom_field' => '']);

        // Alliance (to Add)

        // Beneficiaries
        Tag::updateOrCreate(['name' => 'DSWD', 'parent_id' => 0], ['status'=>1, 'type'=>'beneficiaries', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => 'TUPD', 'parent_id' => 0], ['status'=>1, 'type'=>'beneficiaries', 'custom_field' => '']);
        Tag::updateOrCreate(['name' => "4P'S", 'parent_id' => 0], ['status'=>1, 'type'=>'beneficiaries', 'custom_field' => '']);

    }
}