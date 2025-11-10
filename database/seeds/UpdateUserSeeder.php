<?php
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;


class UpdateUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        User::where(['first_name' => 'Rizza', 'last_name' => 'Dela Cruz'])
                ->update(['email' => 'rizza.qcdistrictone@gmail.com']);

        User::where(['first_name' => 'Lovely Joy', 'last_name' => 'Mondragon'])
                ->update(['email' => 'lovely.qcdistrictone@gmail.com']);
        
        User::where(['first_name' => 'MAUI MAURENE', 'last_name' => 'CUDAL'])
                ->update(['email' => 'maui.qcdistrictone@gmail.com']);

        User::where(['first_name' => 'Rose Ann', 'last_name' => 'Lugtu'])
                ->update(['email' => 'rose.qcdistrictone@gmail.com']);

        User::where(['first_name' => 'ROWENA', 'last_name' => 'CAPISTRANO'])
                ->update(['email' => 'rowena.qcdistrictone@gmail.com']);

        User::where(['first_name' => 'Aimee', 'last_name' => 'Rendon'])
                ->update(['email' => 'aimee.qcdistrictone@gmail.com']);

        User::where(['first_name' => 'Elaizha', 'last_name' => 'Tee'])
                ->update(['email' => 'elaizha.qcdistrictone@gmail.com']);

        User::where(['first_name' => 'Renz Raul', 'last_name' => 'Sta.Maria'])
                ->update(['email' => 'renz.qcdistrictone@gmail.com']);

    }
}
