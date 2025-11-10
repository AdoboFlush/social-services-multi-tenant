<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSectoralFieldsInVotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->string("alliance_subgroup")->after('alliance')->default("");
            $table->string("alliance_1_subgroup")->after('alliance_1')->default("");
            $table->string("affiliation_subgroup")->after('affiliation')->default("");
            $table->string("affiliation_1_subgroup")->after('affiliation_1')->default("");
            $table->string("sectoral")->default("");
            $table->string("sectoral_1")->default("");
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
            $table->dropColumn("alliance_subgroup");
            $table->dropColumn("alliance_1_sugroup");
            $table->dropColumn("affiliation_subgroup");
            $table->dropColumn("affiliation_1_subgroup");
            $table->dropColumn("sectoral");
            $table->dropColumn("sectoral_1");
        });
    }
}
