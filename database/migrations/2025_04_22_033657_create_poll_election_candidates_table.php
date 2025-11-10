<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollElectionCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_election_candidates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('poll_candidate_id')->unsigned();
            $table->string('position')->nullable();
            $table->string('party')->nullable();
            $table->bigInteger('poll_election_id')->unsigned();
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
        Schema::dropIfExists('poll_election_candidates');
    }
}
