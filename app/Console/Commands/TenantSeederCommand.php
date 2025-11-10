<?php

namespace App\Console\Commands;

use App\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use TenantTagSeeder;

class TenantSeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:seed 
                            {domain? : The tenant domain to seed (e.g., tenant1.example.com)} 
                            {--all : Seed all tenant databases}';

    /**
     * The console command description.
     */
    protected $description = 'Run database seeders for a specific tenant or for all tenants';

    public function handle()
    {
        $tenants = config('tenants');

        if ($this->option('all')) {
            $this->seedAllTenants($tenants);
            return;
        }

        $domain = $this->argument('domain');

        if (!$domain) {
            $this->error('Please provide a domain or use the --all flag.');
            return;
        }

        if (!isset($tenants[$domain])) {
            $this->error("Tenant for domain '{$domain}' not found in config/tenants.php.");
            return;
        }

        $this->seedTenant($domain, $tenants[$domain]);
    }

    protected function seedAllTenants(array $tenants)
    {
        foreach ($tenants as $domain => $tenant) {
            $this->seedTenant($domain, $tenant);
        }
    }

    protected function seedTenant($domain, $tenant)
    {
        $this->info("🔄 Seeding tenant: {$domain} ({$tenant['database']})");

        // Switch database connection dynamically
        Config::set('database.connections.mysql.database', $tenant['database']);
        Config::set('database.connections.mysql.username', $tenant['username']);
        Config::set('database.connections.mysql.password', $tenant['password']);

        DB::purge('mysql');
        DB::reconnect('mysql');

        // Call your usual seeders
        Artisan::call('db:seed', [
            // '--class' => 'TenantSeeder', // Change this to your main tenant seeder
            '--force' => true,
        ]);

        // Dynamic seeder of barangays per tenant
        if(isset(config("tenant_brgys")[$tenant['tenant_id']])) {
            $this->info("✅ seeding tenant barangays\n");
            foreach (config("tenant_brgys")[$tenant['tenant_id']] as $brgy) {
                $this->info("✅ seeding tenant barangay - {$brgy} \n");
                Tag::updateOrCreate(['name' => $brgy, 'parent_id' => 0], ['status'=>1, 'type'=>'brgy', 'custom_field' => '']);
            }
        }

        $this->info("✅ Tenant {$domain} seeded successfully.\n");
    }
}
