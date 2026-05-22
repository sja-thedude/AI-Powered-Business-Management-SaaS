<?php

namespace App\Support;

use App\Models\Tenant;
use Illuminate\Support\Collection;

/**
 * Reads the modular ERP registry (config/modules.php) and answers questions
 * the rest of the app asks: "what's in the nav for this tenant?", "is this
 * module unlocked on their plan?". New modules appear everywhere just by
 * adding a config entry.
 */
class Modules
{
    public static function all(): Collection
    {
        return collect(config('modules'))
            ->map(fn ($module, $key) => ['key' => $key] + $module)
            ->sortBy('order')
            ->values();
    }

    public static function get(string $key): ?array
    {
        $module = config("modules.{$key}");

        return $module ? ['key' => $key] + $module : null;
    }

    /**
     * Navigation entries for a tenant, each annotated with whether the plan
     * unlocks it (locked items render with an upgrade prompt instead of hiding,
     * which converts better than silently disappearing).
     */
    public static function navFor(Tenant $tenant): array
    {
        return static::all()
            ->map(fn ($module) => [
                'key'         => $module['key'],
                'name'        => $module['name'],
                'icon'        => $module['icon'],
                'route'       => $module['route'],
                'status'      => $module['status'],
                'locked'      => ! $tenant->canUseModule($module['key']),
                'min_plan'    => $module['min_plan'],
            ])
            ->all();
    }
}
