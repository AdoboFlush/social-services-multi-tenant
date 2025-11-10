<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdRequestsAndMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_number');
            $table->string('first_name')->nullable(true);
            $table->string('last_name')->nullable(true);
            $table->string('middle_name')->nullable(true);
            $table->string('suffix')->nullable(true);
            $table->string('gender')->nullable(true);
            $table->text('address')->nullable(true);
            $table->string('brgy')->nullable(true);
            $table->string('area')->nullable(true);
            $table->string('precinct')->nullable(true);
            $table->string('affiliation')->nullable(true);
            $table->string('alliance')->nullable(true);
            $table->string('civil_status')->nullable(true);
            $table->string('religion')->nullable(true);
            $table->date('birth_date')->nullable(true);
            $table->boolean('is_voter')->default(0);
            $table->tinyInteger('parent_id')->default(0);
            $table->string('status')->nullable(true);
            $table->string('contact_number')->nullable(true);
            $table->string('contact_person_first_name')->nullable(true);
            $table->string('contact_person_last_name')->nullable(true);
            $table->string('contact_person_middle_name')->nullable(true);
            $table->string('contact_person_suffix')->nullable(true);
            $table->string('contact_person_number')->nullable(true);
            $table->text('contact_person_address')->nullable(true);
            $table->text('remarks')->nullable(true);
            $table->tinyInteger('created_by')->nullable(true);
            $table->timestamps();
        });

        Schema::create('id_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->nullable(true);
            $table->tinyInteger('member_id')->default(0);
            $table->tinyInteger('template_id')->default(0);
            $table->string('status')->nullable(true);
            $table->string('profile_pic')->nullable(true);
            $table->string('signature')->nullable(true);
            $table->text('remarks')->nullable(true);
            $table->tinyInteger('created_by')->nullable(true);
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
        Schema::dropIfExists('members');
        Schema::dropIfExists('id_requests');
    }
}
