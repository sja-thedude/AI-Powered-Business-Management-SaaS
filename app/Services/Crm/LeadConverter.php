<?php

namespace App\Services\Crm;

use App\Models\Crm\Client;
use App\Models\Crm\Lead;
use Illuminate\Support\Facades\DB;

/**
 * Converts a lead into a client, copying contact details, carrying over the
 * activity timeline, and marking the lead converted — all atomically.
 */
class LeadConverter
{
    public function convert(Lead $lead): Client
    {
        return DB::transaction(function () use ($lead) {
            $client = Client::create([
                'owner_id' => $lead->owner_id,
                'name'     => $lead->name,
                'company'  => $lead->company,
                'email'    => $lead->email,
                'phone'    => $lead->phone,
                'status'   => 'active',
            ]);

            // Re-parent the lead's communication history onto the new client.
            $lead->activities()->update([
                'subject_type' => Client::class,
                'subject_id'   => $client->id,
            ]);

            $lead->update([
                'status'              => 'converted',
                'converted_client_id' => $client->id,
                'converted_at'        => now(),
            ]);

            return $client;
        });
    }
}
