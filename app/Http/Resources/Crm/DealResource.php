<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'value'               => (float) $this->value,
            'currency'            => $this->currency,
            'status'              => $this->status,
            'probability'        => $this->probability,
            'pipeline_id'         => $this->pipeline_id,
            'pipeline_stage_id'   => $this->pipeline_stage_id,
            'stage'               => $this->whenLoaded('stage', fn () => [
                'id'   => $this->stage?->id,
                'name' => $this->stage?->name,
            ]),
            'client'              => $this->whenLoaded('client', fn () => [
                'id'   => $this->client?->id,
                'name' => $this->client?->name,
            ]),
            'expected_close_date' => $this->expected_close_date,
            'closed_at'           => $this->closed_at,
            'created_at'          => $this->created_at,
        ];
    }
}
