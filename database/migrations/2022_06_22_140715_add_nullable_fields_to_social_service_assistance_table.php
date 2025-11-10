<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullableFieldsToSocialServiceAssistanceTable extends Migration
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
            $table->string('contact_number')->unsigned()->nullable()->change();
            $table->text('remarks')->unsigned()->nullable()->change();
            $table->string('referred_by')->unsigned()->nullable()->change();
            $table->string('received_by')->unsigned()->nullable()->change();
            $table->date('file_date')->unsigned()->nullable()->change();
            $table->date('processed_date')->unsigned()->nullable()->change();
            $table->date('release_date')->unsigned()->nullable()->change();
            $table->string('approved_by')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_service_assistance', function (Blueprint $table) {
            //
        });
    }
}
