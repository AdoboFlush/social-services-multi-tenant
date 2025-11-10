<?php

use App\KycStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KycStatusLookup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kyc_statuses', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });

        DB::update("ALTER TABLE kyc_statuses AUTO_INCREMENT = 1000;");

        $kyc_statuses = collect([
            KycStatus::UNREVIEWED,
            KycStatus::VERIFIED,
            KycStatus::W_CHECK,
            KycStatus::WC_PENDING,
            KycStatus::PENDING,
            KycStatus::CARD_APPROVED,
            KycStatus::CARD_REJECTED,
            KycStatus::CARD_PENDING,
            KycStatus::CHECK_A,
            KycStatus::CHECK_B,
            KycStatus::CHECK_C,
            KycStatus::CHECK_D,
            KycStatus::CHECK_E,
        ]);

        $kyc_statuses->each(function (string $status) {
            KycStatus::create([ 'name' => $status ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kyc_statuses');
    }
}
