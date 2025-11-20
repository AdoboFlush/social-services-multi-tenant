<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class TenantMigration extends Command
{
    protected $signature = 'tenant:migrate 
                            {tenant_id? : The tenant ID to migrate (e.g., 1, 2, 3)} 
                            {--all : Run migrations for all tenants}
                            {--seed : Also run tenant:seed after migrating}';

    protected $description = 'Run migrations for one or all tenant databases.';

    public function handle()
    {
        $tenants = config('tenants');

        if ($this->option('all')) {
            foreach ($tenants as $tenant) {
                $this->migrateTenant($tenant);
            }
            return;
        }

        $tenantId = $this->argument('tenant_id');

        if (!$tenantId) {
            $this->error('Please specify a tenant ID or use the --all flag.');
            return;
        }

        $tenant = $this->findTenantById($tenants, $tenantId);

        if (!$tenant) {
            $this->error("Tenant with ID '{$tenantId}' not found in config/tenants.php.");
            return;
        }

        $this->migrateTenant($tenant);
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

    protected function migrateTenant($tenant)
    {
        $this->info("🚀 Migrating tenant: ID {$tenant['tenant_id']} ({$tenant['database']})");

        // Switch DB connection dynamically
        Config::set('database.connections.mysql.database', $tenant['database']);
        Config::set('database.connections.mysql.username', $tenant['username']);
        Config::set('database.connections.mysql.password', $tenant['password']);

        DB::purge('mysql');
        DB::reconnect('mysql');

        // Ensure the database exists (if your MySQL user has privileges)
        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$tenant['database']}`");
        } catch (\Exception $e) {
            $this->error("❌ Failed to create database: {$e->getMessage()}");
            return;
        }

        // Run migrations
        Artisan::call('migrate', [
            '--force' => true,
            '--path' => '/database/migrations', // optional, customize path if needed
        ]);

        $this->info(Artisan::output());

        if ($this->option('seed')) {
            $this->call('tenant:seed', ['tenant_id' => $tenant['tenant_id']]);
        }

        $this->info("✅ Tenant ID {$tenant['tenant_id']} migrated successfully.\n");
    }
}
