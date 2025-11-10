<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('poll_election_candidate_id');
            $table->bigInteger('poll_election_watcher_id');
            $table->bigInteger('poll_election_id');
            $table->integer('votes')->default(0);
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('poll_entries');
    }
}
