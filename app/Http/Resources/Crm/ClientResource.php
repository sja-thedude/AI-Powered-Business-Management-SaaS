<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'company'    => $this->company,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'website'    => $this->website,
            'status'     => $this->status,
            'tags'       => $this->tags ?? [],
            'deals_count' => $this->whenCounted('deals'),
            'created_at' => $this->created_at,
        ];
    }
}
