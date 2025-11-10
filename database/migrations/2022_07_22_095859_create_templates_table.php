<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable(true);
            $table->text('remarks')->nullable(true);
            $table->text('properties_json')->nullable(true);
            $table->timestamps();
        });

        Schema::table('id_requests', function (Blueprint $table) {
            $table->string('name_on_id')->nullable(true)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('templates');
        Schema::table('id_requests', function (Blueprint $table) {
            $table->dropColumn('name_on_id');
        });
    }
}
