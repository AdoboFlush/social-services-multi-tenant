<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("name");
            $table->text("description")->nullable();
            $table->string("hosted_by")->nullable();
            $table->integer("minimum_attendees")->nullable();
            $table->integer("maximum_attendees")->nullable();
            $table->dateTime("start_at")->nullable();
            $table->dateTime("end_at")->nullable();
            $table->text("venue")->nullable();
            $table->boolean("active")->default(true);
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
        Schema::dropIfExists('events');
    }
}
