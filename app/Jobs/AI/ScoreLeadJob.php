<?php

namespace App\Jobs\AI;

use App\Models\Crm\Lead;
use App\Models\Tenant;
use App\Services\AI\AiManager;
use App\Support\TenantContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Computes an AI lead score asynchronously. Because queue workers run outside
 * a web request, the job re-establishes tenant context from the passed
 * tenant_id before touching any tenant-scoped model.
 */
class ScoreLeadJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $leadId,
        public int $tenantId,
    ) {
        $this->onQueue(config('ai.queue'));
    }

    public function handle(AiManager $ai, TenantContext $context): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (! $tenant) {
            return;
        }

        $context->run($tenant, function () use ($ai) {
            $lead = Lead::find($this->leadId);
            if (! $lead) {
                return;
            }

            $lead->forceFill([
                'score' => $ai->scoreLead([
                    'name'            => $lead->name,
                    'company'         => $lead->company,
                    'source'          => $lead->source,
                    'status'          => $lead->status,
                    'estimated_value' => $lead->estimated_value,
                ]),
            ])->saveQuietly();
        });
    }
}
