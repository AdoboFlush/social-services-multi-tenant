<?php
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;


class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create( [
          'account_number' => '0000',
          'first_name' => 'Default',
          'last_name' => 'Admin',
          'email' => 'app.admin@gmail.com',
          'password' => Hash::make('DeFaulT09876!@$#'),
          'status' => '1',
          'user_type' => 'admin',
          'phone' => '0000',
          'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user->assignRole('admin');

        $user = User::create( [
          'account_number' => '0001',
          'first_name' => 'Rizza',
          'last_name' => 'Dela Cruz',
          'email' => 'rizzadelacruz027@gmail.com',
          'password' => Hash::make('123456'),
          'status' => '1',
          'user_type' => 'admin',
          'phone' => '09972192813',
          'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user->assignRole('encoder');

        $user = User::create( [
          'account_number' => '0002',
          'first_name' => 'Lovely Joy',
          'last_name' => 'Mondragon',
          'email' => 'joyamondragon.jm@gmail.com',
          'password' => Hash::make('123456'),
          'status' => '1',
          'user_type' => 'admin',
          'phone' => '09952253361',
          'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user->assignRole('encoder');

        $user = User::create( [
          'account_number' => '0003',
          'first_name' => 'MAUI MAURENE',
          'last_name' => 'CUDAL',
          'email' => 'mauicudal07@gmail.com',
          'password' => Hash::make('123456'),
          'status' => '1',
          'user_type' => 'admin',
          'phone' => '09274739680',
          'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user->assignRole('encoder');

        $user = User::create( [
          'account_number' => '0004',
          'first_name' => 'Rose Ann',
          'last_name' => 'Lugtu',
          'email' => 'mroseannhernandezlugtu@gmail.com',
          'password' => Hash::make('123456'),
          'status' => '1',
          'user_type' => 'admin',
          'phone' => '09453342406',
          'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user->assignRole('encoder');

        $user = User::create( [
          'account_number' => '0005',
          'first_name' => 'ROWENA',
          'last_name' => 'CAPISTRANO',
          'email' => 'rowjul09@gmail.com',
          'password' => Hash::make('123456'),
          'status' => '1',
          'user_type' => 'admin',
          'phone' => '09677451333',
          'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user->assignRole('encoder');

        $user = User::create( [
          'account_number' => '0006',
          'first_name' => 'Aimee',
          'last_name' => 'Rendon',
          'email' => 'aimee030420@gmail.com',
          'password' => Hash::make('123456'),
          'status' => '1',
          'user_type' => 'admin',
          'phone' => '09958420315',
          'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user->assignRole('encoder');

        $user = User::create( [
          'account_number' => '0006',
          'first_name' => 'Elaizha',
          'last_name' => 'Tee',
          'email' => 'teeelaizha@gmail.com',
          'password' => Hash::make('123456'),
          'status' => '1',
          'user_type' => 'admin',
          'phone' => '09636325325',
          'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user->assignRole('encoder');

        $user = User::create( [
          'account_number' => '0006',
          'first_name' => 'Renz Raul',
          'last_name' => 'Sta.Maria',
          'email' => 'decastrorenz572@gmail.com',
          'password' => Hash::make('123456'),
          'status' => '1',
          'user_type' => 'admin',
          'phone' => '+639456827995',
          'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        $user->assignRole('encoder');

    }
}
