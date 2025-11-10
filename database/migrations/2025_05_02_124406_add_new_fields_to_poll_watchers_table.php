<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToPollWatchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poll_watchers', function (Blueprint $table) {
            $table->string('area')->nullable();
            $table->string('poll_place')->nullable();
            $table->string('clustered_precincts')->nullable();
            $table->integer('no_of_registered_voters')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poll_watchers', function (Blueprint $table) {
            $table->dropColumn('area');
            $table->dropColumn('poll_place');
            $table->dropColumn('clustered_precincts');
            $table->dropColumn('no_of_registered_voters');
        });
    }
}
