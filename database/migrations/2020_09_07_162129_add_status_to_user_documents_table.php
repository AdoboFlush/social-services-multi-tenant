<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToUserDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_documents', function (Blueprint $table) {
            //
            $table->string('status')->after('user_id')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('kyc_status')->after('account_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_documents', function (Blueprint $table) {
            //
            $table->dropColumn('status');
        });
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('kyc_status');
        });
    }
}
