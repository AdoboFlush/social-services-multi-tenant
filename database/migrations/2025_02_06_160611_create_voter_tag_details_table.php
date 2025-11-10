<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoterTagDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voter_tag_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("full_name")->nullable();
            $table->string("brgy")->nullable();
            $table->string("address")->nullable();
            $table->string("birth_date")->nullable();
            $table->string("gender")->nullable();
            $table->string("precinct")->nullable();
            $table->string("affiliation")->nullable();
            $table->string("alliance")->nullable();
            $table->string("contact_number")->nullable();
            $table->string("religion")->nullable();
            $table->string("civil_status")->nullable();
            $table->bigInteger("last_update_by");
            $table->bigInteger("voter_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voter_tag_details');
    }
}
