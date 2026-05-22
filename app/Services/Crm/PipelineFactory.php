<?php

namespace App\Services\Crm;

use App\Models\Crm\Pipeline;

/**
 * Ensures a tenant always has a usable sales pipeline. The first time the
 * board is opened (or during seeding) it lazily creates a sensible default
 * with standard stages, so the Kanban is never empty.
 */
class PipelineFactory
{
    public const DEFAULT_STAGES = [
        ['name' => 'Lead In',     'color' => '#94a3b8', 'probability' => 10],
        ['name' => 'Qualified',   'color' => '#3563ff', 'probability' => 30],
        ['name' => 'Proposal',    'color' => '#a855f7', 'probability' => 55],
        ['name' => 'Negotiation', 'color' => '#f59e0b', 'probability' => 80],
        ['name' => 'Closing',     'color' => '#22c55e', 'probability' => 95],
    ];

    public function defaultPipeline(): Pipeline
    {
        $pipeline = Pipeline::where('is_default', true)->first()
            ?? Pipeline::firstOrCreate(
                ['name' => 'Sales Pipeline'],
                ['is_default' => true],
            );

        if ($pipeline->stages()->doesntExist()) {
            foreach (self::DEFAULT_STAGES as $i => $stage) {
                $pipeline->stages()->create($stage + ['position' => $i]);
            }
        }

        return $pipeline;
    }
}
