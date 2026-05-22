<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\ClientRequest;
use App\Http\Resources\Crm\ClientResource;
use App\Models\Crm\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $clients = Client::withCount('deals')
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return ClientResource::collection($clients);
    }

    public function store(ClientRequest $request)
    {
        $this->authorize('create', Client::class);

        $client = Client::create($request->validated() + [
            'owner_id' => $request->input('owner_id', $request->user()->id),
        ]);

        return $this->created(new ClientResource($client));
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);

        return new ClientResource($client->loadCount('deals'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return new ClientResource($client);
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        $client->delete();

        return $this->ok(null, 'Client deleted.');
    }
}
