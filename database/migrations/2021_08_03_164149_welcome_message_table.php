<?php

use App\WelcomeMessage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WelcomeMessageTable extends Migration
{
    public function up()
    {
        Schema::create('welcome_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('content');
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists(('welcome_messages'));
    }
}
