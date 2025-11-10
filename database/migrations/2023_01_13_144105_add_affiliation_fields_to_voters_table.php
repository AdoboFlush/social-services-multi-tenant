<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAffiliationFieldsToVotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->string('affiliation_1')->nullable(true);
			$table->string('affiliation_2')->nullable(true);
			$table->string('affiliation_3')->nullable(true);
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
			$table->dropColumn('affiliation_1');
			$table->dropColumn('affiliation_2');
			$table->dropColumn('affiliation_3');
        });
    }
}
