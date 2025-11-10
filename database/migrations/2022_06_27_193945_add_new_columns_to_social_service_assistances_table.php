<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToSocialServiceAssistancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_service_assistances', function (Blueprint $table) {
            $table->string('requestor_first_name');
            $table->string('requestor_last_name');
            $table->string('requestor_middle_name');
            $table->string('requestor_suffix');
            $table->unique(['control_number']);
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
            $table->dropColumn('requestor_first_name');
            $table->dropColumn('requestor_last_name');
            $table->dropColumn('requestor_middle_name');
            $table->dropColumn('requestor_suffix');
            $table->dropUnique(['control_number']);
        });
    }
}
