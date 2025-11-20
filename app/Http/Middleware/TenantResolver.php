<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantResolver
{
    public function handle($request, Closure $next)
    {
        $host = $request->getHost(); // e.g., tenant1.example.com
        $tenants = config('tenants');

        Log::info("Resolving tenant for host: " . $host);

        $tenant_children = [];
        $tenant_details = null;

        // Search through tenants to find one that has this host in its domains
        foreach ($tenants as $tenant) {
            if (isset($tenant['domains']) && in_array($host, $tenant['domains'])) {
                Log::info("Tenant found for host {$host}: " . $tenant['tenant_id']);

                // Override the default connection settings
                Config::set('database.connections.mysql.database', $tenant['database']);
                Config::set('database.connections.mysql.username', $tenant['username']);
                Config::set('database.connections.mysql.password', $tenant['password']);

                // Reconnect to apply changes
                DB::purge('mysql');
                DB::reconnect('mysql');
                
                $tenant_details = $tenant;
                $tenant_children = array_filter($tenants, function ($t) use ($tenant) {
                    return $t['parent_id'] === $tenant['tenant_id'];
                });
                
                break;
            }
        }

        if (!$tenant_details) {
            Log::warning("No tenant found for host: " . $host);
        }

        Cache::put('tenant_details', $tenant_details);
        Cache::put('tenant_children', $tenant_children);

        return $next($request);
    }
}
