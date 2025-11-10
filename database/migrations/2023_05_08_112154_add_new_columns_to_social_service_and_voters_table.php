<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToSocialServiceAndVotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('social_service_assistances', function (Blueprint $table) {
            $table->string("id_number")->nullable();
            $table->string("id_type")->nullable();
            $table->string("civil_status")->nullable();
        });

        Schema::table('voters', function (Blueprint $table) {
            $table->string("id_type")->nullable();
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
            $table->dropColumn("id_number");
            $table->dropColumn("id_type");
            $table->dropColumn("civil_status");
        });

        Schema::table('voters', function (Blueprint $table) {
            $table->dropColumn("id_type");
        });
    }
}
