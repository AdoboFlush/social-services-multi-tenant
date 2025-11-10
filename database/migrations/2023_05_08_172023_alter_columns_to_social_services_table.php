<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnsToSocialServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_service_assistances', function (Blueprint $table) {
            $table->bigInteger("encoder_id")->change();
            $table->bigInteger("releaser_id")->change();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->bigInteger("parent_id")->change();
            $table->bigInteger("created_by")->change();
        });

        Schema::table('id_requests', function (Blueprint $table) {
            $table->bigInteger("member_id")->change();
            $table->bigInteger("template_id")->change();
            $table->bigInteger("created_by")->change();
        });

        Schema::table('attendees', function (Blueprint $table) {
            $table->bigInteger("voter_id")->default(0);
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
            $table->tinyInteger("encoder_id")->change();
            $table->tinyInteger("releaser_id")->change();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->tinyInteger("parent_id")->change();
            $table->tinyInteger("created_by")->change();
        });

        Schema::table('id_requests', function (Blueprint $table) {
            $table->tinyInteger("member_id")->change();
            $table->tinyInteger("template_id")->change();
            $table->tinyInteger("created_by")->change();
        });

        Schema::table('attendees', function (Blueprint $table) {
            $table->dropColumn("voter_id");
        });

    }
}
