<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDownloadStatsToIdRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('id_requests', function (Blueprint $table) {
            $table->dateTime("last_downloaded_at")->nullable;
            $table->bigInteger("downloaded_by")->nullable();
            $table->integer("download_count")->default(0);
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
            $table->dropColumn("last_downloaded_at");
            $table->dropColumn("downloaded_by");
            $table->dropColumn("download_count");
        });
    }
}
