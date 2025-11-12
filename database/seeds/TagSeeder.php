<?php

use Illuminate\Database\Seeder;
use App\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Purposes
        Tag::create(['status'=>1, 'name' => 'MEDICAL ASSISTANCE', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'MEDICAL EQUIPMENT', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'SOLICITATION', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'BURIAL', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 0]);

        Tag::create(['status'=>1, 'name' => 'HOSPITAL BILL', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 1]);
        Tag::create(['status'=>1, 'name' => 'MEDICINE', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 1]);
        Tag::create(['status'=>1, 'name' => 'OPERATION / SURGERY', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 1]);
        Tag::create(['status'=>1, 'name' => 'MEDICINE / LABORATORY', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 1]);
        Tag::create(['status'=>1, 'name' => 'DENTAL', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 1]);

        Tag::create(['status'=>1, 'name' => 'WHEELCHAIR', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'CRUTCH', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'CANE', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'WALKER', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'NEBULIZER', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'THERMOMETER', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'BLOOD PRESSURE MONITOR', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'BLOOD SUGAR APPARATUS', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'MEDKITS', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'ASCORBIC ACID / VIT C', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'MULTIVITAMINS', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'AMLODIPHINE', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'METFORMIN', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'LOSARTAN', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
        Tag::create(['status'=>1, 'name' => 'ATORVASTATIN', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 2]);
     
        Tag::create(['status'=>1, 'name' => 'FINANCIAL', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 3]);
        Tag::create(['status'=>1, 'name' => 'SPORTS', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 3]);
        Tag::create(['status'=>1, 'name' => 'TEAM BUILDING', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 3]);
        Tag::create(['status'=>1, 'name' => 'FESTIVAL', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 3]);
        Tag::create(['status'=>1, 'name' => 'EDUCATIONAL', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 3]);
        Tag::create(['status'=>1, 'name' => 'SCHOOL GRADUATION', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 3]);

        Tag::create(['status'=>1, 'name' => 'FINANCIAL', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 4]);
        Tag::create(['status'=>1, 'name' => 'FLOWERS', 'type'=>'purpose', 'custom_field' => '', 'parent_id' => 4]);
        
        // Civil Status
        Tag::create(['status'=>1, 'name' => 'SINGLE', 'type'=>'civil_status', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'MARRIED', 'type'=>'civil_status', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'WINDOW', 'type'=>'civil_status', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'WIDOWER', 'type'=>'civil_status', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'SEPARATED', 'type'=>'civil_status', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'ANNULED', 'type'=>'civil_status', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'DIVORCED', 'type'=>'civil_status', 'custom_field' => '', 'parent_id' => 0]);

        // Religion
        Tag::create(['status'=>1, 'name' => 'ROMAN CATHOLIC', 'type'=>'religion', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'MUSLIM/ISLAM', 'type'=>'religion', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'BORN AGAIN CHRISTIAN', 'type'=>'religion', 'custom_field' => '', 'parent_id' => 0]);

        // Alliance
        // Tag::create(['status'=>1, 'name' => 'ATAYDE', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => 0]);
        // Tag::create(['status'=>1, 'name' => 'CRISOLOGO', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => 0]);

        // Beneficiaries
        Tag::create(['status'=>1, 'name' => 'DSWD', 'type'=>'beneficiaries', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'TUPD', 'type'=>'beneficiaries', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => "4P'S", 'type'=>'beneficiaries', 'custom_field' => '', 'parent_id' => 0]);

    }
}