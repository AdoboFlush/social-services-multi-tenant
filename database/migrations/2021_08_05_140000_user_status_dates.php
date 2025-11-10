<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserStatusDates extends Migration
{
    public function up()
    {
        Schema::table('user_informations', function (Blueprint $table) {
            $table->dateTime('account_verified_at')->nullable();
            $table->dateTime('account_closed_at')->nullable();
            $table->dateTime('account_suspended_at')->nullable();
            $table->dateTime('account_declared_dormant_at')->nullable();
            $table->dateTime('last_login_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('user_informations', function (Blueprint $table) {
            $table->dropColumn('account_verified_at');
            $table->dropColumn('account_closed_at');
            $table->dropColumn('account_suspended_at');
            $table->dropColumn('account_declared_dormant_at');
            $table->dropColumn('last_login_at');
        });

    }
}
