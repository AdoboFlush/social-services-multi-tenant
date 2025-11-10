<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialServiceAssistancesTable extends Migration
{

    protected $fillable = [
        'control_number',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'contact_number',
        'brgy',
        'address',
        'organization',
        'purpose',
        'remarks',
        'referred_by',
        'received_by',
        'file_date',
        'processed_date',
        'release_date',
        'amount',
        'approved_by',
        'status'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_service_assistances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('control_number');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('suffix');
            $table->string('contact_number');
            $table->string('brgy');
            $table->string('organization');
            $table->string('address');
            $table->string('purpose');
            $table->text('remarks');
            $table->string('referred_by');
            $table->string('received_by');
            $table->date('file_date');
            $table->date('processed_date');
            $table->date('release_date');
            $table->float('amount');
            $table->tinyInteger('approved_by')->default(0);
            $table->string('status');
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
        Schema::dropIfExists('social_service_assistance');
    }
}
