<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToSocialServiceAssistancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_service_assistances', function (Blueprint $table) {
            $table->tinyInteger('requestor_same_to_beneficiary')->default(0);
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
            //
            $table->dropColumn('requestor_same_to_beneficiary');
        });
    }
}

