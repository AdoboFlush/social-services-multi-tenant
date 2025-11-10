<?php

use App\KycStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAdditionalKycStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("ALTER TABLE kyc_statuses AUTO_INCREMENT = 1013;");
        Schema::table('kyc_statuses', function (Blueprint $table) {
            $kyc_statuses = collect([
                KycStatus::CHECK_F,
                KycStatus::CHECK_G,
                KycStatus::CHECK_H,
                KycStatus::CHECK_I,
                KycStatus::CHECK_J,
            ]);
    
            $kyc_statuses->each(function (string $status) {
                KycStatus::create([ 'name' => $status ]);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kyc_statuses', function (Blueprint $table) {
            KycStatus::where('id', '>', 1012)->delete();
        });
    }
}
