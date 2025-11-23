<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait TenantConnects
{
    /**
     * Eloquent will call initialize[TraitName] when a model using this trait
     * is instantiated. Use that to set a per-instance DB connection based on
     * session('current_tenant_context') when available.
     */
    public function initializeTenantConnects()
    {
        try {
            $context = session('current_tenant_context');
        } catch (\Exception $e) {
            $context = null;
        }

        if (empty($context) || empty($context['database'])) {
            return;
        }

        $db = $context['database'];
        $user = $context['username'] ?? null;
        $pass = $context['password'] ?? null;

        // stable connection key
        $key = 'tenant_' . md5($db . '|' . ($user ?? '') . '|' . ($pass ?? ''));

        if (! Config::has("database.connections.$key")) {
            $template = Config::get('database.connections.mysql') ?? Config::get('database.connections.' . Config::get('database.default'));
            if ($template) {
                $template['database'] = $db;
                if (! is_null($user)) {
                    $template['username'] = $user;
                }
                if (! is_null($pass)) {
                    $template['password'] = $pass;
                }
                Config::set("database.connections.$key", $template);
            }
        }

        // set this model instance's connection
        $this->setConnection($key);
    }

    /**
     * Convenience: return the configured connection key for a tenant context array.
     * Useful if you need to create queries manually.
     *
     * @param array $context
     * @return string|null
     */
    public static function connectionKeyForContext(array $context)
    {
        if (empty($context) || empty($context['database'])) {
            return null;
        }

        $db = $context['database'];
        $user = $context['username'] ?? null;
        $pass = $context['password'] ?? null;

        return 'tenant_' . md5($db . '|' . ($user ?? '') . '|' . ($pass ?? ''));
    }

    /**
     * Explicitly set the database context for this model instance.
     * Useful for manual tenant switching without relying on session.
     *
     * Usage:
     *   $voter = new Voter();
     *   $voter->setTenantContext(['database' => 'db1', 'username' => 'user1', 'password' => 'pass1']);
     *   $voters = $voter->where('id', '>', 100)->get();
     *
     * @param array $context Tenant context with keys: database, username, password
     * @return $this
     */
    public function setTenantContext(array $context)
    {
        if (empty($context) || empty($context['database'])) {
            return $this;
        }

        $db = $context['database'];
        $user = $context['username'] ?? null;
        $pass = $context['password'] ?? null;

        // Generate stable connection key
        $key = 'tenant_' . md5($db . '|' . ($user ?? '') . '|' . ($pass ?? ''));

        // Create connection if it doesn't exist
        if (! Config::has("database.connections.$key")) {
            $template = Config::get('database.connections.mysql') ?? Config::get('database.connections.' . Config::get('database.default'));
            if ($template) {
                $template['database'] = $db;
                if (! is_null($user)) {
                    $template['username'] = $user;
                }
                if (! is_null($pass)) {
                    $template['password'] = $pass;
                }
                Config::set("database.connections.$key", $template);
            }
        }

        // Set this model instance to use the connection
        $this->setConnection($key);

        return $this;
    }

    /**
     * Static helper to set context on a query without instantiation.
     * Useful for one-off queries on a specific tenant.
     *
     * Usage:
     *   $voters = Voter::onTenantContext(['database' => 'db1', ...])
     *       ->where('brgy', 'Barangay Name')
     *       ->get();
     *
     * @param array $context Tenant context with keys: database, username, password
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function onTenantContext(array $context)
    {
        $model = new static();
        return $model->setTenantContext($context)->newQuery();
    }
}
