<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueColumnsToSocialServiceAssistancesTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_service_assistances', function (Blueprint $table) {
            $table->dropUnique('control_numnber_brgy');
            $table->unique(['control_number', 'brgy', 'request_type_id'], 'control_number_brgy_request_type');
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
            $table->unique(['control_number', 'brgy'], 'control_numnber_brgy');
            $table->dropUnique('control_number_brgy_request_type');
        });
    }
}
