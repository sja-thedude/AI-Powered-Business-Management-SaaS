<?php

namespace App\Support;

/**
 * Central definition of the platform's permission catalog and the default
 * role → permission matrix. Both the seeder (global permissions) and the
 * TenantProvisioner (per-tenant roles) read from here, so there is exactly
 * one place that describes "what can be done" in NovaBiz AI.
 */
class Permissions
{
    /** Standard CRUD verbs granted per module. */
    public const VERBS = ['view', 'create', 'update', 'delete'];

    /** Cross-cutting permissions not tied to a single module. */
    public const PLATFORM = [
        'billing.manage',
        'users.manage',
        'roles.manage',
        'settings.manage',
        'audit.view',
        'reports.view',
        'ai.use',
    ];

    /** Every permission name in the system (module CRUD + platform). */
    public static function all(): array
    {
        $permissions = self::PLATFORM;

        foreach (array_keys(config('modules')) as $module) {
            foreach (self::VERBS as $verb) {
                $permissions[] = "{$module}.{$verb}";
            }
        }

        return array_values(array_unique($permissions));
    }

    /**
     * Default roles seeded into every new tenant. Owner is omitted here because
     * it is granted every ability via a Gate::before bypass.
     */
    public static function roleMatrix(): array
    {
        $allModules = array_keys(config('modules'));

        return [
            'Owner' => ['*'],

            'Admin' => array_merge(
                self::modulePermissions($allModules),
                ['users.manage', 'roles.manage', 'settings.manage', 'audit.view', 'reports.view', 'ai.use'],
            ),

            'Manager' => array_merge(
                self::modulePermissions($allModules, ['view', 'create', 'update']),
                ['reports.view', 'ai.use'],
            ),

            'Member' => array_merge(
                self::modulePermissions(['crm', 'projects', 'invoicing'], ['view', 'create', 'update']),
                ['ai.use'],
            ),
        ];
    }

    protected static function modulePermissions(array $modules, array $verbs = self::VERBS): array
    {
        $out = [];
        foreach ($modules as $module) {
            foreach ($verbs as $verb) {
                $out[] = "{$module}.{$verb}";
            }
        }

        return $out;
    }
}
