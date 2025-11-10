<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->integer('request_type_id')->after('venue')->nullable();
            $table->text('purpose')->after('request_type_id')->nullable();
            $table->float('amount')->after('purpose')->nullable();
            $table->string('color')->after('amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('request_type_id');
            $table->dropColumn('purpose');
            $table->dropColumn('amount');
            $table->dropColumn('color');
        });
    }
}
