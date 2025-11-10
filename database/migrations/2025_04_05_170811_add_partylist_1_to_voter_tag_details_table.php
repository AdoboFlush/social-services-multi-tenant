<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartylist1ToVoterTagDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voter_tag_details', function (Blueprint $table) {
            $table->string('party_list_1')->nullable()->after('party_list');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voter_tag_details', function (Blueprint $table) {
            $table->dropColumn('party_list_1');
        });
    }
}
