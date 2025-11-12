<?php

use Illuminate\Database\Seeder;
use App\Tag;

class TagAdditionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {    
        /*
        // Alliance
        Tag::firstOrCreate(['status'=>1, 'name' => 'ARJO ATAYDE', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'BIG FAMILY', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => Tag::where('name', 'ARJO ATAYDE')->where('type', 'alliance')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'LEADER', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => Tag::where('name', 'ARJO ATAYDE')->where('type', 'alliance')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'SUPPORTER', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => Tag::where('name', 'ARJO ATAYDE')->where('type', 'alliance')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'COORDINATOR', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => Tag::where('name', 'ARJO ATAYDE')->where('type', 'alliance')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'CRISOLOGO', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'BIG FAMILY', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => Tag::where('name', 'CRISOLOGO')->where('type', 'alliance')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'LEADER', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => Tag::where('name', 'CRISOLOGO')->where('type', 'alliance')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'SUPPORTER', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => Tag::where('name', 'CRISOLOGO')->where('type', 'alliance')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'COORDINATOR', 'type'=>'alliance', 'custom_field' => '', 'parent_id' => Tag::where('name', 'CRISOLOGO')->where('type', 'alliance')->first()->id]);

        // Alliance 2
        Tag::firstOrCreate(['status'=>1, 'name' => 'GAB ATAYDE', 'type'=>'alliance_1', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'GARANTISADONG - LEADER', 'type'=>'alliance_1', 'custom_field' => '', 'parent_id' => Tag::where('name', 'GAB ATAYDE')->where('type', 'alliance_1')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'GARANTISADONG - MEMBER', 'type'=>'alliance_1', 'custom_field' => '', 'parent_id' => Tag::where('name', 'GAB ATAYDE')->where('type', 'alliance_1')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'GAB FOR ALL - LEADER', 'type'=>'alliance_1', 'custom_field' => '', 'parent_id' => Tag::where('name', 'GAB ATAYDE')->where('type', 'alliance_1')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'GAB FOR ALL - MEMBER', 'type'=>'alliance_1', 'custom_field' => '', 'parent_id' => Tag::where('name', 'GAB ATAYDE')->where('type', 'alliance_1')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'GAB SUPPORTER ONLY', 'type'=>'alliance_1', 'custom_field' => '', 'parent_id' => Tag::where('name', 'GAB ATAYDE')->where('type', 'alliance_1')->first()->id]);

        // Affiliation
        Tag::firstOrCreate(['status'=>1, 'name' => 'DORAY', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'CHARM', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'SEP', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'TJ', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'BERNARD', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'AKSYONERA', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'AKSYONERA - LEADER', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => Tag::where('name', 'AKSYONERA')->where('type', 'affiliation')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'AKSYONERA - MEMBER', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => Tag::where('name', 'AKSYONERA')->where('type', 'affiliation')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'AADU', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'AADU - LEADER', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => Tag::where('name', 'AADU')->where('type', 'affiliation')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'AADU - MEMBER', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => Tag::where('name', 'AADU')->where('type', 'affiliation')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'CATM', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'CATM - LEADER', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => Tag::where('name', 'CATM')->where('type', 'affiliation')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'CATM - MEMBER', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => Tag::where('name', 'CATM')->where('type', 'affiliation')->first()->id]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'BLUE LEADERS', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'BB', 'type'=>'affiliation', 'custom_field' => '', 'parent_id' => 0]);
   
        // Sectoral
        Tag::firstOrCreate(['status'=>1, 'name' => 'TRISKELION', 'type'=>'sectoral', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'SENIOR', 'type'=>'sectoral', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'PWD', 'type'=>'sectoral', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'LGBT', 'type'=>'sectoral', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'TODA', 'type'=>'sectoral', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'SOLO PARENT', 'type'=>'sectoral', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'PTA', 'type'=>'sectoral', 'custom_field' => '', 'parent_id' => 0]);
        Tag::firstOrCreate(['status'=>1, 'name' => 'APO', 'type'=>'sectoral', 'custom_field' => '', 'parent_id' => 0]);
        */
    }
}