<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\LeadRequest;
use App\Jobs\AI\ScoreLeadJob;
use App\Models\Crm\Lead;
use App\Services\Crm\LeadConverter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

        $leads = Lead::with('owner:id,name')
            ->status($request->string('status')->toString() ?: null)
            ->search($request->string('q')->toString() ?: null)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Crm/Leads/Index', [
            'leads'   => $leads,
            'filters' => $request->only('status', 'q'),
            'stats'   => [
                'new'       => Lead::where('status', 'new')->count(),
                'qualified' => Lead::where('status', 'qualified')->count(),
                'converted' => Lead::where('status', 'converted')->count(),
            ],
        ]);
    }

    public function create()
    {
        $this->authorize('create', Lead::class);

        return Inertia::render('Crm/Leads/Form', ['lead' => null]);
    }

    public function store(LeadRequest $request)
    {
        $this->authorize('create', Lead::class);

        $lead = Lead::create($request->validated() + [
            'owner_id' => $request->input('owner_id', $request->user()->id),
        ]);

        // AI lead scoring runs out-of-band so the request stays snappy.
        ScoreLeadJob::dispatch($lead->id, $lead->tenant_id);

        return redirect()->route('crm.leads.index')->with('success', 'Lead created.');
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        return Inertia::render('Crm/Leads/Show', [
            'lead'       => $lead->load('owner:id,name', 'convertedClient:id,name'),
            'activities' => $lead->activities()->with('user:id,name')->get(),
        ]);
    }

    public function edit(Lead $lead)
    {
        $this->authorize('update', $lead);

        return Inertia::render('Crm/Leads/Form', ['lead' => $lead]);
    }

    public function update(LeadRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $lead->update($request->validated());

        return redirect()->route('crm.leads.index')->with('success', 'Lead updated.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return back()->with('success', 'Lead deleted.');
    }

    /** Convert a qualified lead into a client (and optionally an opening deal). */
    public function convert(Lead $lead, LeadConverter $converter)
    {
        $this->authorize('update', $lead);

        $client = $converter->convert($lead);

        return redirect()->route('crm.clients.show', $client)
            ->with('success', "Lead converted to client {$client->name}.");
    }
}
