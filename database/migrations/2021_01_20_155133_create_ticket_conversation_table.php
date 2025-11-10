<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketConversationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_conversations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ticket_id');
            $table->integer('sender_id');
            $table->string('department');
            $table->longText('message');
            $table->text('attachment');
            $table->text('language');
            $table->integer('is_seen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_conversations', function (Blueprint $table) {
            //
            Schema::dropIfExists('ticket_conversations');
        });
    }
}
