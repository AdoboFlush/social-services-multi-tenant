<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class TenantMigration extends Command
{
    protected $signature = 'tenant:migrate 
                            {domain? : The tenant domain to migrate} 
                            {--all : Run migrations for all tenants}
                            {--seed : Also run tenant:seed after migrating}';

    protected $description = 'Run migrations for one or all tenant databases.';

    public function handle()
    {
        $tenants = config('tenants');

        if ($this->option('all')) {
            foreach ($tenants as $domain => $tenant) {
                $this->migrateTenant($domain, $tenant);
            }
            return;
        }

        $domain = $this->argument('domain');

        if (!$domain) {
            $this->error('Please specify a domain or use the --all flag.');
            return;
        }

        if (!isset($tenants[$domain])) {
            $this->error("Tenant '{$domain}' not found in config/tenants.php.");
            return;
        }

        $this->migrateTenant($domain, $tenants[$domain]);
    }

    protected function migrateTenant($domain, $tenant)
    {
        $this->info("🚀 Migrating tenant: {$domain} ({$tenant['database']})");

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
            $this->call('tenant:seed', ['domain' => $domain]);
        }

        $this->info("✅ Tenant {$domain} migrated successfully.\n");
    }
}
