<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'company'         => $this->company,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'source'          => $this->source,
            'status'          => $this->status,
            'score'           => $this->score,
            'estimated_value' => (float) $this->estimated_value,
            'owner'           => $this->whenLoaded('owner', fn () => [
                'id'   => $this->owner?->id,
                'name' => $this->owner?->name,
            ]),
            'converted_at'    => $this->converted_at,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
