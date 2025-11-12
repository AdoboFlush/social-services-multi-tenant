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

    }
}
