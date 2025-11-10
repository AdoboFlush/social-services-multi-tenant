<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToSocialServiceAssistancesTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_service_assistances', function (Blueprint $table) {
            //
            $table->date('approved_date')->nullable(true);
            $table->date('received_date')->nullable(true);
            $table->string('precinct')->nullable(true);
            $table->boolean('releaser_id')->default(0);
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
            $table->dropColumn('approve_date');
            $table->dropColumn('received_date');
            $table->dropColumn('precinct');
            $table->dropColumn('releaser_id');        
        });
    }
}
