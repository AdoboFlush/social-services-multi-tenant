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
                            {tenant_id? : The tenant ID to seed (e.g., 1, 2, 3)} 
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

        $tenantId = $this->argument('tenant_id');

        if (!$tenantId) {
            $this->error('Please provide a tenant ID or use the --all flag.');
            return;
        }

        $tenant = $this->findTenantById($tenants, $tenantId);

        if (!$tenant) {
            $this->error("Tenant with ID '{$tenantId}' not found in config/tenants.php.");
            return;
        }

        $this->seedTenant($tenant);
    }

    protected function seedAllTenants(array $tenants)
    {
        foreach ($tenants as $tenant) {
            $this->seedTenant($tenant);
        }
    }

    protected function findTenantById(array $tenants, $tenantId)
    {
        foreach ($tenants as $tenant) {
            if ($tenant['tenant_id'] == $tenantId) {
                return $tenant;
            }
        }
        return null;
    }

    protected function seedTenant($tenant)
    {
        $this->info("🔄 Seeding tenant: ID {$tenant['tenant_id']} ({$tenant['database']})");

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

        $this->info("✅ Tenant ID {$tenant['tenant_id']} seeded successfully.\n");
    }
}
