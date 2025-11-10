<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WebsiteUrl extends Migration
{
    public function up()
    {
        Schema::table('user_informations', function (Blueprint $table) {
            $table->string('website_url')->nullable();
        });
    }

    public function down()
    {
        Schema::table('user_informations', function (Blueprint $table) {
            $table->dropColumn('website_url');
        });
    }
}
