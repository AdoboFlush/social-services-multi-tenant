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

        if (isset($tenants[$host])) {
            $tenant = $tenants[$host];

            Log::info("DB creds found for tenant: " . $tenant['tenant_id']);

            // Override the default connection settings
            Config::set('database.connections.mysql.database', $tenant['database']);
            Config::set('database.connections.mysql.username', $tenant['username']);
            Config::set('database.connections.mysql.password', $tenant['password']);

            // Reconnect to apply changes
            DB::purge('mysql');
            DB::reconnect('mysql');

            Cache::put('tenant_id', $tenant['tenant_id']);

        } else {
            Cache::put('tenant_id', "default");
        }

        return $next($request);
    }
}
