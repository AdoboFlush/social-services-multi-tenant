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
    public static function connectionKeyForContext(array $context = null)
    {
        if (empty($context) || empty($context['database'])) {
            return null;
        }

        $db = $context['database'];
        $user = $context['username'] ?? null;
        $pass = $context['password'] ?? null;

        return 'tenant_' . md5($db . '|' . ($user ?? '') . '|' . ($pass ?? ''));
    }
}
