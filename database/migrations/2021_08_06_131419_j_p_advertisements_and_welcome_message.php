<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JPAdvertisementsAndWelcomeMessage extends Migration
{
    public function up()
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('language')->default('English');
        });

        Schema::table('welcome_messages', function (Blueprint $table) {
            $table->longText('jp_content')->nullable();
        });

    }

    public function down()
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn('language');
        });

        Schema::table('welcome_messages', function (Blueprint $table) {
            $table->dropColumn('jp_content');
        });
    }
}
