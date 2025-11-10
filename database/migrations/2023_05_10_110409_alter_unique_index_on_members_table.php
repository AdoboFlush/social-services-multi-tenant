<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUniqueIndexOnMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique('member_unique');
            $table->string("gender", 10)->nullable()->change();
            $table->string("precinct", 90)->nullable()->change();
            $table->unique(['first_name','last_name','middle_name','suffix','birth_date','gender','precinct'], 'member_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique('member_unique');
            $table->string("gender")->nullable()->change();
            $table->string("precinct")->nullable()->change();
            $table->unique(['first_name','last_name','middle_name','birth_date','gender','precinct'], 'member_unique');
        });
    }
}
