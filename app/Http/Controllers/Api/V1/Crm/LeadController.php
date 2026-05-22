<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\LeadRequest;
use App\Http\Resources\Crm\LeadResource;
use App\Jobs\AI\ScoreLeadJob;
use App\Models\Crm\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

        $leads = Lead::with('owner:id,name')
            ->status($request->query('status'))
            ->search($request->query('q'))
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return LeadResource::collection($leads);
    }

    public function store(LeadRequest $request)
    {
        $this->authorize('create', Lead::class);

        $lead = Lead::create($request->validated() + [
            'owner_id' => $request->input('owner_id', $request->user()->id),
        ]);

        ScoreLeadJob::dispatch($lead->id, $lead->tenant_id);

        return $this->created(new LeadResource($lead));
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        return new LeadResource($lead->load('owner:id,name'));
    }

    public function update(LeadRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $lead->update($request->validated());

        return new LeadResource($lead);
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return $this->ok(null, 'Lead deleted.');
    }
}
