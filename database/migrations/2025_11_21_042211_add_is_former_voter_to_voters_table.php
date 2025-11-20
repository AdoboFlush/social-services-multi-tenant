<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFormerVoterToVotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->boolean('is_former_voter')->default(false);
        });

        Schema::table('voter_tag_details', function (Blueprint $table) {
            $table->boolean('is_former_voter')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->dropColumn('is_former_voter');
        });

        Schema::table('voter_tag_details', function (Blueprint $table) {
            $table->dropColumn('is_former_voter');
        });
    }
}
