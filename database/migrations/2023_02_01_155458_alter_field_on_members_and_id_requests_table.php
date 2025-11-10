<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFieldOnMembersAndIdRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->integer('parent_id')->default(0)->change();
            $table->integer('created_by')->nullable()->change();
        });

        Schema::table('id_requests', function (Blueprint $table) {
            $table->integer('member_id')->default(0)->change();
            $table->integer('template_id')->default(0)->change();
            $table->integer('created_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
