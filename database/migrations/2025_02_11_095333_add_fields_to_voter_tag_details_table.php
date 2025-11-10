<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToVoterTagDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voter_tag_details', function (Blueprint $table) {
            $table->string("alliance_subgroup")->nullable();
            $table->string("affiliation_subgroup")->nullable();
            $table->string("alliance_1")->nullable();
            $table->string("alliance_1_subgroup")->nullable();
            $table->string("affiliation_1")->nullable();
            $table->string("affiliation_1_subgroup")->nullable();
            $table->string("sectoral")->nullable();
            $table->string("sectoral_1")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voter_tag_details', function (Blueprint $table) {
            $table->dropColumn([
                'alliance_subgroup',
                'alliance_1',
                'alliance_1_subgroup',
                'affiliation_subgroup',
                'affiliation_1',
                'affiliation_1_subgroup',
                'sectoral',
                'sectoral_1',
            ]);
        });
    }
}
