<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("action")->nullable();
            $table->text("description")->nullable();
            $table->bigInteger("poll_entry_id")->nullable();
            $table->bigInteger("poll_watcher_id")->nullable();
            $table->bigInteger("poll_election_id")->nullable();
            $table->string("ip_address")->nullable();
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
        Schema::dropIfExists('poll_activity_logs');
    }
}
