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
        

        // Barangay
        Tag::create(['status'=>1, 'name' => 'RAMON MAGSAYSAY', 'type'=>'brgy', 'custom_field' => 'AREA 1', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'ALICIA', 'type'=>'brgy', 'custom_field' => 'AREA 1', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'STO. CRISTO', 'type'=>'brgy', 'custom_field' => 'AREA 1', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'BAGONG PAG-ASA', 'type'=>'brgy', 'custom_field' => 'AREA 1', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'PROJECT 6', 'type'=>'brgy', 'custom_field' => 'AREA 1', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'VASRA', 'type'=>'brgy', 'custom_field' => 'AREA 1', 'parent_id' => 0]);
        
        Tag::create(['status'=>1, 'name' => 'BAHAY TORO', 'type'=>'brgy', 'custom_field' => 'AREA 2', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'KATIPUNAN', 'type'=>'brgy', 'custom_field' => 'AREA 2', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'SAN ANTONIO', 'type'=>'brgy', 'custom_field' => 'AREA 2', 'parent_id' => 0]);

        Tag::create(['status'=>1, 'name' => 'VETERANS VILLAGE', 'type'=>'brgy', 'custom_field' => 'AREA 3', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'BUNGAD', 'type'=>'brgy', 'custom_field' => 'AREA 3', 'parent_id' => 0]);

        Tag::create(['status'=>1, 'name' => 'PHIL-AM', 'type'=>'brgy', 'custom_field' => 'AREA 3', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'WEST TRIANGLE', 'type'=>'brgy', 'custom_field' => 'AREA 3', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'PALTOK', 'type'=>'brgy', 'custom_field' => 'AREA 3', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'NAYONG KANLURAN', 'type'=>'brgy', 'custom_field' => 'AREA 3', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'STA. CRUZ', 'type'=>'brgy', 'custom_field' => 'AREA 3', 'parent_id' => 0]);

        Tag::create(['status'=>1, 'name' => 'MASAMBONG', 'type'=>'brgy', 'custom_field' => 'AREA 4', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'DEL MONTE', 'type'=>'brgy', 'custom_field' => 'AREA 4', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'DAMAYAN', 'type'=>'brgy', 'custom_field' => 'AREA 4', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'PARAISO', 'type'=>'brgy', 'custom_field' => 'AREA 4', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'TALAYAN', 'type'=>'brgy', 'custom_field' => 'AREA 4', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'MARIBLO', 'type'=>'brgy', 'custom_field' => 'AREA 4', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'STO. DOMINGO', 'type'=>'brgy', 'custom_field' => 'AREA 4', 'parent_id' => 0]);
        

        Tag::create(['status'=>1, 'name' => 'BALINGASA', 'type'=>'brgy', 'custom_field' => 'AREA 5', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'PAG-IBIG SA NAYON', 'type'=>'brgy', 'custom_field' => 'AREA 5', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'DAMAR', 'type'=>'brgy', 'custom_field' => 'AREA 5', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'SAN JOSE', 'type'=>'brgy', 'custom_field' => 'AREA 5', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'MANRESA', 'type'=>'brgy', 'custom_field' => 'AREA 5', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'ST. PETER', 'type'=>'brgy', 'custom_field' => 'AREA 5', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'SIENA', 'type'=>'brgy', 'custom_field' => 'AREA 5', 'parent_id' => 0]);

        Tag::create(['status'=>1, 'name' => 'LOURDES', 'type'=>'brgy', 'custom_field' => 'AREA 6', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'STA. TERESITA', 'type'=>'brgy', 'custom_field' => 'AREA 6', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'SAN ISIDRO LABRADOR', 'type'=>'brgy', 'custom_field' => 'AREA 6', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'SALVACION', 'type'=>'brgy', 'custom_field' => 'AREA 6', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'PAANG BUNDOK', 'type'=>'brgy', 'custom_field' => 'AREA 6', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'N.S. AMORANTO', 'type'=>'brgy', 'custom_field' => 'AREA 6', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'MAHARLIKA', 'type'=>'brgy', 'custom_field' => 'AREA 6', 'parent_id' => 0]);

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
        Tag::create(['status'=>1, 'name' => 'ATAYDE', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'CRISOLOGO', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => 0]);

        // Beneficiaries
        Tag::create(['status'=>1, 'name' => 'DSWD', 'type'=>'beneficiaries', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => 'TUPD', 'type'=>'beneficiaries', 'custom_field' => '', 'parent_id' => 0]);
        Tag::create(['status'=>1, 'name' => "4P'S", 'type'=>'beneficiaries', 'custom_field' => '', 'parent_id' => 0]);

    }
}