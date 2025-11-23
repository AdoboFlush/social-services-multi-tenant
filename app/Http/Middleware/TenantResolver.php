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
                Log::info("Tenant found for host {$host}: " . json_encode($tenant));

                // Override the default connection settings
                Config::set('database.connections.mysql.database', $tenant['database']);
                Config::set('database.connections.mysql.username', $tenant['username']);
                Config::set('database.connections.mysql.password', $tenant['password']);

                // Reconnect to apply changes
                DB::purge('mysql');
                DB::reconnect('mysql');
                
                $tenant_details = $tenant;
                $tenant_children = array_filter($tenants, function ($t) use ($tenant) {
                    return $t['parent_id'] == $tenant['tenant_id'];
                });

                // handle tenant switching context
                $this->handleSwitchTenantContext($request, $tenants);

                break;
            }
        }

        if (!$tenant_details) {
            Log::warning("No tenant found for host: " . $host);
        }

        // Store in request scope for this request lifecycle
        $request->attributes->set('tenant_details', $tenant_details);
        $request->attributes->set('tenant_children', $tenant_children);

        return $next($request);
    }

    private function handleSwitchTenantContext($request, $tenants): void
    {
        $current_tenant_context_id = $request->query('current_tenant_context_id');

        // If a specific tenant context is requested, find and set it
        if ($current_tenant_context_id !== null) {
            foreach ($tenants as $tenant) {
                if ($tenant['tenant_id'] == $current_tenant_context_id) {
                    session(['current_tenant_context' => $tenant]);
                    break; // Use break instead of return to allow session to flush
                }
            }
        }
    }
}
