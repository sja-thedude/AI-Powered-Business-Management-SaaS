<?php

namespace App\Events\Crm;

use App\Models\Crm\Deal;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Broadcast when a deal card is dragged to a new stage/position. Listened to
 * on the tenant-scoped private CRM channel so every open board updates live.
 */
class DealMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public Deal $deal)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("tenant.{$this->deal->tenant_id}.crm");
    }

    public function broadcastWith(): array
    {
        return [
            'id'                => $this->deal->id,
            'pipeline_stage_id' => $this->deal->pipeline_stage_id,
            'position'          => $this->deal->position,
            'title'             => $this->deal->title,
            'value'             => $this->deal->value,
        ];
    }
}
