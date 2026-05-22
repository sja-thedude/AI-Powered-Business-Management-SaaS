<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\DealRequest;
use App\Http\Resources\Crm\DealResource;
use App\Models\Crm\Deal;
use Illuminate\Http\Request;

class DealController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Deal::class);

        $deals = Deal::with(['stage:id,name', 'client:id,name'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return DealResource::collection($deals);
    }

    public function store(DealRequest $request)
    {
        $this->authorize('create', Deal::class);

        $deal = Deal::create($request->validated() + [
            'owner_id' => $request->input('owner_id', $request->user()->id),
            'status'   => 'open',
        ]);

        return $this->created(new DealResource($deal));
    }

    public function show(Deal $deal)
    {
        $this->authorize('view', $deal);

        return new DealResource($deal->load('stage:id,name', 'client:id,name'));
    }

    public function update(DealRequest $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $deal->update($request->validated());

        return new DealResource($deal);
    }

    public function destroy(Deal $deal)
    {
        $this->authorize('delete', $deal);

        $deal->delete();

        return $this->ok(null, 'Deal deleted.');
    }
}
