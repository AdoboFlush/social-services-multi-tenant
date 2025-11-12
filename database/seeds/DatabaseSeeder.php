<?php

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            UserSeeder::class,
            UtilitySeeder::class,
            AdminSeeder::class,
            CountriesTableSeeder::class,
            CurrencySeeder::class,
            WelcomeMessageSeeder::class,
            AdditionalPermissionSeeder::class,
            TenantTagSeeder::class,
		]);
    }
}
