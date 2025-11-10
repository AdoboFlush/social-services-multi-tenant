<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToIdRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('id_requests', function (Blueprint $table) {
            $table->string('id_number')->nullable(true);
        });
        Schema::table('members', function (Blueprint $table) {
            $table->unique(['first_name','last_name', 'middle_name', 'birth_date', 'gender', 'precinct'], 'member_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('id_requests', function (Blueprint $table) {
            $table->dropColumn('id_number');
            $table->dropUnique('member_unique');
        });
    }
}
