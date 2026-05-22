<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\ClientRequest;
use App\Models\Crm\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $clients = Client::with('owner:id,name')
            ->withCount('deals')
            ->when($request->string('q')->toString(), fn ($q, $term) => $q
                ->where('name', 'ilike', "%{$term}%")
                ->orWhere('company', 'ilike', "%{$term}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Crm/Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Client::class);

        return Inertia::render('Crm/Clients/Form', ['client' => null]);
    }

    public function store(ClientRequest $request)
    {
        $this->authorize('create', Client::class);

        Client::create($request->validated() + [
            'owner_id' => $request->input('owner_id', $request->user()->id),
        ]);

        return redirect()->route('crm.clients.index')->with('success', 'Client created.');
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);

        return Inertia::render('Crm/Clients/Show', [
            'client'     => $client->load('owner:id,name'),
            'deals'      => $client->deals()->with('stage:id,name,color')->get(),
            'activities' => $client->activities()->with('user:id,name')->get(),
        ]);
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);

        return Inertia::render('Crm/Clients/Form', ['client' => $client]);
    }

    public function update(ClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return redirect()->route('crm.clients.index')->with('success', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        $client->delete();

        return back()->with('success', 'Client deleted.');
    }
}
