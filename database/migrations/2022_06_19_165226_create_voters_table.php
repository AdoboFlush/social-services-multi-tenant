<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotersTable extends Migration
{

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'brgy',
        'address',
        'birth_date',
        'gender',
        'precinct',
        'alliance',
        'affiliation',
        'civil_status',
        'religion',
        'contact_number',
        'remarks'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('suffix');
            $table->string('brgy');
            $table->string('address');
            $table->date('birth_date');
            $table->string('gender');
            $table->string('precinct');
            $table->string('alliance');
            $table->string('affiliation');
            $table->string('civil_status');
            $table->string('religion');
            $table->string('contact_number');
            $table->text('remarks');
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
        Schema::dropIfExists('voters');
    }
}
