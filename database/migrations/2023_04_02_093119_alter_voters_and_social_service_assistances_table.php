<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVotersAndSocialServiceAssistancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_service_assistances', function (Blueprint $table) {
            $table->boolean('is_deceased')->default(false);
            $table->date('birth_date')->nullable();
        });

        Schema::table('voters', function (Blueprint $table) {
            $table->integer('is_deceased')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_service_assistances', function (Blueprint $table) {
            $table->dropColumn('is_deceased');
            $table->dropColumn('birth_date');
        });

        Schema::table('voters', function (Blueprint $table) {
            $table->dropColumn('is_deceased');
        });
    }
}
